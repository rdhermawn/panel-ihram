@section('title', ' - Update Fitur')
<x-hrace009.layouts.admin>
    <x-slot name="header">
        <div class="flex items-center justify-between px-4 py-4 border-b lg:py-6 dark:border-primary-darker">
            <div>
                <h1 class="text-2xl font-semibold">Cyber Bakti - Update Fitur Aman</h1>
                <p class="mt-1 text-sm text-gray-500">Mode kompatibilitas database lama. Halaman ini tidak mengubah database.</p>
            </div>
        </div>
    </x-slot>

    <x-slot name="content">
        <div class="p-4 space-y-6">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-5">
                @foreach($stats as $label => $value)
                    <div class="p-4 bg-white rounded-lg shadow dark:bg-darker">
                        <div class="text-xs font-semibold uppercase text-gray-500">{{ $label }}</div>
                        <div class="mt-2 text-2xl font-bold">{{ number_format($value) }}</div>
                    </div>
                @endforeach
            </div>

            <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                <div class="p-5 bg-white rounded-lg shadow dark:bg-darker">
                    <h2 class="text-lg font-semibold">Ringkasan Coin / Donasi</h2>
                    <div class="mt-4 space-y-2 text-sm">
                        <div class="flex justify-between"><span>Coin donasi berhasil</span><strong>{{ number_format($donationCoin) }}</strong></div>
                        <div class="flex justify-between"><span>Nilai transaksi berhasil</span><strong>Rp {{ number_format($donationMoney, 0, ',', '.') }}</strong></div>
                        <div class="flex justify-between"><span>Referral bonus tercatat</span><strong>{{ number_format($referralBonus, 0, ',', '.') }}</strong></div>
                        <div class="flex justify-between"><span>Streamer bonus di iPaymu</span><strong>{{ number_format($streamerBonus, 0, ',', '.') }}</strong></div>
                        <div class="pt-2 mt-2 border-t flex justify-between text-base"><span>Peredaran dasar yang bisa dihitung dari schema lama</span><strong>{{ number_format($coinCirculation, 0, ',', '.') }}</strong></div>
                    </div>
                    <p class="mt-3 text-xs text-gray-500">Withdrawal streamer tidak dikurangi karena database lama tidak memiliki ledger withdrawal. Patch ini tidak membuat tabel baru.</p>
                </div>

                <div class="p-5 bg-white rounded-lg shadow dark:bg-darker">
                    <h2 class="text-lg font-semibold">Prinsip Patch</h2>
                    <ul class="mt-4 text-sm space-y-2 list-disc list-inside">
                        <li>Tidak menjalankan migration.</li>
                        <li>Tidak mengubah, rename, atau drop tabel/kolom.</li>
                        <li>Tidak menimpa .env dan data storage.</li>
                        <li>Panel lama tetap menjadi core utama.</li>
                        <li>Fitur V9 hanya di-port bila kompatibel dengan Laravel 8 + schema lama.</li>
                    </ul>
                </div>
            </div>

            <div class="p-5 bg-white rounded-lg shadow dark:bg-darker overflow-x-auto">
                <h2 class="text-lg font-semibold mb-4">Fitur yang kompatibel langsung</h2>
                <table class="min-w-full text-sm">
                    <thead><tr class="border-b text-left"><th class="py-2 pr-4">Fitur</th><th class="py-2 pr-4">Status</th><th class="py-2">Sumber data</th></tr></thead>
                    <tbody>
                    @foreach($features as $feature)
                        <tr class="border-b">
                            <td class="py-2 pr-4">{{ $feature['name'] }}</td>
                            <td class="py-2 pr-4"><span class="font-semibold {{ $feature['ready'] ? 'text-green-600' : 'text-red-600' }}">{{ $feature['ready'] ? 'READY' : 'BELUM' }}</span></td>
                            <td class="py-2">{{ $feature['uses'] }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-5 bg-white rounded-lg shadow dark:bg-darker overflow-x-auto">
                <h2 class="text-lg font-semibold mb-2">Fitur V9 yang sengaja tidak dipaksakan</h2>
                <p class="text-sm text-gray-500 mb-4">Modul berikut membutuhkan tabel V9. Karena permintaan update ini melarang perubahan struktur database, modul ditahan agar panel lama tidak error.</p>
                <table class="min-w-full text-sm">
                    <thead><tr class="border-b text-left"><th class="py-2 pr-4">Fitur</th><th class="py-2 pr-4">Tabel</th><th class="py-2">Status DB lama</th></tr></thead>
                    <tbody>
                    @foreach($blocked as $item)
                        <tr class="border-b">
                            <td class="py-2 pr-4">{{ $item['name'] }}</td>
                            <td class="py-2 pr-4"><code>{{ $item['table'] }}</code></td>
                            <td class="py-2"><span class="font-semibold {{ $item['exists'] ? 'text-green-600' : 'text-yellow-600' }}">{{ $item['exists'] ? 'SUDAH ADA' : 'DITAHAN - TABEL TIDAK ADA' }}</span></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </x-slot>
</x-hrace009.layouts.admin>
