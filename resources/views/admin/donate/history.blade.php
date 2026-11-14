@section('title', ' - ' . __('donate.title'))
<x-hrace009.layouts.admin>
    <x-slot name="header">
        <div class="flex items-center justify-between px-4 py-4 border-b lg:py-6 dark:border-primary-darker">
            <h1 class="text-2xl font-semibold">{{ __('donate.history.title') }}</h1>
        </div>
    </x-slot>
    <x-slot name="content">
        <div class="mx-6 my-6">
            <!-- Filter Section -->
            <div class="bg-white dark:bg-darker rounded-lg shadow-md p-6 mb-6">
                <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-200">Filter Laporan</h3>
                <form method="GET" action="{{ route('admin.donate.history') }}" class="flex flex-wrap gap-4 items-end">
                    <div class="flex-1 min-w-48">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Jenis Filter</label>
                        <select name="filter" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-black dark:bg-gray-800 dark:text-white">
                            <option value="monthly" {{ $currentFilter == 'monthly' ? 'selected' : '' }}>Bulanan</option>
                            <option value="yearly" {{ $currentFilter == 'yearly' ? 'selected' : '' }}>Tahunan</option>
                            <option value="all" {{ $currentFilter == 'all' ? 'selected' : '' }}>Semua Data</option>
                        </select>
                    </div>

                    <div class="flex-1 min-w-32" id="monthFilter" style="{{ $currentFilter == 'yearly' || $currentFilter == 'all' ? 'display: none;' : '' }}">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Bulan</label>
                        <select name="month" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-black dark:bg-gray-800 dark:text-white">
                            @for($i = 1; $i <= 12; $i++)
                                <option value="{{ sprintf('%02d', $i) }}" {{ $currentMonth == sprintf('%02d', $i) ? 'selected' : '' }}>
                                    {{ DateTime::createFromFormat('!m', $i)->format('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <div class="flex-1 min-w-32">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tahun</label>
                        <select name="year" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-black dark:bg-gray-800 dark:text-white">
                            @for($year = date('Y'); $year >= 2020; $year--)
                                <option value="{{ $year }}" {{ $currentYear == $year ? 'selected' : '' }}>{{ $year }}</option>
                            @endfor
                        </select>
                    </div>

                    <div>
                        <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md transition duration-200">
                            Filter
                        </button>
                    </div>
                </form>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <div class="bg-gradient-to-r from-gray-400 to-gray-500 rounded-lg shadow-md p-6 text-white opacity-50">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-100 text-sm font-medium">Total Bank Transfer</p>
                            <p class="text-2xl font-bold">Tidak Tersedia</p>
                            <p class="text-gray-100 text-xs">Fitur tidak aktif</p>
                        </div>
                        <div class="bg-gray-400 bg-opacity-30 rounded-full p-3">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4zM18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg shadow-md p-6 text-gray-600">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-white-500 text-sm font-medium">Total iPaymu</p>
                            <p class="text-2xl font-bold text-gray-700">Rp {{ number_format($ipaymuTotal, 0, ',', '.') }}</p>
                            <p class="text-gray-500 text-xs">{{ $ipaymuCount }} transaksi</p>
                        </div>
                        <div class="bg-green-400 bg-opacity-30 rounded-full p-3">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"></path>
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg shadow-md p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-purple-100 text-sm font-medium">Total Keseluruhan</p>
                            <p class="text-2xl font-bold">Rp {{ number_format($ipaymuTotal, 0, ',', '.') }}</p>
                            <p class="text-purple-100 text-xs">{{ $ipaymuCount }} transaksi</p>
                        </div>
                        <div class="bg-purple-400 bg-opacity-30 rounded-full p-3">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3zm11.707 4.707a1 1 0 00-1.414-1.414L10 9.586 8.707 8.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-lg shadow-md p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-orange-100 text-sm font-medium">Rata-rata per Transaksi</p>
                            <p class="text-2xl font-bold">Rp {{ $ipaymuCount > 0 ? number_format($ipaymuTotal / $ipaymuCount, 0, ',', '.') : '0' }}</p>
                            <p class="text-orange-100 text-xs">Per transaksi</p>
                        </div>
                        <div class="bg-orange-400 bg-opacity-30 rounded-full p-3">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chart Section -->
            <div class="bg-white dark:bg-darker rounded-lg shadow-md p-6 mb-6">
                <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-200">Grafik Pendapatan iPaymu 12 Bulan Terakhir</h3>
                <div class="h-64">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>

            <!-- Top Donors Section -->
            <div class="bg-white dark:bg-primary shadow-md rounded border border-gray-300 dark:border-primary-light mb-6">
                <div class="py-4 px-6 border-b border-gray-200 dark:border-primary-light">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-light">
                        Top 10 Donatur
                        @if($currentFilter == 'monthly')
                            Bulan {{ DateTime::createFromFormat('!m', $currentMonth)->format('F') }} {{ $currentYear }}
                        @elseif($currentFilter == 'yearly')
                            Tahun {{ $currentYear }}
                        @else
                            Sepanjang Masa
                        @endif
                    </h3>
                </div>

                @if($topDonors->count() > 0)
                    <div class="p-6">
                        <!-- Top 3 Donors - Special Cards -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                            @foreach($topDonors->take(3) as $index => $donor)
                                <div class="relative">
                                    @if($index == 0)
                                        <div class="bg-gradient-to-br from-yellow-400 to-yellow-600 rounded-lg p-6 text-white shadow-lg transform hover:scale-105 transition-transform duration-200">
                                            <div class="absolute -top-2 -right-2 bg-yellow-300 text-yellow-800 rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold">
                                                ??
                                            </div>
                                    @elseif($index == 1)
                                        <div class="bg-gradient-to-br from-gray-400 to-gray-600 rounded-lg p-6 text-white shadow-lg transform hover:scale-105 transition-transform duration-200">
                                            <div class="absolute -top-2 -right-2 bg-gray-300 text-gray-800 rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold">
                                                ??
                                            </div>
                                    @else
                                        <div class="bg-gradient-to-br from-orange-400 to-orange-600 rounded-lg p-6 text-white shadow-lg transform hover:scale-105 transition-transform duration-200">
                                            <div class="absolute -top-2 -right-2 bg-orange-300 text-orange-800 rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold">
                                                ??
                                            </div>
                                    @endif
                                        <div class="text-center">
                                            <div class="text-sm opacity-90 mb-1">#{!! $index + 1 !!}</div>
                                            <div class="font-bold text-lg mb-2 truncate">{{ $donor->name }}</div>
                                            <div class="text-2xl font-bold mb-1">Rp {{ number_format($donor->total_donation, 0, ',', '.') }}</div>
                                            <div class="text-sm opacity-90">{{ $donor->total_transactions }} transaksi</div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Remaining Donors - Table Format -->
                        @if($topDonors->count() > 3)
                            <table class="w-full table-auto">
                                <thead>
                                    <tr class="bg-gray-200 dark:bg-primary dark:text-light text-gray-600 uppercase text-xs leading-normal">
                                        <th class="py-3 px-6 text-left">Rank</th>
                                        <th class="py-3 px-6 text-left">User</th>
                                        <th class="py-3 px-6 text-left">Total Donasi</th>
                                        <th class="py-3 px-6 text-left">Transaksi</th>
                                        <th class="py-3 px-6 text-left">Rata-rata</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-600 text-xs dark:text-light">
                                    @foreach($topDonors->skip(3) as $index => $donor)
                                        <tr class="border-b border-gray-200 bg-gray-50 hover:bg-gray-100 dark:border-primary dark:bg-darker dark:hover:bg-primary-dark">
                                            <td class="py-3 px-6 text-left font-medium">
                                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 text-blue-800 text-sm font-bold">
                                                    {{ $index + 4 }}
                                                </span>
                                            </td>
                                            <td class="py-3 px-6 text-left">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-8 w-8">
                                                        <div class="h-8 w-8 rounded-full bg-gradient-to-r from-blue-400 to-purple-500 flex items-center justify-center text-white text-sm font-bold">
                                                            {{ strtoupper(substr($donor->name, 0, 1)) }}
                                                        </div>
                                                    </div>
                                                    <div class="ml-3">
                                                        <div class="text-sm font-medium text-gray-900 dark:text-light">{{ $donor->name }}</div>
                                                        <div class="text-sm text-gray-500 dark:text-gray-400">ID: {{ $donor->ID }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-3 px-6 text-left font-semibold text-green-600">
                                                Rp {{ number_format($donor->total_donation, 0, ',', '.') }}
                                            </td>
                                            <td class="py-3 px-6 text-left">
                                                {{ $donor->total_transactions }}
                                            </td>
                                            <td class="py-3 px-6 text-left">
                                                Rp {{ number_format($donor->total_donation / $donor->total_transactions, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-light">Belum ada donatur</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Belum ada data donatur untuk periode ini.</p>
                    </div>
                @endif
            </div>

            <!-- iPaymu Transactions -->
<div class="bg-white dark:bg-primary shadow-md rounded border border-gray-300 dark:border-primary-light">

    <div class="py-4 px-6 border-b border-gray-200 dark:border-primary-light">

        <h3 class="text-lg font-semibold text-gray-800 dark:text-light">
            Transaksi iPaymu
        </h3>

        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
            {{ $ipaymuCount }} transaksi
        </p>

    </div>


    @if($ipaymuLogs->count() > 0)

        <div class="overflow-x-auto">

            <table class="w-full table-auto">

                <thead>

                    <tr class="bg-gray-200 dark:bg-primary text-gray-600 dark:text-light uppercase text-xs">

                        <th class="py-3 px-6 text-left">
                            Kode Transaksi
                        </th>

                        <th class="py-3 px-6 text-left">
                            User
                        </th>

                        <th class="py-3 px-6 text-left">
                            {{ config('pw-config.currency_name') }}
                        </th>

                        <th class="py-3 px-6 text-left">
                            Harga
                        </th>

                        <th class="py-3 px-6 text-left">
                            Status
                        </th>

                        <th class="py-3 px-6 text-left">
                            Tanggal
                        </th>

                    </tr>

                </thead>


                <tbody class="text-gray-600 dark:text-light text-xs">

                    @foreach($ipaymuLogs as $ipaymu)

                        <tr class="border-b border-gray-200 dark:border-primary bg-gray-50 dark:bg-darker hover:bg-gray-100 dark:hover:bg-primary-dark">

                            {{-- ================================================= --}}
                            {{-- KODE TRANSAKSI --}}
                            {{-- ================================================= --}}

                            <td class="py-4 px-6">

                                <button
                                    type="button"
                                    class="invoice-trigger font-mono font-bold text-blue-600 hover:text-blue-800 hover:underline cursor-pointer"
                                    data-trx="{{ $ipaymu->trx_id }}"
                                    data-user="{{ $ipaymu->user->name ?? 'N/A' }}"
                                    data-user-id="{{ $ipaymu->user->ID ?? '' }}"
                                    data-amount="{{ $ipaymu->amount }}"
                                    data-money="{{ $ipaymu->money }}"
                                    data-status="{{ $ipaymu->status }}"
                                    data-date="{{ $ipaymu->created_at ? $ipaymu->created_at->format('d F Y') : '-' }}"
                                    data-time="{{ $ipaymu->created_at ? $ipaymu->created_at->format('H:i:s') : '-' }}"
                                >

                                    {{ $ipaymu->trx_id }}

                                </button>

                            </td>


                            {{-- USER --}}

                            <td class="py-4 px-6">

                                <div class="flex items-center">

                                    <div class="h-8 w-8 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold">

                                        {{ strtoupper(substr($ipaymu->user->name ?? 'N', 0, 1)) }}

                                    </div>

                                    <div class="ml-3">

                                        <div class="font-semibold text-gray-900 dark:text-light">

                                            {{ $ipaymu->user->name ?? 'N/A' }}

                                        </div>

                                    </div>

                                </div>

                            </td>


                            {{-- COIN --}}

                            <td class="py-4 px-6">

                                <span class="font-bold text-blue-600">

                                    {{ number_format($ipaymu->amount, 0, ',', '.') }}

                                </span>

                            </td>


                            {{-- HARGA --}}

                            <td class="py-4 px-6">

                                <span class="font-bold text-green-600">

                                    Rp {{ number_format($ipaymu->money, 0, ',', '.') }}

                                </span>

                            </td>


                            {{-- STATUS --}}

                            <td class="py-4 px-6">

                                @if($ipaymu->status == 'berhasil')

                                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-green-100 text-green-700 font-semibold">

                                        <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>

                                        Berhasil

                                    </span>

                                @elseif($ipaymu->status == 'pending')

                                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-yellow-100 text-yellow-700 font-semibold">

                                        <span class="w-2 h-2 bg-yellow-500 rounded-full mr-2"></span>

                                        Pending

                                    </span>

                                @else

                                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-red-100 text-red-700 font-semibold">

                                        {{ ucfirst($ipaymu->status ?? 'Unknown') }}

                                    </span>

                                @endif

                            </td>


                            {{-- TANGGAL --}}

                            <td class="py-4 px-6">

                                @if($ipaymu->created_at)

                                    <div class="font-medium">

                                        {{ $ipaymu->created_at->format('d/m/Y') }}

                                    </div>

                                    <div class="text-gray-400">

                                        {{ $ipaymu->created_at->format('H:i') }}

                                    </div>

                                @else

                                    -

                                @endif

                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>


        {{-- PAGINATION --}}

        <div class="p-4 border-t border-gray-200 dark:border-primary-light">

            {{ $ipaymuLogs->appends(request()->query())->links() }}

        </div>


    @else

        <div class="text-center py-12">

            <svg
                class="mx-auto h-14 w-14 text-gray-400"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
            >

                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a2 2 0 01.293.707V19a2 2 0 01-2 2z"
                />

            </svg>

            <h3 class="mt-3 text-sm font-medium text-gray-900 dark:text-light">

                Tidak ada data

            </h3>

            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">

                Belum ada transaksi iPaymu untuk periode ini.

            </p>

        </div>

    @endif

</div>


{{-- ========================================================= --}}
{{-- INVOICE MODAL --}}
{{-- ========================================================= --}}

<div
    id="invoiceModal"
    class="hidden fixed inset-0 z-[99999]"
>

    {{-- BACKDROP --}}

    <div
        id="invoiceBackdrop"
        class="absolute inset-0 bg-black/70"
    ></div>


    {{-- MODAL --}}

    <div class="relative z-10 flex min-h-screen items-center justify-center p-4">

        <div
            id="invoiceBox"
            class="bg-white w-full max-w-3xl rounded-2xl shadow-2xl overflow-hidden"
        >

            {{-- HEADER --}}

            <div class="px-8 py-6 border-b border-gray-200">

                <div class="flex items-start justify-between">

                    <div>

                        <div class="text-2xl font-black text-gray-900">
                            PERFECT WORLD
                        </div>

                        <div class="text-xl font-bold text-blue-600">
                            ETERNA
                        </div>

                        <div class="text-sm text-gray-500 mt-1">
                            Payment Invoice
                        </div>

                    </div>


                    <button
                        type="button"
                        id="invoiceClose"
                        class="w-9 h-9 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-600 flex items-center justify-center"
                    >

                        <svg
                            class="w-5 h-5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"
                            />

                        </svg>

                    </button>

                </div>

            </div>


            {{-- INVOICE INFO --}}

            <div class="px-8 py-5 bg-gray-50 border-b border-gray-200">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <div>

                        <div class="text-xs uppercase font-bold text-gray-400">
                            Invoice
                        </div>

                        <div
                            id="invoiceTrx"
                            class="font-mono font-bold text-lg text-gray-900 mt-1 break-all"
                        >
                            -
                        </div>

                    </div>


                    <div class="md:text-right">

                        <div class="text-xs uppercase font-bold text-gray-400">
                            Tanggal
                        </div>

                        <div
                            id="invoiceDate"
                            class="font-semibold text-gray-900 mt-1"
                        >
                            -
                        </div>

                        <div
                            id="invoiceTime"
                            class="text-sm text-gray-500"
                        >
                            -
                        </div>

                    </div>

                </div>

            </div>


            {{-- CUSTOMER --}}

            <div class="px-8 py-6">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <div>

                        <div class="text-xs uppercase font-bold text-gray-400 mb-2">
                            Customer
                        </div>

                        <div
                            id="invoiceUser"
                            class="text-lg font-bold text-gray-900"
                        >
                            -
                        </div>

                        <div
                            id="invoiceUserId"
                            class="text-sm text-gray-500 mt-1"
                        >
                        </div>

                    </div>


                    <div>

                        <div class="text-xs uppercase font-bold text-gray-400 mb-2">
                            Status
                        </div>

                        <div id="invoiceStatus">
                            -
                        </div>

                    </div>

                </div>

            </div>


            {{-- DETAIL --}}

            <div class="px-8">

                <div class="border border-gray-200 rounded-xl overflow-hidden">

                    <table class="w-full">

                        <thead>

                            <tr class="bg-gray-50 border-b">

                                <th class="px-5 py-4 text-left text-xs uppercase text-gray-500">
                                    Deskripsi
                                </th>

                                <th class="px-5 py-4 text-right text-xs uppercase text-gray-500">
                                    Jumlah
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            <tr>

                                <td class="px-5 py-5">

                                    <div class="font-bold text-gray-900">

                                        Pembelian {{ config('pw-config.currency_name') }}

                                    </div>

                                    <div class="text-sm text-gray-500 mt-1">

                                        Perfect World Eterna

                                    </div>

                                </td>


                                <td class="px-5 py-5 text-right">

                                    <div
                                        id="invoiceAmount"
                                        class="text-xl font-black text-blue-600"
                                    >
                                        0
                                    </div>

                                    <div class="text-xs text-gray-500">

                                        {{ config('pw-config.currency_name') }}

                                    </div>

                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>


            {{-- TOTAL --}}

            <div class="px-8 py-7">

                <div class="flex justify-end">

                    <div class="w-full md:w-96">

                        <div class="flex justify-between py-2 text-sm text-gray-500">

                            <span>
                                Subtotal
                            </span>

                            <span id="invoiceSubtotal">
                                Rp 0
                            </span>

                        </div>


                        <div class="flex justify-between py-2 text-sm text-gray-500">

                            <span>
                                Biaya Admin
                            </span>

                            <span>
                                Rp 0
                            </span>

                        </div>


                        <div class="border-t border-gray-200 pt-4 mt-3 flex justify-between">

                            <span class="text-lg font-black text-gray-900">
                                TOTAL
                            </span>

                            <span
                                id="invoiceTotal"
                                class="text-2xl font-black text-green-600"
                            >
                                Rp 0
                            </span>

                        </div>

                    </div>

                </div>

            </div>


            {{-- FOOTER --}}

            <div class="px-8 py-6 bg-gray-50 border-t border-gray-200 text-center">

                <div
                    id="invoiceMessage"
                    class="font-bold text-lg"
                >
                    -
                </div>

                <div class="text-sm text-gray-500 mt-2">

                    Terima kasih telah melakukan transaksi di Perfect World Eterna.

                </div>

            </div>


            {{-- BUTTON --}}

            <div class="px-8 py-5 border-t border-gray-200 flex justify-end gap-3">

                <button
                    type="button"
                    id="invoiceCloseBottom"
                    class="px-5 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg font-semibold"
                >
                    Tutup
                </button>


                <button
                    type="button"
                    id="invoicePrint"
                    class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold"
                >

                    Cetak Invoice

                </button>

            </div>

        </div>

    </div>

</div>


{{-- ========================================================= --}}
{{-- INVOICE SCRIPT --}}
{{-- ========================================================= --}}

<script>

(function () {

    function initInvoice()
    {

        const modal =
            document.getElementById('invoiceModal');

        const backdrop =
            document.getElementById('invoiceBackdrop');

        const closeButton =
            document.getElementById('invoiceClose');

        const closeBottom =
            document.getElementById('invoiceCloseBottom');

        const printButton =
            document.getElementById('invoicePrint');


        if (!modal) {
            return;
        }


        /*
        |--------------------------------------------------------------------------
        | FORMAT RUPIAH
        |--------------------------------------------------------------------------
        */

        function rupiah(value)
        {

            value = parseFloat(value) || 0;

            return 'Rp ' +
                value.toLocaleString('id-ID');

        }


        /*
        |--------------------------------------------------------------------------
        | FORMAT ANGKA
        |--------------------------------------------------------------------------
        */

        function number(value)
        {

            value = parseFloat(value) || 0;

            return value.toLocaleString('id-ID');

        }


        /*
        |--------------------------------------------------------------------------
        | OPEN INVOICE
        |--------------------------------------------------------------------------
        */

        function openInvoice(button)
        {

            const trx =
                button.getAttribute('data-trx');

            const user =
                button.getAttribute('data-user') || 'N/A';

            const userId =
                button.getAttribute('data-user-id') || '';

            const amount =
                button.getAttribute('data-amount') || 0;

            const money =
                button.getAttribute('data-money') || 0;

            const status =
                button.getAttribute('data-status') || '';

            const date =
                button.getAttribute('data-date') || '-';

            const time =
                button.getAttribute('data-time') || '-';


            /*
            |--------------------------------------------------------------------------
            | DATA
            |--------------------------------------------------------------------------
            */

            document.getElementById('invoiceTrx').textContent =
                '#' + trx;

            document.getElementById('invoiceUser').textContent =
                user;

            document.getElementById('invoiceUserId').textContent =
                userId ? 'User ID: ' + userId : '';

            document.getElementById('invoiceAmount').textContent =
                number(amount);

            document.getElementById('invoiceSubtotal').textContent =
                rupiah(money);

            document.getElementById('invoiceTotal').textContent =
                rupiah(money);

            document.getElementById('invoiceDate').textContent =
                date;

            document.getElementById('invoiceTime').textContent =
                time + ' WITA';


            /*
            |--------------------------------------------------------------------------
            | STATUS
            |--------------------------------------------------------------------------
            */

            const statusBox =
                document.getElementById('invoiceStatus');

            const message =
                document.getElementById('invoiceMessage');


            if (status === 'berhasil')
            {

                statusBox.innerHTML = `
                    <span class="inline-flex items-center px-3 py-1.5 rounded-full bg-green-100 text-green-700 font-bold text-sm">
                        <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                        BERHASIL
                    </span>
                `;

                message.className =
                    'font-bold text-lg text-green-600';

                message.textContent =
                    '? PEMBAYARAN BERHASIL';

            }

            else if (status === 'pending')
            {

                statusBox.innerHTML = `
                    <span class="inline-flex items-center px-3 py-1.5 rounded-full bg-yellow-100 text-yellow-700 font-bold text-sm">
                        <span class="w-2 h-2 bg-yellow-500 rounded-full mr-2"></span>
                        PENDING
                    </span>
                `;

                message.className =
                    'font-bold text-lg text-yellow-600';

                message.textContent =
                    '? PEMBAYARAN PENDING';

            }

            else
            {

                statusBox.innerHTML = `
                    <span class="inline-flex items-center px-3 py-1.5 rounded-full bg-red-100 text-red-700 font-bold text-sm">
                        ${status.toUpperCase()}
                    </span>
                `;

                message.className =
                    'font-bold text-lg text-red-600';

                message.textContent =
                    '? PEMBAYARAN ' + status.toUpperCase();

            }


            /*
            |--------------------------------------------------------------------------
            | SHOW MODAL
            |--------------------------------------------------------------------------
            */

            modal.classList.remove('hidden');

            document.body.classList.add('overflow-hidden');

        }


        /*
        |--------------------------------------------------------------------------
        | CLOSE
        |--------------------------------------------------------------------------
        */

        function closeInvoice()
        {

            modal.classList.add('hidden');

            document.body.classList.remove('overflow-hidden');

        }


        /*
        |--------------------------------------------------------------------------
        | CLICK KODE TRANSAKSI
        |--------------------------------------------------------------------------
        */

        document
            .querySelectorAll('.invoice-trigger')
            .forEach(function (button)
            {

                button.addEventListener('click', function ()
                {

                    openInvoice(this);

                });

            });


        /*
        |--------------------------------------------------------------------------
        | CLOSE BUTTON
        |--------------------------------------------------------------------------
        */

        closeButton.addEventListener(
            'click',
            closeInvoice
        );


        closeBottom.addEventListener(
            'click',
            closeInvoice
        );


        backdrop.addEventListener(
            'click',
            closeInvoice
        );


        /*
        |--------------------------------------------------------------------------
        | ESC
        |--------------------------------------------------------------------------
        */

        document.addEventListener(
            'keydown',
            function (event)
            {

                if (event.key === 'Escape')
                {

                    closeInvoice();

                }

            }
        );


        /*
        |--------------------------------------------------------------------------
        | PRINT
        |--------------------------------------------------------------------------
        */

        printButton.addEventListener(
            'click',
            function ()
            {

                const invoice =
                    document.getElementById('invoiceBox');

                if (!invoice) {
                    return;
                }


                const printWindow =
                    window.open(
                        '',
                        '_blank',
                        'width=900,height=900'
                    );


                if (!printWindow)
                {

                    alert(
                        'Popup diblokir browser. Izinkan popup untuk mencetak invoice.'
                    );

                    return;

                }


                printWindow.document.write(`
                    <!DOCTYPE html>

                    <html>

                    <head>

                        <meta charset="UTF-8">

                        <title>Invoice Perfect World Eterna</title>

                        <script src="https://cdn.tailwindcss.com"><\/script>

                        <style>

                            body {
                                background: white;
                                margin: 0;
                                padding: 30px;
                            }

                            @media print {

                                body {
                                    padding: 0;
                                }

                            }

                        </style>

                    </head>

                    <body>

                        ${invoice.outerHTML}

                    </body>

                    </html>
                `);


                printWindow.document.close();


                setTimeout(function ()
                {

                    printWindow.focus();

                    printWindow.print();

                }, 1000);

            }
        );

    }


    /*
    |--------------------------------------------------------------------------
    | START
    |--------------------------------------------------------------------------
    */

    if (document.readyState === 'loading')
    {

        document.addEventListener(
            'DOMContentLoaded',
            initInvoice
        );

    }

    else
    {

        initInvoice();

    }

})();

</script>

        <!-- Chart.js -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {

                // Filter functionality
                const filterSelect = document.querySelector('select[name="filter"]');
                const monthFilter = document.getElementById('monthFilter');

                filterSelect.addEventListener('change', function() {
                    if (this.value === 'yearly' || this.value === 'all') {
                        monthFilter.style.display = 'none';
                    } else {
                        monthFilter.style.display = 'block';
                    }
                });

                // Chart
                const ctx = document.getElementById('revenueChart').getContext('2d');
                const monthlyData = @json($monthlyData);

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: monthlyData.map(item => item.month),
                        datasets: [{
                            label: 'iPaymu',
                            data: monthlyData.map(item => item.ipaymu),
                            borderColor: 'rgb(34, 197, 94)',
                            backgroundColor: 'rgba(34, 197, 94, 0.1)',
                            tension: 0.4,
                            borderWidth: 3
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return 'Rp ' + value.toLocaleString('id-ID');
                                    }
                                }
                            }
                        },
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return context.dataset.label + ': Rp ' + context.parsed.y.toLocaleString('id-ID');
                                    }
                                }
                            }
                        }
                    }
                });
            });
        </script>
    </x-slot>
</x-hrace009.layouts.admin>
