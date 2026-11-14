@php
    use Illuminate\Support\Str;

    /*
    |--------------------------------------------------------------------------
    | FILTER PERIODE
    |--------------------------------------------------------------------------
    |
    | Nilai utama berasal dari RankingController.
    | Fallback request dipakai agar tetap aman.
    |
    */

    $selectedMonth = (int) ($selectedMonth ?? request('month', now()->month));
    $selectedYear  = (int) ($selectedYear ?? request('year', now()->year));

    if ($selectedMonth < 1 || $selectedMonth > 12) {
        $selectedMonth = now()->month;
    }

    if ($selectedYear < 2020 || $selectedYear > now()->year) {
        $selectedYear = now()->year;
    }

    /*
    |--------------------------------------------------------------------------
    | NAMA PERIODE
    |--------------------------------------------------------------------------
    */

    $periodLabel = $periodLabel
        ?? \Carbon\Carbon::create(
            $selectedYear,
            $selectedMonth,
            1
        )
        ->locale(app()->getLocale())
        ->translatedFormat('F Y');
@endphp


@section('title', ' - ' . __('ranking.title_spender_coin'))


<x-hrace009.layouts.app>

    {{-- ============================================================= --}}
    {{-- HEADER --}}
    {{-- ============================================================= --}}

    <x-slot name="header">

        <div
            class="
                flex
                items-center
                justify-between
                px-4
                py-4
                border-b
                lg:py-6
                dark:border-primary-darker
            "
        >

            <h1 class="text-2xl font-semibold">

                {{
                    __('general.menu.ranking.spender_coin')
                    ?? 'Ranking: Top Spender Coin'
                }}

            </h1>

        </div>

    </x-slot>



    {{-- ============================================================= --}}
    {{-- CONTENT --}}
    {{-- ============================================================= --}}

    <x-slot name="content">

        <div class="mx-auto max-w-2xl px-4 xl:-mx-4 rounded">


            {{-- ===================================================== --}}
            {{-- PERIOD + TOTAL SPENDER --}}
            {{-- ===================================================== --}}

            <div class="grid gap-4 md:grid-cols-2 mb-6">


                {{-- ================================================= --}}
                {{-- PERIOD BOX --}}
                {{-- ================================================= --}}

                <div
                    class="
                        flex
                        flex-col
                        items-center
                        justify-center
                        text-center
                        p-4
                        bg-white
                        dark:bg-primary
                        rounded-lg
                        shadow
                        border
                        border-gray-200
                        dark:border-primary-light
                        transition
                    "
                >

                    {{-- ICON CALENDAR --}}

                    <svg
                        class="w-6 h-6 mb-1 text-gray-500 dark:text-gray-300"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        viewBox="0 0 24 24"
                    >

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                        />

                    </svg>


                    <div
                        class="
                            text-xs
                            text-gray-600
                            dark:text-gray-300
                            font-semibold
                            mb-1
                        "
                    >

                        {{ __('ranking.period') ?? 'Period' }}

                    </div>


                    {{-- ============================================= --}}
                    {{-- FILTER BULAN + TAHUN --}}
                    {{-- ============================================= --}}

                    <form
                        method="GET"
                        action="{{ url()->current() }}"
                        class="
                            flex
                            items-center
                            justify-center
                            gap-1
                            w-full
                            mt-1
                        "
                    >

                        {{-- BULAN --}}

                        <select
                            name="month"
                            onchange="this.form.submit()"
                            class="
                                w-32
                                text-sm
                                rounded-md
                                border-gray-300
                                dark:border-primary-light
                                bg-white
                                dark:bg-darker
                                text-gray-800
                                dark:text-white
                                focus:border-primary
                                focus:ring-primary
                            "
                        >

                            @foreach(range(1, 12) as $month)

                                @php

                                    $monthName = \Carbon\Carbon::create(
                                        2000,
                                        $month,
                                        1
                                    )
                                    ->locale(app()->getLocale())
                                    ->translatedFormat('F');

                                @endphp


                                <option
                                    value="{{ $month }}"
                                    {{ (int) $selectedMonth === (int) $month ? 'selected' : '' }}
                                >

                                    {{ $monthName }}

                                </option>

                            @endforeach

                        </select>



                        {{-- TAHUN --}}

                        <select
                            name="year"
                            onchange="this.form.submit()"
                            class="
                                w-24
                                text-sm
                                rounded-md
                                border-gray-300
                                dark:border-primary-light
                                bg-white
                                dark:bg-darker
                                text-gray-800
                                dark:text-white
                                focus:border-primary
                                focus:ring-primary
                            "
                        >

                            @for($year = now()->year; $year >= 2020; $year--)

                                <option
                                    value="{{ $year }}"
                                    {{ (int) $selectedYear === (int) $year ? 'selected' : '' }}
                                >

                                    {{ $year }}

                                </option>

                            @endfor

                        </select>

                    </form>


                    {{-- PERIODE AKTIF --}}

                    <div
                        class="
                            mt-3
                            text-sm
                            font-bold
                            text-gray-800
                            dark:text-white
                        "
                    >

                        {{ $periodLabel }}

                    </div>

                </div>



                {{-- ================================================= --}}
                {{-- TOTAL SPENDER BOX --}}
                {{-- ================================================= --}}

                <div
                    class="
                        flex
                        flex-col
                        items-center
                        justify-center
                        text-center
                        p-4
                        bg-green-50
                        dark:bg-primary-light
                        rounded-lg
                        shadow-md
                        border
                        border-green-300
                        dark:border-primary
                    "
                >

                    {{-- ICON MONEY --}}

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="
                            w-7
                            h-7
                            mb-1
                            text-green-600
                            dark:text-light
                        "
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                        />

                    </svg>


                    <div
                        class="
                            text-xs
                            text-gray-600
                            dark:text-gray-300
                            font-semibold
                        "
                    >

                        Total Spender

                    </div>


                    {{-- BULAN YANG SEDANG DILIHAT --}}

                    <div
                        class="
                            text-xs
                            text-gray-500
                            dark:text-gray-300
                            mt-1
                        "
                    >

                        {{ $periodLabel }}

                    </div>


                    {{-- TOTAL RUPIAH BULAN TERPILIH --}}

                    <div
                        class="
                            mt-1
                            text-sm
                            font-bold
                            text-green-800
                            dark:text-white
                        "
                    >

                        Rp. {{
                            number_format(
                                $totalMoneyAllSpenders ?? 0,
                                0,
                                ',',
                                '.'
                            )
                        }}

                    </div>

                </div>

            </div>



            {{-- ===================================================== --}}
            {{-- TABLE RANKING --}}
            {{-- ===================================================== --}}

            <div
                class="
                    bg-white
                    dark:bg-primary
                    shadow-md
                    rounded
                    border
                    border-gray-300
                    dark:border-primary-light
                    overflow-x-auto
                "
            >

                <table class="w-full table-auto">


                    {{-- ============================================= --}}
                    {{-- HEADER TABLE --}}
                    {{-- ============================================= --}}

                    <thead>

                        <tr
                            class="
                                bg-gray-200
                                dark:bg-primary
                                text-gray-600
                                dark:text-light
                                uppercase
                                text-xs
                                leading-normal
                            "
                        >

                            <th class="py-3 px-6 text-center">

                                Rank

                            </th>


                            <th class="py-3 px-6 text-center">

                                Name

                            </th>


                            <th class="py-3 px-6 text-center">

                                Streamer

                            </th>


                            <th class="py-3 px-6 text-center">

                                Coin

                            </th>


                            <th class="py-3 px-6 text-center">

                                Total

                            </th>

                        </tr>

                    </thead>



                    {{-- ============================================= --}}
                    {{-- ISI RANKING --}}
                    {{-- ============================================= --}}

                    <tbody class="text-xs text-gray-600 dark:text-light">


                        @forelse ($topSpenders as $spender)


                            <tr
                                class="
                                    border-b
                                    border-gray-200
                                    bg-gray-50
                                    dark:border-primary
                                    dark:bg-darker
                                    hover:bg-gray-100
                                    dark:hover:bg-primary-dark
                                "
                            >


                                {{-- RANK --}}

                                <td
                                    class="
                                        py-3
                                        px-6
                                        text-center
                                        font-semibold
                                    "
                                >

                                    {{
                                        ($topSpenders->currentPage() - 1)
                                        * $topSpenders->perPage()
                                        + $loop->iteration
                                    }}

                                </td>



                                {{-- NAME --}}

                                <td
                                    class="
                                        py-3
                                        px-6
                                        text-center
                                    "
                                >

                                    {{
                                        Str::limit(
                                            $spender->name ?? '-',
                                            50,
                                            ''
                                        )
                                    }}

                                </td>



                                {{-- STREAMER --}}

                                <td
                                    class="
                                        py-3
                                        px-6
                                        text-center
                                    "
                                >

                                    {{ $spender->streamer_name ?? '-' }}

                                </td>



                                {{-- TOTAL COIN BULAN TERPILIH --}}

                                <td
                                    class="
                                        py-3
                                        px-6
                                        text-center
                                    "
                                >

                                    {{
                                        number_format(
                                            $spender->total_amount ?? 0,
                                            0,
                                            ',',
                                            '.'
                                        )
                                    }}

                                </td>



                                {{-- TOTAL RUPIAH BULAN TERPILIH --}}

                                <td
                                    class="
                                        py-3
                                        px-6
                                        text-center
                                        font-semibold
                                    "
                                >

                                    Rp. {{
                                        number_format(
                                            $spender->total_money ?? 0,
                                            0,
                                            ',',
                                            '.'
                                        )
                                    }}

                                </td>

                            </tr>


                        @empty


                            <tr>

                                <td
                                    colspan="5"
                                    class="
                                        py-8
                                        px-6
                                        text-center
                                        text-sm
                                        text-gray-500
                                        dark:text-gray-300
                                    "
                                >

                                    Tidak ada data spender untuk periode

                                    <strong>
                                        {{ $periodLabel }}
                                    </strong>.

                                </td>

                            </tr>


                        @endforelse


                    </tbody>

                </table>

            </div>



            {{-- ===================================================== --}}
            {{-- INFO + PAGINATION --}}
            {{-- ===================================================== --}}

            <div
                class="
                    mt-5
                    flex
                    flex-col
                    sm:flex-row
                    sm:items-center
                    sm:justify-between
                    gap-4
                "
            >


                {{-- INFO JUMLAH DATA --}}

                <div
                    class="
                        text-sm
                        text-gray-600
                        dark:text-gray-300
                    "
                >

                    @if($topSpenders->total() > 0)

                        Showing

                        <strong>
                            {{ $topSpenders->firstItem() }}
                        </strong>

                        to

                        <strong>
                            {{ $topSpenders->lastItem() }}
                        </strong>

                        of

                        <strong>
                            {{ $topSpenders->total() }}
                        </strong>

                        results

                    @else

                        Showing 0 results

                    @endif

                </div>



                {{-- PAGINATION --}}

                @if ($topSpenders->hasPages())

                    <div>

                        {{
                            $topSpenders
                                ->appends([
                                    'month' => $selectedMonth,
                                    'year'  => $selectedYear,
                                ])
                                ->links()
                        }}

                    </div>

                @endif

            </div>


        </div>

    </x-slot>

</x-hrace009.layouts.app>