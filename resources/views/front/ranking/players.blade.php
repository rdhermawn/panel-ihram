@section('title', ' - ' . __('ranking.title'))
<x-hrace009.layouts.app>
    <x-slot name="header">
        <div class="flex items-center justify-between px-4 py-4 border-b lg:py-6 dark:border-primary-darker">
            <h1 class="text-2xl font-semibold">{{ __('general.menu.ranking.players') }}</h1>
        </div>
    </x-slot>

    <x-slot name="content">
        <div class="mx-auto rounded max-w-3xl xl:-mx-4 px-4">

        {{-- FILTER BY CLASS - MODERN BUTTON STYLE --}}
        <div class="mb-6">
            <div class="flex flex-wrap gap-2 justify-start items-center">
                @php
                    $classIcons = [
                        0 => 'blademaster', 1 => 'mage', 2 => 'psychic', 3 => 'venomancer',
                        4 => 'barbarian', 5 => 'assassin', 6 => 'archer', 7 => 'cleric',
                        8 => 'seeker', 9 => 'mystic', 10 => 'duskblade', 11 => 'stormbringer',
                        12 => 'technician', 13 => 'edgerunner', 14 => 'wildwalker',
                    ];
                @endphp

                @foreach($classIcons as $id => $icon)
                    <a href="{{ route('app.ranking.player', [Request::segment(4), 'class' => $id]) }}">
                        <button class="rounded-full border transition-all duration-200 shadow-sm p-1
                            {{ request('class') == $id
                                ? 'border-primary ring-2 ring-primary dark:border-white dark:ring-white'
                                : 'border-gray-300 hover:border-primary dark:border-gray-600' }}">
                            <img src="{{ asset('img/icon/' . $icon . '.png') }}"
                                 alt="{{ $icon }}"
                                 title="{{ __('general.classes.' . $id) }}"
                                 class="w-6 h-6 rounded-full bg-white dark:bg-darker shadow-md">
                        </button>
                    </a>
                @endforeach

                @if(request()->has('class'))
                    <a href="{{ route('app.ranking.player', Request::segment(4)) }}">
                        <button class="ml-2 p-2 rounded-full border border-gray-300 text-gray-600 hover:bg-red-100 hover:text-red-600 hover:border-red-400 transition duration-150 dark:bg-transparent dark:hover:bg-red-500 dark:hover:text-white" title="Reset Filter">
                            {{-- Ikon reset dari icon666.com --}}
                            <svg version="1.1" xmlns="http://www.w3.org/2000/svg"
                                 viewBox="0 0 34.333 34.333"
                                 class="w-4 h-4 fill-current">
                                <g>
                                    <path d="M34.167,22.333v7.666c0,1.381-1.119,2.5-2.5,2.5s-2.5-1.119-2.5-2.5v-0.24
                                        c-3.109,2.886-7.238,4.574-11.667,4.574c-6.937,0-13.152-4.135-15.836-10.533
                                        c-0.132-0.315-0.194-0.643-0.194-0.965c0-0.979,0.576-1.905,1.533-2.308
                                        c1.273-0.534,2.738,0.064,3.272,1.339c1.902,4.536,6.309,7.467,11.225,7.467
                                        c3.737,0,7.176-1.694,9.446-4.5H24c-1.381,0-2.5-1.119-2.5-2.5s1.119-2.5,2.5-2.5h7.666
                                        C33.047,19.833,34.167,20.952,34.167,22.333z M10.333,14.834c1.381,0,2.5-1.12,2.5-2.5
                                        c0-1.381-1.119-2.5-2.5-2.5H7.796C10.061,6.832,13.618,5,17.5,5c4.917,0,9.323,2.932,11.226,7.468
                                        c0.533,1.273,1.998,1.872,3.271,1.339c1.273-0.534,1.872-1.998,1.339-3.272
                                        C30.651,4.135,24.436,0,17.5,0C12.75,0,8.337,1.941,5.167,5.219V4.667c0-1.381-1.12-2.5-2.5-2.5
                                        c-1.381,0-2.5,1.119-2.5,2.5v7.667c0,0.663,0.264,1.299,0.732,1.769c0.469,0.469,1.104,0.731,1.768,0.731H10.333z"/>
                                </g>
                            </svg>
                        </button>
                    </a>
                @endif
            </div>
        </div>

        {{-- TABS --}}
            <div class="flex-1 flex items-center justify-center sm:items-stretch sm:justify-start mb-4">
                <div class="hidden sm:block bg-gray-200 dark:bg-darker p-2 rounded">
                    <div class="flex space-x-4">
                        <!-- Current: "bg-gray-900 text-white", Default: "text-gray-300 hover:bg-gray-700 hover:text-white" -->
                        <a href="{{ route('app.ranking.player', 'level') }}"
                           class="{{ (request()->is('*/level') ) ? 'bg-gray-900 dark:bg-primary text-white' : 'text-gray-500 hover:bg-gray-700 hover:text-white dark:hover:bg-primary' }} px-3 py-2 rounded-md text-sm font-medium">{{ __('ranking.type.level') }}</a>

                        <a href="{{ route('app.ranking.player', 'rep') }}"
                           class="{{ (request()->is('*/rep') ) ? 'bg-gray-900 dark:bg-primary text-white' : 'text-gray-500 hover:bg-gray-700 hover:text-white dark:hover:bg-primary' }} px-3 py-2 rounded-md text-sm font-medium">{{ __('ranking.type.rep') }}</a>

                        <a href="{{ route('app.ranking.player', 'time') }}"
                           class="{{ (request()->is('*/time') ) ? 'bg-gray-900 dark:bg-primary text-white' : 'text-gray-500 hover:bg-gray-700 hover:text-white dark:hover:bg-primary' }} px-3 py-2 rounded-md text-sm font-medium">{{ __('ranking.type.time') }}</a>

                        <a href="{{ route('app.ranking.player', 'pvp') }}"
                           class="{{ (request()->is('*/pvp') ) ? 'bg-gray-900 dark:bg-primary text-white' : 'text-gray-500 hover:bg-gray-700 hover:text-white dark:hover:bg-primary' }} px-3 py-2 rounded-md text-sm font-medium">{{ __('ranking.type.pvp') }}</a>
                        <a href="{{ route('app.ranking.player', 'pariah_time') }}"
                           class="{{ (request()->is('*/pariah_time') ) ? 'bg-gray-900 dark:bg-primary text-white' : 'text-gray-500 hover:bg-gray-700 hover:text-white dark:hover:bg-primary' }} px-3 py-2 rounded-md text-sm font-medium">{{ __('ranking.type.pariah_time') }}</a>
                    </div>
                </div>
            </div>
        {{-- TABLE --}}
        <div class="bg-white dark:bg-primary shadow-md rounded border border-gray-300 dark:border-primary-light justify-items-center">
            <table class="w-full table-auto">
                <thead>
                <tr class="bg-gray-200 dark:bg-primary dark:text-light text-gray-600 uppercase text-xs leading-normal">
                    <th class="py-3 px-6 text-left">#</th>
                    <th class="py-3 px-6 text-left">{{ __('ranking.name') }}</th>
                    <th class="py-3 px-6 text-left">{{ __('ranking.class') }}</th>
                    <th class="py-3 px-6 text-left">{{ __('ranking.gender.caption') }}</th>
                    <th class="py-3 px-6 text-left">{{ __('ranking.spouse.caption') }}</th>
                    <th class="py-3 px-6 text-left">{{ __('ranking.type.' . Request::segment(4)) }}</th>
                    <th class="py-3 px-6 text-left">{{ __('ranking.guild') }}</th>
                </tr>
                </thead>
                <tbody class="text-gray-600 text-xs dark:text-light">
                @foreach($ranks as $rank)
                    @php
                        $classId = $rank->cls;
                        $classKey = $classIcons[$classId] ?? 'unknown';
                    @endphp
                    <tr class="border-b border-gray-200 bg-gray-50 hover:bg-gray-100 dark:border-primary dark:bg-darker dark:hover:bg-primary-dark">
                        <td class="py-3 px-6 text-left">
                            {{ ($ranks->currentPage() - 1) * $ranks->perPage() + $loop->iteration }}
                        </td>
                        <td class="py-3 px-6 text-left">
                            {{ $rank->name }}
                        </td>
                        <td class="py-3 px-6 text-left">
                            <div class="flex items-center space-x-2">
                                <img src="{{ asset('img/icon/' . $classKey . '.png') }}"
                                     alt="{{ $classKey }}"
                                     class="w-6 h-6"
                                     title="{{ __('general.classes.' . $classId) }}">
                                <span class="hidden sm:inline">{{ __('general.classes.' . $classId) }}</span>
                            </div>
                        </td>
                        <td class="py-3 px-6 text-left">
                            <img src="/img/gender/{{ $rank->gender }}.png" alt="{{ $rank->gender }}" class="w-5 h-5" title="{{ __('ranking.gender.' . $rank->gender) }}">
                        </td>
                        <td class="py-3 px-6 text-left">
                            @if($rank->spouse)
                                @if(!$players->getPlayer($rank->spouse))
                                    {{ __('ranking.spouse.4everalone') }}
                                @else
                                    {{ $players->getPlayer($rank->spouse)->name }}
                                @endif
                            @else
                                {{ __('ranking.spouse.4everalone') }}
                            @endif
                        </td>
                        <td class="py-3 px-6 text-left">
                            @switch(Request::segment(4))
                                @case('level')
                                    {{ $rank->level }}
                                    @break
                                @case('rep')
                                    {{ number_format($rank->reputation) }}
                                    @break
                                @case('time')
                                    {{ $timeMaker->makeTime($rank->time_used) }}
                                    @break
                                @case('pvp')
                                    {{ $rank->pk_count }}
                                    @break
                                @case('pariah_time')
                                    {{ $timeMaker->makeTime($rank->pariah_time) }}
                                    @break
                            @endswitch
                        </td>
                        <td class="py-3 px-6 text-left">
                            {{ $rank->faction_name ?? '-' }}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        @if($ranks->items())
            <div class="mt-4">
                {{ $ranks->appends(request()->query())->render() }}
            </div>
        @endif
    </div>
    </x-slot>
</x-hrace009.layouts.app>
