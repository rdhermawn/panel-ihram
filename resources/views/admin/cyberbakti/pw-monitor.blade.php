@section('title', ' - PW Monitor')
<x-hrace009.layouts.admin>
<x-slot name="header"><div class="flex items-center justify-between px-4 py-4 border-b lg:py-6 dark:border-primary-darker"><h1 class="text-2xl font-semibold">Perfect World Monitor</h1><a class="px-3 py-2 text-sm text-white bg-primary rounded" href="{{ route('admin.pw.trades') }}">Player Trade Monitor</a></div></x-slot>
<x-slot name="content">
<div class="p-4 space-y-5">
<div class="p-4 bg-white rounded shadow dark:bg-darker"><b>Read-only monitor.</b> Tidak mengubah database, tidak menjalankan start/stop service, dan tidak menyentuh PK Event.</div>
<div class="grid gap-4 md:grid-cols-4">
@php($sys=$snapshot['system']??[])
<div class="p-4 bg-white rounded shadow dark:bg-darker"><div class="text-xs opacity-70">HOST</div><div class="font-semibold">{{ $sys['hostname']??'-' }}</div></div>
<div class="p-4 bg-white rounded shadow dark:bg-darker"><div class="text-xs opacity-70">LOAD</div><div class="font-semibold">{{ isset($sys['load']) ? implode(' / ', array_map(function($x){return number_format($x,2);},$sys['load'])) : '-' }}</div></div>
<div class="p-4 bg-white rounded shadow dark:bg-darker"><div class="text-xs opacity-70">PLAYER DB</div><div class="font-semibold">{{ number_format($snapshot['database']['pwp_players']??0) }}</div></div>
<div class="p-4 bg-white rounded shadow dark:bg-darker"><div class="text-xs opacity-70">TRADE LOG</div><div class="font-semibold">{{ ($tradeInfo['available']??false) ? (($tradeInfo['scanned_files']??0).' file') : 'Belum ditemukan' }}</div></div>
</div>
<div class="p-4 bg-white rounded shadow dark:bg-darker"><h2 class="mb-3 text-lg font-semibold">Process Perfect World</h2><div class="overflow-x-auto"><table class="w-full text-sm"><thead><tr class="text-left"><th class="p-2">Service</th><th>Status</th><th>Process</th><th>PID</th><th>CPU</th><th>Memory</th><th>Uptime</th></tr></thead><tbody>@foreach($snapshot['processes']??[] as $p)<tr class="border-t dark:border-primary-darker"><td class="p-2 font-semibold">{{ $p['name'] }}</td><td><span class="px-2 py-1 rounded {{ $p['running']?'bg-green-100 text-green-800':'bg-red-100 text-red-800' }}">{{ $p['running']?'ONLINE':'OFFLINE' }}</span></td><td>{{ $p['count'] }}</td><td>{{ implode(', ',array_slice($p['pids'],0,8)) ?: '-' }}</td><td>{{ number_format($p['cpu_percent'],1) }}%</td><td>{{ number_format(($p['memory_bytes']??0)/1048576,1) }} MB</td><td>{{ isset($p['uptime_seconds'])&&$p['uptime_seconds']!==null ? gmdate('H:i:s',$p['uptime_seconds']) : '-' }}</td></tr>@endforeach</tbody></table></div></div>
<div class="p-4 bg-white rounded shadow dark:bg-darker"><h2 class="mb-3 text-lg font-semibold">Port PW</h2><div class="grid gap-2 md:grid-cols-3">@foreach($snapshot['ports']??[] as $p)<div class="p-3 border rounded dark:border-primary-darker"><b>{{ $p['service'] }}</b> : {{ $p['port'] }}<div class="text-sm {{ $p['reachable']?'text-green-600':'text-red-600' }}">{{ $p['reachable']?'OPEN':'CLOSED' }}</div></div>@endforeach</div></div>
<div class="p-4 bg-white rounded shadow dark:bg-darker"><h2 class="mb-3 text-lg font-semibold">Log Health</h2>@forelse($snapshot['logs']??[] as $l)<div class="py-2 border-b dark:border-primary-darker"><code>{{ $l['path'] }}</code><div class="text-xs opacity-70">{{ $l['modified_at'] }} · {{ number_format(($l['size']??0)/1048576,2) }} MB · umur {{ number_format(($l['age_seconds']??0)/60,1) }} menit</div></div>@empty<div>Log belum ditemukan.</div>@endforelse</div>
<div class="p-4 bg-white rounded shadow dark:bg-darker"><h2 class="mb-2 text-lg font-semibold">Command</h2><pre class="text-sm">php artisan pw:server-monitor-legacy
php artisan pw:trade-monitor-legacy --limit=50</pre></div>
</div>
</x-slot></x-hrace009.layouts.admin>
