<?php

namespace App\Console\Commands;

use App\Models\Player;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdatePvpKillsCommand extends Command
{
    protected $signature = 'pw:update-pvp';

    protected $description = 'Update khusus PVP Kills dari rank.fRank';

    public function handle(): int
    {
        try {
            $playerModel = new Player();

            $this->info('==========================================');
            $this->info(' CYBER BAKTI - UPDATE PVP KILLS');
            $this->info('==========================================');

            $this->line('Player Table : ' . $playerModel->getTable());
            $this->line('Database PVP : rank');
            $this->line('Table PVP    : fRank');
            $this->newLine();

            /*
             * Ambil jumlah PVP Kill langsung dari rank.fRank
             */
            $kills = DB::table('rank.fRank')
                ->select('Kid', DB::raw('COUNT(*) AS total_kills'))
                ->groupBy('Kid')
                ->get();

            $totalRows = DB::table('rank.fRank')->count();

            $this->line(
                'Total record rank.fRank : ' .
                number_format($totalRows)
            );

            $this->line(
                'Role dengan PVP Kill    : ' .
                number_format($kills->count())
            );

            /*
             * Reset HANYA pk_count.
             * Tidak menyentuh level, reputation, faction,
             * equipment, name, dll.
             */
            Player::query()->update([
                'pk_count' => 0,
            ]);

            $updated = 0;
            $notFound = 0;

            foreach ($kills as $kill) {

                $affected = Player::query()
                    ->where('id', $kill->Kid)
                    ->update([
                        'pk_count' => (int) $kill->total_kills,
                    ]);

                if ($affected > 0) {
                    $updated++;
                } else {
                    $notFound++;
                }
            }

            $this->newLine();
            $this->info('PVP KILLS BERHASIL DIPERBARUI');
            $this->line('Role diperbarui       : ' . number_format($updated));
            $this->line('Role tidak ditemukan  : ' . number_format($notFound));

            $this->newLine();
            $this->info('TOP 20 PVP KILLS');

            $top = Player::query()
                ->orderByDesc('pk_count')
                ->limit(20)
                ->get(['id', 'name', 'pk_count']);

            foreach ($top as $i => $player) {
                $this->line(
                    ($i + 1) .
                    '. [' . $player->id . '] ' .
                    $player->name .
                    ' = ' .
                    number_format((int) $player->pk_count) .
                    ' Kill'
                );
            }

            Log::info('pw:update-pvp selesai', [
                'rank_rows'  => $totalRows,
                'kill_roles' => $kills->count(),
                'updated'    => $updated,
                'not_found'  => $notFound,
            ]);

            return self::SUCCESS;

        } catch (\Throwable $e) {

            $this->error('GAGAL UPDATE PVP KILLS');
            $this->error($e->getMessage());

            Log::error('pw:update-pvp gagal', [
                'error' => $e->getMessage(),
                'file'  => $e->getFile(),
                'line'  => $e->getLine(),
            ]);

            return self::FAILURE;
        }
    }
}
