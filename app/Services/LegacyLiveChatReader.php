<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

/**
 * Live chat reader untuk panel Laravel 8 lama.
 * Tidak membutuhkan tabel/migration baru. Nama karakter diambil dari pwp_players.
 */
class LegacyLiveChatReader
{
    public function read($configuredPath, $limit = 500)
    {
        $file = $this->resolveFile((string) $configuredPath);
        if ($file === null || !is_readable($file)) {
            return [];
        }

        $limit = max(20, min(2000, (int) $limit));
        $lines = $this->tail($file, $limit);
        $rows = [];

        foreach ($lines as $line) {
            $row = $this->parseLine($line);
            if ($row !== null) {
                $rows[] = $row;
            }
        }

        return $this->resolveNames($rows);
    }

    public function resolveFile($configuredPath)
    {
        $configuredPath = rtrim(trim((string) $configuredPath), '/');
        if ($configuredPath === '') {
            return null;
        }

        $candidates = [];
        if (preg_match('/\.chat$/i', $configuredPath)) {
            $candidates[] = $configuredPath;
        } else {
            $candidates[] = $configuredPath . '/world2.chat';
            foreach ((array) @glob($configuredPath . '/world*.chat') as $candidate) {
                $candidates[] = $candidate;
            }
        }

        // Dukungan bind-mount read-only untuk log di /root/pwserver/logs.
        if (strpos($configuredPath, '/root/pwserver/logs') === 0) {
            $suffix = ltrim(substr($configuredPath, strlen('/root/pwserver/logs')), '/');
            $mirror = '/var/log/pw-panel' . ($suffix !== '' ? '/' . $suffix : '');
            if (preg_match('/\.chat$/i', $mirror)) {
                $candidates[] = $mirror;
            } else {
                $candidates[] = rtrim($mirror, '/') . '/world2.chat';
                foreach ((array) @glob(rtrim($mirror, '/') . '/world*.chat') as $candidate) {
                    $candidates[] = $candidate;
                }
            }
        }

        $candidates = array_values(array_unique($candidates));
        usort($candidates, static function ($a, $b) {
            return ((int) @filemtime($b)) <=> ((int) @filemtime($a));
        });

        foreach ($candidates as $candidate) {
            if (is_file($candidate) && is_readable($candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    private function parseLine($line)
    {
        $line = trim(str_replace("\0", '', (string) $line));
        if ($line === '') {
            return null;
        }

        // Format legacy yang dipakai ChatController panel lama.
        $parts = preg_split('/\s+/', $line);
        if (is_array($parts) && count($parts) >= 10 && isset($parts[7], $parts[8], $parts[9])) {
            $date = trim(($parts[0] ?? '') . ' ' . ($parts[1] ?? ''));
            $type = isset($parts[6]) ? rtrim($parts[6], ':') : '';
            $uid = $this->valueAfterPrefix($parts[7]);
            $destOrChannel = $this->valueAfterPrefix($parts[8]);
            $messageToken = implode(' ', array_slice($parts, 9));
            $message = $this->decodeLegacyMessage($this->valueAfterPrefix($messageToken));

            $channelId = is_numeric($destOrChannel) ? (int) $destOrChannel : null;
            $destinationId = null;
            if (stripos($parts[8], 'fid') !== false) {
                $destinationId = is_numeric($destOrChannel) ? (int) $destOrChannel : null;
            }

            return $this->makeRow(
                $date,
                is_numeric($uid) ? (int) $uid : null,
                $channelId,
                $destinationId,
                $message,
                $type,
                $line
            );
        }

        // Fallback format key=value dari build server yang lebih baru/custom.
        $date = null;
        if (preg_match('/(\d{4}-\d{2}-\d{2}\s+\d{2}:\d{2}:\d{2})/', $line, $m)) {
            $date = $m[1];
        }

        $uid = $this->findInt($line, ['roleid', 'role_id', 'role', 'userid', 'user_id', 'src']);
        $channel = $this->findInt($line, ['channel', 'chl', 'ch']);
        $destination = $this->findInt($line, [
            'destination', 'destinationid', 'destination_id', 'dst', 'dstid', 'dst_id',
            'target', 'targetid', 'target_id', 'receiver', 'receiverid', 'receiver_id',
            'to_role', 'to_roleid', 'to_role_id', 'to'
        ], true);

        $message = null;
        if (preg_match('/(?:^|[\s:,;])(?:msg|message|content)\s*[:=]\s*(.+)$/i', $line, $m)) {
            $message = $this->decodeFlexibleMessage($m[1]);
        }
        if ($message === null || $message === '') {
            $message = $this->toUtf8($line);
        }

        return $this->makeRow($date ?: '—', $uid, $channel, $destination, $message, '', $line);
    }

    private function makeRow($date, $uid, $channel, $destination, $message, $type, $raw)
    {
        $time = '—';
        if (preg_match('/(\d{2}:\d{2}:\d{2})/', (string) $date, $m)) {
            $time = $m[1];
        }

        return [
            'row_color' => $this->rowColor($channel),
            'date' => $date ?: '—',
            'time' => $time,
            'type' => $type,
            'uid' => $uid,
            'character_name' => null,
            'channel_id' => $channel,
            'channel' => $this->channelName($channel, $destination),
            'dest_id' => $destination,
            'dest' => $destination ? (string) $destination : '—',
            'destination_name' => null,
            'msg' => $this->truncate($message, 2000),
            'raw' => $this->truncate($this->toUtf8($raw), 3000),
        ];
    }

    private function resolveNames(array $rows)
    {
        $ids = [];
        foreach ($rows as $row) {
            if (!empty($row['uid'])) {
                $ids[] = (int) $row['uid'];
            }
            if (!empty($row['dest_id']) && (int) $row['dest_id'] > 1024) {
                $ids[] = (int) $row['dest_id'];
            }
        }
        $ids = array_values(array_unique(array_filter($ids)));
        if (empty($ids)) {
            return $rows;
        }

        try {
            $names = DB::table('pwp_players')->whereIn('id', $ids)->pluck('name', 'id')->all();
        } catch (\Throwable $e) {
            $names = [];
        }

        foreach ($rows as &$row) {
            $uid = !empty($row['uid']) ? (int) $row['uid'] : null;
            $dest = !empty($row['dest_id']) ? (int) $row['dest_id'] : null;
            $row['character_name'] = $uid && isset($names[$uid]) ? $names[$uid] : null;
            $row['destination_name'] = $dest && isset($names[$dest]) ? $names[$dest] : null;
            if ($row['destination_name']) {
                $row['dest'] = $row['destination_name'] . ' (' . $dest . ')';
                $row['channel'] = 'Whisper / Private';
            }
        }
        unset($row);

        return $rows;
    }

    private function tail($file, $limit)
    {
        $handle = @fopen($file, 'rb');
        if (!$handle) {
            return [];
        }

        $buffer = '';
        $chunk = 8192;
        fseek($handle, 0, SEEK_END);
        $position = ftell($handle);
        while ($position > 0 && substr_count($buffer, "\n") <= $limit) {
            $read = min($chunk, $position);
            $position -= $read;
            fseek($handle, $position);
            $buffer = fread($handle, $read) . $buffer;
        }
        fclose($handle);

        $lines = preg_split('/\r\n|\n|\r/', trim($buffer));
        if (!is_array($lines)) {
            return [];
        }
        return array_slice($lines, -$limit);
    }

    private function valueAfterPrefix($token)
    {
        $token = trim((string) $token);
        $pos = strpos($token, ':');
        if ($pos === false) {
            $pos = strpos($token, '=');
        }
        return $pos === false ? $token : substr($token, $pos + 1);
    }

    private function decodeLegacyMessage($value)
    {
        $value = trim((string) $value);
        $decoded = base64_decode($value, true);
        if ($decoded !== false && $decoded !== '') {
            $converted = @iconv('UCS-2LE', 'UTF-8//IGNORE', $decoded);
            if (is_string($converted) && $converted !== '') {
                return $converted;
            }
        }
        return $this->decodeFlexibleMessage($value);
    }

    private function decodeFlexibleMessage($value)
    {
        $value = trim((string) $value);
        $decoded = base64_decode($value, true);
        if ($decoded !== false && $decoded !== '') {
            foreach (['UCS-2LE', 'UTF-16LE', 'GB18030'] as $encoding) {
                $converted = @iconv($encoding, 'UTF-8//IGNORE', $decoded);
                if (is_string($converted) && trim($converted) !== '') {
                    return trim($converted);
                }
            }
            if (preg_match('//u', $decoded)) {
                return $decoded;
            }
        }
        return $this->toUtf8($value);
    }

    private function toUtf8($value)
    {
        $value = (string) $value;
        if ($value === '' || preg_match('//u', $value)) {
            return $value;
        }
        foreach (['GB18030', 'GBK', 'BIG5'] as $encoding) {
            $converted = @iconv($encoding, 'UTF-8//IGNORE', $value);
            if (is_string($converted) && $converted !== '') {
                return $converted;
            }
        }
        return $value;
    }

    private function findInt($line, array $keys, $allowNegative = false)
    {
        $number = $allowNegative ? '-?\\d+' : '\\d+';
        foreach ($keys as $key) {
            if (preg_match('/(?:^|[\s:,;])' . preg_quote($key, '/') . '\s*[:=]\s*(' . $number . ')/i', $line, $m)) {
                return (int) $m[1];
            }
        }
        return null;
    }

    private function channelName($channel, $destination = null)
    {
        if ($destination !== null && (int) $destination > 0) {
            return 'Whisper / Private';
        }
        $names = [
            0 => 'Umum / Normal', 1 => 'Global', 2 => 'Grup / Party', 3 => 'Clan / Guild',
            4 => 'Channel 4', 5 => 'TOA / Notice', 6 => 'Channel 6', 7 => 'Trade',
            8 => 'Channel 8', 9 => 'System', 10 => 'Channel 10', 11 => 'Channel 11',
            12 => 'GM / Broadcast'
        ];
        if ($channel === null) {
            return 'Tidak diketahui';
        }
        return isset($names[(int) $channel]) ? $names[(int) $channel] : 'Channel ' . (int) $channel;
    }

    private function rowColor($channel)
    {
        $colors = [
            0 => 'dark:text-light', 1 => 'text-yellow-400', 2 => 'text-green-400',
            4 => 'text-pink-400', 7 => 'text-orange-400', 9 => 'text-red-400', 12 => 'text-red-400'
        ];
        return isset($colors[(int) $channel]) ? $colors[(int) $channel] : 'text-blue-400';
    }

    private function truncate($value, $length)
    {
        $value = (string) $value;
        if (function_exists('mb_substr')) {
            return mb_substr($value, 0, $length, 'UTF-8');
        }
        return substr($value, 0, $length);
    }
}
