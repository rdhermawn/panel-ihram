<?php
namespace App\Console\Commands;
use App\Services\LegacyPanelInspector;
use Illuminate\Console\Command;
class GameRuntimePathsLegacy extends Command
{
    protected $signature='pw:game-runtime-paths-legacy';
    protected $description='Tampilkan path runtime game yang dapat ditemukan tanpa mengubah konfigurasi.';
    public function handle(LegacyPanelInspector $i): int
    {
        $d=$i->gameInfo();
        $rows=[['Server IP',(string)$d['server_ip']],['Server Version',(string)$d['server_version']]];
        foreach($d['paths'] as $k=>$v) $rows[]=[$k,$v];
        $this->table(['Item','Nilai'],$rows); return self::SUCCESS;
    }
}
