<?php
namespace App\Console\Commands;
use App\Services\LegacyPanelInspector;
use Illuminate\Console\Command;
class SchedulerStatusLegacy extends Command
{
    protected $signature='panel:scheduler-status';
    protected $description='Cek konfigurasi scheduler/cron panel lama secara read-only.';
    public function handle(LegacyPanelInspector $i): int
    {
        $d=$i->schedulerInfo();
        $rows=[]; foreach($d['expected'] as $cmd=>$freq) $rows[]=[$cmd,$freq];
        $this->table(['Command','Frekuensi panel'], $rows);
        $this->line('Crontab root terbaca: '.($d['cron_readable']?'YA':'TIDAK'));
        $this->line('schedule:run terdeteksi: '.($d['cron_has_schedule_run']?'YA':'TIDAK'));
        return self::SUCCESS;
    }
}
