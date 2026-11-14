@section('title', ' - CyberBakti Tools')
<x-hrace009.layouts.admin>
<x-slot name="header"><div class="flex items-center justify-between px-4 py-4 border-b lg:py-6 dark:border-primary-darker"><h1 class="text-2xl font-semibold">CyberBakti Tools - Legacy Safe</h1></div></x-slot>
<x-slot name="content">
<div class="p-4 space-y-5">
<div class="p-4 bg-white rounded shadow dark:bg-darker"><b>Mode aman:</b> halaman ini read-only. Tidak ada migration, DROP/ALTER table, dan PK Event tidak dipasang.</div>
@foreach(['app'=>'Aplikasi','database'=>'Database','scheduler'=>'Scheduler / Cron','storage'=>'Storage','game'=>'Game Runtime','ranking'=>'Ranking','donation'=>'Donasi','discord'=>'Discord','features'=>'Fitur Aman'] as $key=>$title)
<div class="p-4 bg-white rounded shadow dark:bg-darker"><h2 class="mb-3 text-lg font-semibold">{{ $title }}</h2><pre class="text-xs whitespace-pre-wrap">{{ json_encode($data[$key] ?? [], JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) }}</pre></div>
@endforeach
<div class="p-4 bg-white rounded shadow dark:bg-darker"><h2 class="mb-2 text-lg font-semibold">Command tambahan</h2><pre class="text-sm">php artisan panel:health
php artisan panel:scheduler-status
php artisan pw:game-runtime-paths-legacy
php artisan pw:ranking-status
php artisan donation:status-legacy
php artisan panel:make-admin USER --dry-run</pre></div>
</div>
</x-slot>
</x-hrace009.layouts.admin>
