<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Services\LegacyPlayerTradeReader;
class PwTradeMonitorLegacy extends Command
{
    protected $signature='pw:trade-monitor-legacy {--limit=20} {--search=} {--min-gold=0}';
    protected $description='Read recent committed Perfect World player trades from world*.formatlog without DB changes';
    public function handle(LegacyPlayerTradeReader $r){$x=$r->recent((int)$this->option('limit'),$this->option('search'),(int)$this->option('min-gold'));if(!$x['available']){$this->warn('world*.formatlog belum ditemukan/readable.');return 1;}foreach($x['rows'] as $t){$this->line(sprintf('%s | %s(%d) -> %s(%d) | gold A:%d B:%d | item A:%d B:%d',$t['traded_at'],$t['role_a_name']??'?', $t['role_a_id'],$t['role_b_name']??'?', $t['role_b_id'],$t['money_a'],$t['money_b'],count($t['objects_a']),count($t['objects_b'])));}return 0;}
}
