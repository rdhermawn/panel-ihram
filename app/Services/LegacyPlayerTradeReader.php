<?php
namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LegacyPlayerTradeReader
{
    const DEFAULT_TAIL_BYTES = 16777216; // 16 MB per log

    public function discoverFiles(): array
    {
        $paths = [];
        $configured = trim((string) config('pw-config.trade_log_path', ''));
        $chat = trim((string) config('pw-config.chat_log_path', ''));
        foreach ([$configured, $chat, '/home/logs', '/home/server/logs', '/home/gamed/logs', '/root/pwserver/logs'] as $base) {
            if ($base === '') continue;
            $base = rtrim($base, '/');
            if (is_file($base) && is_readable($base) && substr(strtolower($base), -10) === '.formatlog') {
                $paths[] = $base;
                continue;
            }
            if (!is_dir($base) || !is_readable($base)) continue;
            foreach ((array) glob($base.'/world*.formatlog') as $f) if (is_file($f) && is_readable($f)) $paths[] = $f;
            foreach ((array) glob($base.'/*/world*.formatlog') as $f) if (is_file($f) && is_readable($f)) $paths[] = $f;
        }
        $paths = array_values(array_unique($paths));
        usort($paths, function ($a, $b) { return (@filemtime($b) <=> @filemtime($a)) ?: strcmp($b, $a); });
        return array_slice($paths, 0, 8);
    }

    public function recent(int $limit = 200, ?string $search = null, int $minGold = 0): array
    {
        $limit = max(1, min(1000, $limit));
        $rows = [];
        $files = $this->discoverFiles();
        foreach ($files as $file) {
            foreach ($this->tailTradeLines($file, self::DEFAULT_TAIL_BYTES) as $entry) {
                $row = $this->parseCommittedTradeLine($entry['line']);
                if (!$row) continue;
                $row['source_file'] = basename($file);
                $row['source_path'] = $file;
                $row['source_offset'] = $entry['offset'];
                if (max((int)$row['money_a'], (int)$row['money_b']) < $minGold) continue;
                $rows[] = $row;
            }
        }
        usort($rows, function ($a, $b) { return strcmp((string)$b['traded_at'], (string)$a['traded_at']); });
        $rows = array_slice($rows, 0, 2000);
        $this->attachNames($rows);
        if ($search !== null && trim($search) !== '') {
            $q = mb_strtolower(trim($search));
            $rows = array_values(array_filter($rows, function ($r) use ($q) {
                $hay = mb_strtolower(implode(' ', [
                    $r['role_a_id'], $r['role_b_id'], $r['role_a_name'] ?? '', $r['role_b_name'] ?? '',
                    $r['source_file'] ?? '', json_encode($r['objects_a']), json_encode($r['objects_b'])
                ]));
                return mb_strpos($hay, $q) !== false;
            }));
        }
        return [
            'rows' => array_slice($rows, 0, $limit),
            'files' => $files,
            'scanned_files' => count($files),
            'available' => count($files) > 0,
        ];
    }

    public function parseCommittedTradeLine(string $line): ?array
    {
        $line = trim($line);
        if (strpos($line, 'formatlog:trade:') === false) return null;
        $pattern = '/^(?<time>\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}).*?formatlog:trade:roleidA=(?<a>\d+):roleidB=(?<b>\d+):moneyA=(?<ma>\d+):moneyB=(?<mb>\d+):objectsA=(?<oa>.*?):objectsB=(?<ob>.*)\s*$/';
        if (!preg_match($pattern, $line, $m)) return null;
        $a = (int)$m['a']; $b = (int)$m['b'];
        if ($a < 1 || $b < 1 || $a === $b) return null;
        return [
            'traded_at' => $m['time'], 'role_a_id' => $a, 'role_b_id' => $b,
            'money_a' => (int)$m['ma'], 'money_b' => (int)$m['mb'],
            'objects_a' => $this->parseObjects((string)$m['oa']),
            'objects_b' => $this->parseObjects((string)$m['ob']),
        ];
    }

    private function parseObjects(string $value): array
    {
        $items = [];
        foreach (explode(';', trim($value)) as $entry) {
            $entry = trim($entry); if ($entry === '') continue;
            $parts = array_map('trim', explode(',', $entry));
            if (count($parts) < 3 || !is_numeric($parts[0]) || !is_numeric($parts[2])) continue;
            $id=(int)$parts[0]; $qty=(int)$parts[2]; if ($id < 1 || $qty < 1) continue;
            $items[] = ['item_id'=>$id, 'slot'=>(isset($parts[1]) && is_numeric($parts[1])) ? (int)$parts[1] : null, 'quantity'=>$qty];
        }
        return $items;
    }

    private function tailTradeLines(string $file, int $maxBytes): array
    {
        $size = (int) (@filesize($file) ?: 0); if ($size < 1) return [];
        $start = max(0, $size - $maxBytes);
        $h = @fopen($file, 'rb'); if (!$h) return [];
        $rows=[];
        if ($start > 0) { @fseek($h, $start); fgets($h); }
        while (!feof($h)) {
            $offset=(int)ftell($h); $line=fgets($h); if ($line===false) break;
            if (strpos($line, 'formatlog:trade:') !== false) $rows[]=['offset'=>$offset,'line'=>$line];
        }
        fclose($h);
        return $rows;
    }

    private function attachNames(array &$rows): void
    {
        if (!$rows || !Schema::hasTable('pwp_players')) return;
        $ids=[]; foreach($rows as $r){$ids[]=(int)$r['role_a_id'];$ids[]=(int)$r['role_b_id'];}
        $ids=array_values(array_unique(array_filter($ids)));
        $names=[];
        foreach (array_chunk($ids, 500) as $chunk) {
            foreach (DB::table('pwp_players')->whereIn('id',$chunk)->select('id','name')->get() as $p) $names[(int)$p->id]=(string)$p->name;
        }
        foreach($rows as &$r){$r['role_a_name']=$names[(int)$r['role_a_id']]??null;$r['role_b_name']=$names[(int)$r['role_b_id']]??null;} unset($r);
    }
}
