<div class="grid gap-3 mb-4 md:grid-cols-3">
<a href="{{ route('admin.cyberbakti.tools') }}" class="block p-4 bg-white rounded shadow dark:bg-darker"><div class="font-semibold">CyberBakti Tools</div><div class="text-sm opacity-70">Health, cron, ranking, donation.</div></a>
@if(Route::has('admin.pw.monitor'))<a href="{{ route('admin.pw.monitor') }}" class="block p-4 bg-white rounded shadow dark:bg-darker"><div class="font-semibold">PW Server Monitor</div><div class="text-sm opacity-70">Process, port, log, resource server.</div></a>@endif
@if(Route::has('admin.pw.trades'))<a href="{{ route('admin.pw.trades') }}" class="block p-4 bg-white rounded shadow dark:bg-darker"><div class="font-semibold">Player Trade Monitor</div><div class="text-sm opacity-70">Pantau trade player dari formatlog tanpa tabel baru.</div></a>@endif
</div>
