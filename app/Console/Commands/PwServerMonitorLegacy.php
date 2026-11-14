<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Services\LegacyPwServerMonitor;
class PwServerMonitorLegacy extends Command
{
    protected $signature='pw:server-monitor-legacy';
    protected $description='Read-only Perfect World server/process/port monitor for legacy panel';
    public function handle(LegacyPwServerMonitor $m){$s=$m->snapshot();$this->line(json_encode($s,JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE));return 0;}
}
