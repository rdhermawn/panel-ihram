<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\LegacyPlayerTradeReader;
use App\Services\LegacyPwServerMonitor;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PwMonitorController extends Controller
{
    public function index(LegacyPwServerMonitor $monitor, LegacyPlayerTradeReader $trades)
    {
        $snapshot=$monitor->snapshot();
        $tradeInfo=$trades->recent(10);
        return view('admin.cyberbakti.pw-monitor',compact('snapshot','tradeInfo'));
    }
    public function trades(Request $request, LegacyPlayerTradeReader $reader)
    {
        $q=(string)$request->query('q','');$limit=(int)$request->query('limit',200);$minGold=max(0,(int)$request->query('min_gold',0));
        $result=$reader->recent($limit,$q,$minGold);
        return view('admin.cyberbakti.trades',['result'=>$result,'q'=>$q,'limit'=>$limit,'minGold'=>$minGold]);
    }
    public function export(Request $request, LegacyPlayerTradeReader $reader): StreamedResponse
    {
        $q=(string)$request->query('q','');$minGold=max(0,(int)$request->query('min_gold',0));$rows=$reader->recent(1000,$q,$minGold)['rows'];
        return response()->streamDownload(function()use($rows){$h=fopen('php://output','w');fputcsv($h,['time','role_a_id','role_a_name','money_a','items_a','role_b_id','role_b_name','money_b','items_b','source']);foreach($rows as $r){$fmt=function($items){return implode('; ',array_map(function($i){return $i['item_id'].' x'.$i['quantity'];},$items));};fputcsv($h,[$r['traded_at'],$r['role_a_id'],$r['role_a_name']??'',$r['money_a'],$fmt($r['objects_a']),$r['role_b_id'],$r['role_b_name']??'',$r['money_b'],$fmt($r['objects_b']),$r['source_file']??'']);}fclose($h);},'pw-trades-'.date('Ymd-His').'.csv',['Content-Type'=>'text/csv']);
    }
}
