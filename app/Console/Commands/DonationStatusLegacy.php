<?php
namespace App\Console\Commands;
use App\Services\LegacyPanelInspector;
use Illuminate\Console\Command;
class DonationStatusLegacy extends Command
{
    protected $signature='donation:status-legacy';
    protected $description='Ringkasan read-only tabel donasi lama.';
    public function handle(LegacyPanelInspector $i): int
    {
        $d=$i->donationInfo();
        $rows=[]; foreach($d as $k=>$v) $rows[]=[$k,is_array($v)?json_encode($v,JSON_UNESCAPED_UNICODE):(string)$v];
        $this->table(['Item','Nilai'],$rows); return self::SUCCESS;
    }
}
