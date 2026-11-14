<?php
namespace App\Console\Commands;
use App\Services\LegacyPanelInspector;
use Illuminate\Console\Command;
class PanelHealth extends Command
{
    protected $signature = 'panel:health';
    protected $description = 'Diagnosa read-only panel lama tanpa mengubah database.';
    public function handle(LegacyPanelInspector $i): int
    {
        $d=$i->snapshot();
        $this->info('CyberBakti Legacy Panel Health');
        $this->table(['Item','Nilai'], collect($d['app'])->map(fn($v,$k)=>[$k,(string)$v])->values()->all());
        $this->table(['Database','Nilai'], [['Status',$d['database']['status']],['Nama',$d['database']['database']??'-']]);
        $this->table(['Ranking','Jumlah'], collect($d['ranking'])->map(fn($v,$k)=>[$k,$v===null?'N/A':(string)$v])->values()->all());
        $this->line('PK Event: tidak dipasang oleh patch ini.');
        return $d['database']['status']==='OK' ? self::SUCCESS : self::FAILURE;
    }
}
