<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LegacyPanelInspector
{
    public function snapshot(): array
    {
        return [
            'app' => $this->appInfo(),
            'database' => $this->databaseInfo(),
            'scheduler' => $this->schedulerInfo(),
            'storage' => $this->storageInfo(),
            'game' => $this->gameInfo(),
            'ranking' => $this->rankingInfo(),
            'donation' => $this->donationInfo(),
            'discord' => $this->discordInfo(),
            'features' => $this->featureInfo(),
        ];
    }

    public function appInfo(): array
    {
        return [
            'Laravel' => app()->version(),
            'PHP' => PHP_VERSION,
            'Environment' => (string) app()->environment(),
            'URL' => (string) config('app.url'),
            'Timezone' => (string) config('app.timezone'),
            'Debug' => config('app.debug') ? 'ON' : 'OFF',
        ];
    }

    public function databaseInfo(): array
    {
        try {
            DB::connection()->getPdo();
            $db = DB::connection()->getDatabaseName();
            $tables = ['users','point','auth','forbid','pwp_players','pwp_factions','pwp_territories','pwp_ipaymu','pwp_transfer','referral_logs'];
            $present = [];
            foreach ($tables as $table) {
                $present[$table] = Schema::hasTable($table) ? 'OK' : 'TIDAK ADA';
            }
            return ['status' => 'OK', 'database' => $db, 'tables' => $present];
        } catch (\Throwable $e) {
            return ['status' => 'ERROR', 'database' => '-', 'error' => $e->getMessage(), 'tables' => []];
        }
    }

    public function schedulerInfo(): array
    {
        $commands = [
            'pw:update-transfer' => 'setiap menit',
            'pw:update-players' => 'setiap jam',
            'pw:update-faction' => 'setiap jam',
            'pw:update-territories' => 'setiap jam',
            'donation:discord' => 'setiap menit',
        ];
        $cron = null;
        foreach (['/var/spool/cron/root','/var/spool/cron/crontabs/root'] as $path) {
            if (is_readable($path)) { $cron = (string) @file_get_contents($path); break; }
        }
        return [
            'expected' => $commands,
            'cron_readable' => $cron !== null,
            'cron_has_schedule_run' => $cron !== null && strpos($cron, 'schedule:run') !== false,
            'cron_preview' => $cron ? implode("\n", array_slice(array_filter(explode("\n", $cron)), 0, 20)) : '',
        ];
    }

    public function storageInfo(): array
    {
        $paths = [
            'storage' => storage_path(),
            'logs' => storage_path('logs'),
            'framework' => storage_path('framework'),
            'bootstrap/cache' => base_path('bootstrap/cache'),
            'public/storage' => public_path('storage'),
        ];
        $out = [];
        foreach ($paths as $name => $path) {
            $out[$name] = [
                'path' => $path,
                'exists' => file_exists($path),
                'writable' => is_writable($path),
            ];
        }
        return $out;
    }

    public function gameInfo(): array
    {
        $cfg = (array) config('pw-config');
        $serverIp = data_get($cfg, 'server_ip', data_get($cfg, 'server.ip', '-'));
        $serverVersion = data_get($cfg, 'server_version', '-');
        $paths = [
            'world2.chat' => ['/home/logs/world2.chat','/home/pwserver/logs/world2.chat','/home/gamed/logs/world2.chat'],
            'elements.data' => ['/home/gamed/config/elements.data','/home/pwserver/gamed/elements.data','/home/gamed/elements.data'],
        ];
        $resolved = [];
        foreach ($paths as $label => $candidates) {
            $found = null;
            foreach ($candidates as $candidate) { if (is_file($candidate)) { $found = $candidate; break; } }
            $resolved[$label] = $found ?: 'tidak ditemukan pada path umum';
        }
        return ['server_ip' => $serverIp, 'server_version' => $serverVersion, 'paths' => $resolved];
    }

    public function rankingInfo(): array
    {
        $map = [
            'players' => 'pwp_players',
            'factions' => 'pwp_factions',
            'territories' => 'pwp_territories',
        ];
        $out = [];
        foreach ($map as $key => $table) {
            try { $out[$key] = Schema::hasTable($table) ? DB::table($table)->count() : null; }
            catch (\Throwable $e) { $out[$key] = null; }
        }
        return $out;
    }

    public function donationInfo(): array
    {
        $result = ['table' => Schema::hasTable('pwp_ipaymu') ? 'OK' : 'TIDAK ADA'];
        if (!Schema::hasTable('pwp_ipaymu')) return $result;
        try {
            $result['total_rows'] = DB::table('pwp_ipaymu')->count();
            if (Schema::hasColumn('pwp_ipaymu','status')) {
                $result['status_counts'] = DB::table('pwp_ipaymu')->select('status', DB::raw('COUNT(*) total'))->groupBy('status')->pluck('total','status')->toArray();
            }
            if (Schema::hasColumn('pwp_ipaymu','money')) {
                $q = DB::table('pwp_ipaymu');
                if (Schema::hasColumn('pwp_ipaymu','status')) $q->whereIn('status',['PAID','paid','success','SUCCESS','1']);
                $result['paid_money_total'] = (float) $q->sum('money');
            }
        } catch (\Throwable $e) { $result['error'] = $e->getMessage(); }
        return $result;
    }

    public function discordInfo(): array
    {
        $candidates = [
            'services.discord.webhook', 'discord.webhook', 'pw-config.discord_webhook',
            'pw-config.discord.webhook', 'services.discord.donation_webhook'
        ];
        foreach ($candidates as $key) {
            $value = config($key);
            if (is_string($value) && trim($value) !== '') return ['configured' => true, 'source' => $key];
        }
        return ['configured' => false, 'source' => '-'];
    }

    public function featureInfo(): array
    {
        return [
            'Live Chat enhanced' => class_exists(\App\Services\LegacyLiveChatReader::class),
            'Ranking tables' => Schema::hasTable('pwp_players') && Schema::hasTable('pwp_factions'),
            'Donation iPaymu' => Schema::hasTable('pwp_ipaymu'),
            'Streamer columns' => Schema::hasTable('pwp_ipaymu') && Schema::hasColumn('pwp_ipaymu','streamer_id') && Schema::hasColumn('pwp_ipaymu','streamer_bonus'),
            'PK Event V3' => false,
            'Database migration by patch' => false,
        ];
    }
}
