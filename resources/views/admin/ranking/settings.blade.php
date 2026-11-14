@section('title', ' - ' . __('ranking.title'))

<x-hrace009.layouts.admin>
    <x-slot name="header">
        <div class="flex items-center justify-between px-4 py-4 border-b lg:py-6 dark:border-primary-darker">
            <h1 class="text-2xl font-semibold">{{ __('ranking.config') }}</h1>
            
            <!-- Tombol Download Excel -->
            <a href="{{ route('ranking.spender_coin.export') }}"
               class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded shadow transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 4v16m8-8H4"/>
                </svg>
                {{ __('general.download_excel') ?? 'Download Excel' }}
            </a>
        </div>
    </x-slot>

    <x-slot name="content">
        <div class="max-w mx-6 my-6">
            <x-hrace009::admin.automate-panel/>
        </div>

        <div class="max-w-sm mx-auto mt-6">
            <x-hrace009::admin.validation-error/>
            <form method="post" action="{{ route('admin.ranking.postSettings') }}">
                {!! csrf_field() !!}
                <div class="relative z-0 mb-6 w-full group">
                    <x-hrace009::input-with-popover id="ignoreRoles" name="ignoreRoles"
                                                    value="{{ config('pw-config.ignoreRoles') }}"
                                                    placeholder=" " required :popover="__('ranking.role.desc')"/>
                    <x-hrace009::label for="ignoreRoles">{{ __('ranking.role.ignore') }}</x-hrace009::label>
                </div>
                <div class="relative z-0 mb-6 w-full group">
                    <x-hrace009::input-with-popover id="ignoreFaction" name="ignoreFaction"
                                                    value="{{ config('pw-config.ignoreFaction') }}" placeholder=" "
                                                    required
                                                    :popover="__('ranking.faction.desc')"
                    />
                    <x-hrace009::label for="ignoreFaction">{{ __('ranking.faction.ignore') }}</x-hrace009::label>
                </div>
                <x-hrace009::button-with-popover class="w-auto" popover="{{ __('general.config_save_desc') }}">
                    {{ __('general.Save') }}
                </x-hrace009::button-with-popover>
            </form>
        </div>

        <div class="max-w-screen-xl mx-6 my-6">
            <div class="grid grid-cols-3 gap-8 p-4">
                <div class="flex-col p-4 bg-white rounded-md dark:bg-darker">
                    <h1 class="text-sm font-medium tracking-wider text-gray-500 uppercase dark:text-primary-light">
                        {{ __('ranking.force.manual.player') }}
                    </h1>
                    <x-hrace009::divider/>
                    <div class="flex mt-4 items-center">
                        {{ __('ranking.force.manual.player_message') }}
                    </div>
                    <div class="flex mt-4 items-center">
                        <form action="{{ route('admin.ranking.updatePlayers') }}" method="get">
                            {!! csrf_field() !!}
                            <x-hrace009::button-with-popover class="w-auto" popover="{{ __('ranking.process_desc') }}">
                                {{ __('ranking.process') }}
                            </x-hrace009::button-with-popover>
                        </form>
                    </div>
                </div>

                <div class="flex-col p-4 bg-white rounded-md dark:bg-darker">
                    <h1 class="text-sm font-medium tracking-wider text-gray-500 uppercase dark:text-primary-light">
                        {{ __('ranking.force.manual.faction') }}
                    </h1>
                    <x-hrace009::divider/>
                    <div class="flex mt-4 items-center">
                        {{ __('ranking.force.manual.faction_message') }}
                    </div>
                    <div class="flex mt-4 items-center">
                        <form action="{{ route('admin.ranking.updateFaction') }}" method="get">
                            {!! csrf_field() !!}
                            <x-hrace009::button-with-popover class="w-auto" popover="{{ __('ranking.process_desc') }}">
                                {{ __('ranking.process') }}
                            </x-hrace009::button-with-popover>
                        </form>
                    </div>
                </div>

                <div class="flex-col p-4 bg-white rounded-md dark:bg-darker">
                    <h1 class="text-sm font-medium tracking-wider text-gray-500 uppercase dark:text-primary-light">
                        {{ __('ranking.force.manual.terrotories') }}
                    </h1>
                    <x-hrace009::divider/>
                    <div class="flex mt-4 items-center">
                        {{ __('ranking.force.manual.terrotories_message') }}
                    </div>
                    <div class="flex mt-4 items-center">
                        <form action="{{ route('admin.ranking.updateTerritories') }}" method="get">
                            {!! csrf_field() !!}
                            <x-hrace009::button-with-popover class="w-auto" popover="{{ __('ranking.process_desc') }}">
                                {{ __('ranking.process') }}
                            </x-hrace009::button-with-popover>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>
</x-hrace009.layouts.admin>
