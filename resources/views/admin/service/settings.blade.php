@section('title', ' - ' . __('service.title'))
<x-hrace009.layouts.admin>
    <x-slot name="header">
        <div class="flex items-center justify-between px-4 py-4 border-b lg:py-6 dark:border-primary-darker">
            <h1 class="text-2xl font-semibold">{{ __('service.config') }}</h1>
        </div>
    </x-slot>

    <x-slot name="content">
        <div class="max-w-2xl mx-auto mt-6">
            <h2 class="mb-6">{{ __('service.teleport_character') }}</h2>
            <x-hrace009::admin.validation-error/>
            <form action="{{ route('admin.service.updateSettings') }}" method="post">
                {!! csrf_field() !!}

                {{-- Teleport Config --}}
                <div class="relative z-0 mb-6 w-full group">
                    <x-hrace009::input id="teleport_world_tag" name="teleport_world_tag"
                                       value="{{ config('pw-config.teleport_world_tag') }}"
                                       placeholder=" " required/>
                    <x-hrace009::label for="teleport_world_tag">{{ __('service.teleport_world_tag') }}</x-hrace009::label>
                </div>
                <div class="relative z-0 mb-6 w-full group">
                    <x-hrace009::input id="teleport_x" name="teleport_x"
                                       value="{{ config('pw-config.teleport_x') }}"
                                       placeholder=" " required/>
                    <x-hrace009::label for="teleport_x">{{ __('service.teleport_x') }}</x-hrace009::label>
                </div>
                <div class="relative z-0 mb-6 w-full group">
                    <x-hrace009::input id="teleport_y" name="teleport_y"
                                       value="{{ config('pw-config.teleport_y') }}"
                                       placeholder=" " required/>
                    <x-hrace009::label for="teleport_y">{{ __('service.teleport_y') }}</x-hrace009::label>
                </div>
                <div class="relative z-0 mb-6 w-full group">
                    <x-hrace009::input id="teleport_z" name="teleport_z"
                                       value="{{ config('pw-config.teleport_z') }}"
                                       placeholder=" " required/>
                    <x-hrace009::label for="teleport_z">{{ __('service.teleport_z') }}</x-hrace009::label>
                </div>

                {{-- Level Up Config --}}
                <h2 class="mb-6">{{ __('service.level_up') }}</h2>
                <div class="relative z-0 mb-6 w-full group">
                    <x-hrace009::input id="level_up_cap" name="level_up_cap"
                                       value="{{ config('pw-config.level_up_cap') }}"
                                       placeholder=" " required/>
                    <x-hrace009::label for="level_up_cap">{{ __('service.level_up_cap') }}</x-hrace009::label>
                </div>

                {{-- Exchange Config --}}
                <h2 class="mb-6">{{ __('service.exchange_settings') }}</h2>
                <div class="relative z-0 mb-6 w-full group">
                    <x-hrace009::input id="item_id" name="item_id"
                                       value="{{ config('pw-config.item_id') }}"
                                       placeholder=" " required/>
                    <x-hrace009::label for="item_id">{{ __('service.item_id') }}</x-hrace009::label>
                </div>
                <div class="relative z-0 mb-6 w-full group">
                    <x-hrace009::input id="price_per_unit" name="price_per_unit"
                                       value="{{ config('pw-config.price_per_unit') }}"
                                       placeholder=" " required/>
                    <x-hrace009::label for="price_per_unit">{{ __('service.price_per_unit') }}</x-hrace009::label>
                </div>
<div class="relative z-0 mb-6 w-full group">
    <label for="target" class="block mb-2 text-sm font-medium text-gray-700 dark:text-white">
        {{ __('service.target') }}
    </label>
    <select id="target" name="target"
            class="form-select w-full px-4 py-2 border rounded-md focus:outline-none focus:ring focus:border-blue-300 dark:bg-gray-800 dark:border-gray-600 dark:text-white"
            required>
        <option value="bonuses" {{ config('pw-config.target') === 'bonuses' ? 'selected' : '' }}>
            {{ __('service.bonuses') }}
        </option>
        <option value="money" {{ config('pw-config.target') === 'money' ? 'selected' : '' }}>
            {{ __('service.money') }}
        </option>
    </select>
</div>

                {{-- Checkbox Config (HTML Standard) --}}
                <div class="flex items-center mb-6">
                    <input type="checkbox" id="bonuses" name="bonuses"
                           class="form-checkbox h-5 w-5 text-indigo-600"
                           {{ config('pw-config.bonuses') ? 'checked' : '' }}>
                    <label for="bonuses" class="ml-2 text-sm">{{ __('service.bonuses') }}</label>
                </div>

                <div class="flex items-center mb-6">
                    <input type="checkbox" id="money" name="money"
                           class="form-checkbox h-5 w-5 text-indigo-600"
                           {{ config('pw-config.money') ? 'checked' : '' }}>
                    <label for="money" class="ml-2 text-sm">{{ __('service.money') }}</label>
                </div>

                {{-- Submit --}}
                <x-hrace009::button-with-popover class="w-auto" popover="{{ __('general.config_save_desc') }}">
                    {{ __('general.Save') }}
                </x-hrace009::button-with-popover>
            </form>
        </div>
    </x-slot>
</x-hrace009.layouts.admin>
