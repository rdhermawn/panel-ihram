<?php
namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LegacyPwServerMonitor
{
    private $names = ['logservice','uniquenamed','authd','gamedbd','gdeliveryd','gacd','glinkd','gs'];

    public function snapshot(): array
    {
        return [
            'captured_at' => date('Y-m-d H:i:s'),
            'system' => $this->systemInfo(),
            'processes' => $this->processes(),
            'ports' => $this->ports(),
            'database' => $this->databaseStats(),
            'logs' => $this->logHealth(),
        ];
    }

    private function processes(): array
    {
        $out = $this->run('ps -eo pid=,etimes=,%cpu=,rss=,comm=,args= 2>/dev/null');
        $group=[];
        foreach($this->names as $n) $group[$n]=['name'=>$n,'running'=>false,'count'=>0,'pids'=>[],'cpu_percent'=>0.0,'memory_bytes'=>0,'uptime_seconds'=>null,'instances'=>[]];
        foreach(preg_split('/\R/', trim($out)) as $line){
            if(!preg_match('/^\s*(\d+)\s+(\d+)\s+([0-9.]+)\s+(\d+)\s+(\S+)\s+(.*)$/',$line,$m)) continue;
            $pid=(int)$m[1];$elapsed=(int)$m[2];$cpu=(float)$m[3];$rss=(int)$m[4]*1024;$comm=basename($m[5]);$args=$m[6];
            foreach($this->names as $name){
                $match = $name==='gs' ? ($comm==='gs' || preg_match('~(^|/)gs(?:\s|$)~',$args)) : ($comm===$name || preg_match('~(^|/)'.preg_quote($name,'~').'(?:\s|$)~',$args));
                if(!$match) continue;
                $g=&$group[$name];$g['running']=true;$g['count']++;$g['pids'][]=$pid;$g['cpu_percent']+= $cpu;$g['memory_bytes'] += $rss;$g['uptime_seconds']=$g['uptime_seconds']===null?$elapsed:max($g['uptime_seconds'],$elapsed);
                if(count($g['instances'])<12)$g['instances'][]=['pid'=>$pid,'uptime_seconds'=>$elapsed,'cpu_percent'=>$cpu,'memory_bytes'=>$rss,'command'=>$this->redactCommand($args)];
                unset($g); break;
            }
        }
        return array_values($group);
    }

    private function ports(): array
    {
        $configured = [
            ['service'=>'gdeliveryd','port'=>(int)config('pw-api.ports.gdeliveryd',29100)],
            ['service'=>'gamedbd','port'=>(int)config('pw-api.ports.gamedbd',29400)],
            ['service'=>'client','port'=>(int)config('pw-api.ports.client',29000)],
            ['service'=>'gacd','port'=>29712],
        ];
        foreach([10929,10930,10931,10932,10936,10939] as $p)$configured[]=['service'=>'gs','port'=>$p];
        $listen=$this->run('ss -lntp 2>/dev/null');
        foreach($configured as &$row){$p=(int)$row['port'];$row['listening']=$p>0 && preg_match('/[:.]'.preg_quote((string)$p,'/').'\s/', $listen);$row['reachable']=$this->tcp('127.0.0.1',$p,0.15);} unset($row);
        return $configured;
    }

    private function databaseStats(): array
    {
        $tables=['users','pwp_players','pwp_factions','pwp_territories','pwp_transfer','pwp_ipaymu'];$out=[];
        foreach($tables as $t){try{$out[$t]=Schema::hasTable($t)?DB::table($t)->count():null;}catch(\Throwable $e){$out[$t]=null;}}
        if(Schema::hasTable('pwp_ipaymu')){try{$out['ipaymu_pending']=DB::table('pwp_ipaymu')->where('status','pending')->count();}catch(\Throwable $e){}}
        return $out;
    }

    private function logHealth(): array
    {
        $bases=[trim((string)config('pw-config.chat_log_path','')),'/home/logs','/home/server/logs','/home/gamed/logs','/root/pwserver/logs'];
        $files=[];
        foreach(array_unique(array_filter($bases)) as $base){$base=rtrim($base,'/');if(!is_dir($base))continue;foreach(['world2.chat','world*.formatlog'] as $pat){foreach((array)glob($base.'/'.$pat) as $f){if(is_file($f))$files[$f]=['path'=>$f,'size'=>(int)@filesize($f),'modified_at'=>@filemtime($f)?date('Y-m-d H:i:s',@filemtime($f)):null,'age_seconds'=>@filemtime($f)?time()-@filemtime($f):null];}}}
        uasort($files,function($a,$b){return strcmp((string)$b['modified_at'],(string)$a['modified_at']);});return array_slice(array_values($files),0,20);
    }

    private function systemInfo(): array
    {
        $load=function_exists('sys_getloadavg')?sys_getloadavg():[];
        $mem=[];$text=@file_get_contents('/proc/meminfo');if($text){foreach(['MemTotal','MemAvailable','SwapTotal','SwapFree'] as $k){if(preg_match('/^'.$k.':\s+(\d+)/m',$text,$m))$mem[$k]=(int)$m[1]*1024;}}
        $diskTotal=@disk_total_space('/');$diskFree=@disk_free_space('/');
        return ['hostname'=>gethostname(),'php'=>PHP_VERSION,'load'=>$load,'memory'=>$mem,'disk_total'=>$diskTotal?:null,'disk_free'=>$diskFree?:null,'uptime_seconds'=>$this->readUptime()];
    }
    private function readUptime(){ $x=@file_get_contents('/proc/uptime'); return $x!==false?(int)floor((float)explode(' ',$x)[0]):null; }
    private function tcp($host,$port,$timeout){if($port<1)return false;$errno=0;$errstr='';$fp=@fsockopen($host,$port,$errno,$errstr,$timeout);if($fp){fclose($fp);return true;}return false;}
    private function run($cmd){ if(!function_exists('shell_exec')) return ''; $x=@shell_exec($cmd); return is_string($x)?$x:''; }
    private function redactCommand($s){ return preg_replace('/(--?(?:pass|password|token|secret|key)(?:=|\s+)\S+)/i','$1[redacted]',substr((string)$s,0,700)); }
}
