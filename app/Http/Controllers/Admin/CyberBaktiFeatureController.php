<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CyberBaktiFeatureController extends Controller
{
    public function index()
    {
        $tables = [
            'users','auth','forbid','point','pwp_players','pwp_factions','pwp_territories',
            'pwp_ipaymu','referral_logs','pwp_services','pwp_services_logs','pwp_shops',
            'pwp_vouchers','pwp_votes','pwp_paymentwall_log','pwp_bank_log','pwp_paypals',
        ];

        $present = [];
        foreach ($tables as $table) {
            $present[$table] = Schema::hasTable($table);
        }

        $stats = [
            'users' => $this->safeCount('users'),
            'players' => $this->safeCount('pwp_players'),
            'factions' => $this->safeCount('pwp_factions'),
            'territories' => $this->safeCount('pwp_territories'),
            'services' => $this->safeCount('pwp_services'),
        ];

        $donationCoin = 0;
        $donationMoney = 0;
        if (Schema::hasTable('pwp_ipaymu')) {
            $donationCoin = (int) DB::table('pwp_ipaymu')->where('status', 'berhasil')->sum('amount');
            $donationMoney = (float) DB::table('pwp_ipaymu')->where('status', 'berhasil')->sum('money');
        }

        $referralBonus = 0;
        if (Schema::hasTable('referral_logs') && Schema::hasColumn('referral_logs', 'bonus')) {
            $referralBonus = (float) DB::table('referral_logs')->sum('bonus');
        }

        $streamerBonus = 0;
        if (Schema::hasTable('pwp_ipaymu') && Schema::hasColumn('pwp_ipaymu', 'streamer_bonus')) {
            $streamerBonus = (float) DB::table('pwp_ipaymu')
                ->where('status', 'berhasil')
                ->whereNotNull('streamer_bonus')
                ->sum('streamer_bonus');
        }

        // Database lama tidak memiliki tabel withdrawal streamer. Jangan dikurangi secara asumsi.
        $coinCirculation = $donationCoin + $referralBonus;

        $features = [
            ['name' => 'Ranking Player', 'ready' => $present['pwp_players'], 'uses' => 'pwp_players'],
            ['name' => 'Ranking Faction', 'ready' => $present['pwp_factions'], 'uses' => 'pwp_factions'],
            ['name' => 'Territory Ranking', 'ready' => $present['pwp_territories'], 'uses' => 'pwp_territories'],
            ['name' => 'Top Spender / iPaymu', 'ready' => $present['pwp_ipaymu'], 'uses' => 'pwp_ipaymu'],
            ['name' => 'Streamer Referral Dasar', 'ready' => $present['pwp_ipaymu'] && $present['referral_logs'], 'uses' => 'pwp_ipaymu + referral_logs'],
            ['name' => 'Ingame Services Lama', 'ready' => $present['pwp_services'] && $present['pwp_services_logs'], 'uses' => 'pwp_services + pwp_services_logs'],
            ['name' => 'GM / Auth', 'ready' => $present['auth'], 'uses' => 'auth'],
            ['name' => 'Ban / Forbid', 'ready' => $present['forbid'], 'uses' => 'forbid'],
            ['name' => 'Online Account Check', 'ready' => $present['point'], 'uses' => 'point'],
            ['name' => 'Voucher', 'ready' => $present['pwp_vouchers'], 'uses' => 'pwp_vouchers'],
            ['name' => 'Vote / Arena', 'ready' => $present['pwp_votes'], 'uses' => 'pwp_votes'],
            ['name' => 'Paymentwall', 'ready' => $present['pwp_paymentwall_log'], 'uses' => 'pwp_paymentwall_log'],
        ];

        $blocked = [
            ['name' => 'Player Trade Tracker', 'table' => 'pwp_player_trades'],
            ['name' => 'Game Discord Events', 'table' => 'pwp_game_discord_events'],
            ['name' => 'Server Metrics History', 'table' => 'pwp_server_metrics'],
            ['name' => 'Streamer Coin Withdrawal Ledger', 'table' => 'pwp_streamer_coin_withdrawals'],
            ['name' => 'CMS Discord Delivery Queue', 'table' => 'pwp_cms_discord_deliveries'],
            ['name' => 'Double Drop Purchase/State', 'table' => 'pwp_double_drop_purchases'],
            ['name' => 'Game Item Drop Suppression', 'table' => 'pwp_game_item_drop_suppressions'],
        ];
        foreach ($blocked as &$item) {
            $item['exists'] = Schema::hasTable($item['table']);
        }
        unset($item);

        return view('admin.cyberbakti.features', compact(
            'present', 'stats', 'features', 'blocked', 'donationCoin', 'donationMoney',
            'referralBonus', 'streamerBonus', 'coinCirculation'
        ));
    }

    private function safeCount(string $table): int
    {
        if (!Schema::hasTable($table)) {
            return 0;
        }

        try {
            return (int) DB::table($table)->count();
        } catch (\Throwable $e) {
            return 0;
        }
    }
}
