<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class CyberBaktiFeatureAudit extends Command
{
    protected $signature = 'panel:feature-audit';
    protected $description = 'Audit kompatibilitas fitur tambahan terhadap database panel lama tanpa mengubah data.';

    public function handle(): int
    {
        $groups = [
            'Core PW lama' => ['users','auth','forbid','point'],
            'Ranking' => ['pwp_players','pwp_factions','pwp_territories'],
            'Donation/Streamer dasar' => ['pwp_ipaymu','referral_logs'],
            'Service' => ['pwp_services','pwp_services_logs'],
            'V9: Trade tracker (butuh schema baru)' => ['pwp_player_trades','pwp_player_trade_items'],
            'V9: Discord game events (butuh schema baru)' => ['pwp_game_discord_events'],
            'V9: Server metrics (butuh schema baru)' => ['pwp_server_metrics'],
            'V9: Streamer withdrawal (butuh schema baru)' => ['pwp_streamer_coin_withdrawals'],
        ];

        $rows = [];
        foreach ($groups as $feature => $tables) {
            $missing = [];
            foreach ($tables as $table) {
                if (!Schema::hasTable($table)) {
                    $missing[] = $table;
                }
            }
            $rows[] = [$feature, empty($missing) ? 'READY' : 'BELUM', empty($missing) ? '-' : implode(', ', $missing)];
        }

        $this->table(['Fitur', 'Status', 'Tabel yang belum ada'], $rows);
        $this->newLine();
        $this->info('Audit hanya membaca schema. Tidak ada database yang diubah.');
        return 0;
    }
}
