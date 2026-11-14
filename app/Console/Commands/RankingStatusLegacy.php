<?php
namespace App\Console\Commands;
use App\Services\LegacyPanelInspector;
use Illuminate\Console\Command;
class RankingStatusLegacy extends Command
{
    protected $signature='pw:ranking-status';
    protected $description='Cek jumlah snapshot ranking tanpa mengubah data.';
    public function handle(LegacyPanelInspector $i): int
    {
        $d=$i->rankingInfo(); $rows=[]; foreach($d as $k=>$v)$rows[]=[$k,$v===null?'N/A':(string)$v];
        $this->table(['Ranking','Jumlah'],$rows); return self::SUCCESS;
    }
}
