@section('title', ' - ' . __('voucher.title'))
<x-hrace009.layouts.admin>
    <x-slot name="header">
        <div class="flex items-center justify-between px-4 py-4 border-b lg:py-6 dark:border-primary-darker">
            <h1 class="text-2xl font-semibold">{{ __('voucher.add') }}</h1>
        </div>
    </x-slot>
    <x-slot name="content">
        <div class="max-w mx-auto mt-6 ml-6 mr-6">
            <x-hrace009::admin.validation-error/>
            <form method="post" action="{{ route('voucher.store') }}" enctype="multipart/form-data">
                {!! csrf_field() !!}

                <!-- Voucher Code Field -->
                <div class="relative z-0 mb-6 w-full group">
                    <x-hrace009::input-with-popover id="code" name="code"
                                                    placeholder=" " readonly
                                                    value="{{ $randomAlphaNum }}"
                                                    :popover="__('voucher.code_desc')"/>
                    <x-hrace009::label
                        for="code">{{ __('voucher.code') }}</x-hrace009::label>
                </div>

                <!-- Amount Field -->
                <div class="relative z-0 mb-6 w-full group">
                    <x-hrace009::input-with-popover id="amount" name="amount"
                                                    placeholder=" "
                                                    :popover="__('voucher.amount_desc', ['currency' => config('pw-config.currency_name')])"/>
                    <x-hrace009::label
                        for="amount">{{ __('voucher.amount', ['currency' => config('pw-config.currency_name')]) }}</x-hrace009::label>
                </div>
                    <!-- Field untuk Max Redeem -->
                <div class="relative z-0 mb-6 w-full group">
                    <x-hrace009::input-with-popover id="max_redeem" name="max_redeem"
                                                    placeholder=" "
                                                    type="number"
                                                    min="1"
                                                    :popover="__('voucher.max_redeem_desc')"/>
                    <x-hrace009::label for="max_redeem">{{ __('voucher.max_redeem') }}</x-hrace009::label>
                </div>
                <!-- Item Name Field -->
                <div class="relative z-0 mb-6 w-full group">
                    <x-hrace009::input-with-popover popover="{{ __('shop.fields.name_desc') }}" id="name" name="name"
                                                    type="text"/>
                    <x-hrace009::label for="name">{{ __('shop.fields.name') }}</x-hrace009::label>
                </div>

                <!-- Item ID Field -->
                <div class="relative z-0 mb-6 w-full group">
                    <x-hrace009::input-with-popover popover="{{ __('shop.fields.item_id_desc') }}" id="item_id"
                                                    name="item_id" type="text"/>
                    <x-hrace009::label for="item_id">{{ __('shop.fields.item_id') }}</x-hrace009::label>
                </div>

                <!-- Octet Field -->
                <div class="relative z-0 mb-6 w-full group">
                    <x-hrace009::input-with-popover popover="{{__('shop.fields.octet_desc')}}" id="octet" name="octet"
                                                    type="text"/>
                    <x-hrace009::label for="octet">{{ __('shop.fields.octet') }}</x-hrace009::label>
                </div>

                <!-- Mask Field -->
                <div class="relative z-0 mb-6 w-full group">
                    <x-hrace009::select-with-popover popover="{{ __('shop.fields.mask_desc') }}" id="mask" name="mask">
                        <option class="dark:text-gray-500"
                                value=""> -
                        </option>
                        @foreach( $masks as $group => $mask )
                            @if( is_array( $mask ) )
                                <optgroup class="dark:text-gray-500" label="{{ $group }}">
                                    @foreach( $mask as $item => $value )
                                        <option class="dark:text-gray-500"
                                                value="{{ $item }}"> {{ $value }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @else
                                <option class="dark:text-gray-500"
                                        value="{{ $group }}"> {{ $mask }}
                                </option>
                            @endif
                        @endforeach
                    </x-hrace009::select-with-popover>
                    <x-hrace009::label for="mask">{{ __('shop.fields.mask') }}</x-hrace009::label>
                </div>

                <!-- Count Field -->
                <div class="relative z-0 mb-6 w-full group">
                    <x-hrace009::input-with-popover popover="{{__('shop.fields.count_desc')}}" id="count" name="count"
                                                    type="text"/>
                    <x-hrace009::label for="count">{{ __('shop.fields.count') }}</x-hrace009::label>
                </div>

                <!-- Max Count Field -->
                <div class="relative z-0 mb-6 w-full group">
                    <x-hrace009::input-with-popover popover="{{__('shop.fields.max_count_desc')}}" id="max_count"
                                                    name="max_count" type="text"/>
                    <x-hrace009::label for="max_count">{{ __('shop.fields.max_count') }}</x-hrace009::label>
                </div>

                <!-- Protection Type Field -->
                <div class="relative z-0 mb-6 w-full group">
                    <x-hrace009::input-with-popover popover="{{__('shop.fields.protection_type_desc')}}"
                                                    id="protection_type" name="protection_type" type="text"/>
                    <x-hrace009::label for="protection_type">{{ __('shop.fields.protection_type') }}</x-hrace009::label>
                </div>

                <div class="relative z-0 mb-6 w-full group">
                    <x-hrace009::select-with-popover popover="{{ __('shop.fields.expire_date_desc') }}" id="expire_date" name="expire_date">
                        <option class="dark:text-gray-500" value="0">-</option>
                        <option class="dark:text-gray-500" value="{{ now()->addHours(24)->timestamp }}">1 Day ({{ now()->format('H:i') }} - {{ now()->addHours(24)->format('Y-m-d H:i') }})</option>
                        <option class="dark:text-gray-500" value="{{ now()->addHours(48)->timestamp }}">2 Days ({{ now()->format('H:i') }} - {{ now()->addHours(48)->format('Y-m-d H:i') }})</option>
                        <option class="dark:text-gray-500" value="{{ now()->addHours(72)->timestamp }}">3 Days ({{ now()->format('H:i') }} - {{ now()->addHours(72)->format('Y-m-d H:i') }})</option>
                        <option class="dark:text-gray-500" value="{{ now()->addHours(168)->timestamp }}">7 Days ({{ now()->format('H:i') }} - {{ now()->addHours(168)->format('Y-m-d H:i') }})</option>
                        <option class="dark:text-gray-500" value="{{ now()->addHours(720)->timestamp }}">30 Days ({{ now()->format('H:i') }} - {{ now()->addHours(720)->format('Y-m-d H:i') }})</option>
                    </x-hrace009::select-with-popover>
                    <x-hrace009::label for="expire_date">{{ __('shop.fields.expire_date') }}</x-hrace009::label>
                </div>

                <!-- Submit Button -->
                <x-hrace009::button-with-popover class="w-auto" popover="{{ __('general.config_save_desc') }}">
                    {{ __('voucher.add') }}
                </x-hrace009::button-with-popover>
            </form>
        </div>
    </x-slot>
</x-hrace009.layouts.admin>
