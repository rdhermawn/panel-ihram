@section('title', ' - ' . __('voucher.title'))
<x-hrace009.layouts.admin>
    <x-slot name="header">
        <div class="flex items-center justify-between px-4 py-4 border-b lg:py-6 dark:border-primary-darker">
            <h1 class="text-2xl font-semibold">{{ __('voucher.edit') }}</h1>
        </div>
    </x-slot>
    <x-slot name="content">
        <div class="max-w mx-auto mt-6 ml-6 mr-6">
            <x-hrace009::admin.validation-error/>
            <form method="post" action="{{ route('voucher.update', $voucher->code) }}">
    {!! csrf_field() !!}
    @method('PATCH')

    <div class="relative z-0 mb-6 w-full group">
        <x-hrace009::input-with-popover id="code" name="code"
                                        placeholder=" " readonly
                                        value="{{ $voucher->code }}"
                                        :popover="__('voucher.code_desc')"/>
        <x-hrace009::label for="code">{{ __('voucher.code') }}</x-hrace009::label>
    </div>

    <div class="relative z-0 mb-6 w-full group">
        <x-hrace009::input-with-popover id="amount" name="amount"
                                        placeholder=" "
                                        value="{{ $voucher->amount }}"
                                        :popover="__('voucher.amount_desc', ['currency' => config('pw-config.currency_name')])"/>
        <x-hrace009::label for="amount">{{ __('voucher.amount', ['currency' => config('pw-config.currency_name')]) }}</x-hrace009::label>
    </div>

    <div class="relative z-0 mb-6 w-full group">
        <x-hrace009::input-with-popover id="max_redeem" name="max_redeem"
                                        placeholder=" "
                                        type="number"
                                        min="1"
                                        value="{{ $voucher->max_redeem }}"
                                        :popover="__('voucher.max_redeem_desc')"/>
        <x-hrace009::label for="max_redeem">{{ __('voucher.max_redeem') }}</x-hrace009::label>
    </div>

    <div class="relative z-0 mb-6 w-full group">
        <x-hrace009::input-with-popover id="name" name="name"
                                        type="text" value="{{ $voucher->name }}"
                                        popover="{{ __('shop.fields.name_desc') }}"/>
        <x-hrace009::label for="name">{{ __('shop.fields.name') }}</x-hrace009::label>
    </div>

    <div class="relative z-0 mb-6 w-full group">
        <x-hrace009::input-with-popover id="item_id" name="item_id"
                                        type="text" value="{{ $voucher->item_id }}"
                                        popover="{{ __('shop.fields.item_id_desc') }}"/>
        <x-hrace009::label for="item_id">{{ __('shop.fields.item_id') }}</x-hrace009::label>
    </div>

    <div class="relative z-0 mb-6 w-full group">
        <x-hrace009::input-with-popover id="octet" name="octet"
                                        type="text" value="{{ $voucher->octet }}"
                                        popover="{{ __('shop.fields.octet_desc') }}"/>
        <x-hrace009::label for="octet">{{ __('shop.fields.octet') }}</x-hrace009::label>
    </div>

    <div class="relative z-0 mb-6 w-full group">
        <x-hrace009::select-with-popover id="mask" name="mask"
                                         popover="{{ __('shop.fields.mask_desc') }}">
            <option class="dark:text-gray-500" value=""> - </option>
            @foreach($masks as $group => $mask)
                @if(is_array($mask))
                    <optgroup class="dark:text-gray-500" label="{{ $group }}">
                        @foreach($mask as $item => $value)
                            <option class="dark:text-gray-500"
                                    value="{{ $item }}" {{ $item == $voucher->mask ? 'selected' : '' }}>
                                {{ $value }}
                            </option>
                        @endforeach
                    </optgroup>
                @else
                    <option class="dark:text-gray-500"
                            value="{{ $group }}" {{ $group == $voucher->mask ? 'selected' : '' }}>
                        {{ $mask }}
                    </option>
                @endif
            @endforeach
        </x-hrace009::select-with-popover>
        <x-hrace009::label for="mask">{{ __('shop.fields.mask') }}</x-hrace009::label>
    </div>

    <div class="relative z-0 mb-6 w-full group">
        <x-hrace009::input-with-popover id="count" name="count"
                                        type="text" value="{{ $voucher->count }}"
                                        popover="{{ __('shop.fields.count_desc') }}"/>
        <x-hrace009::label for="count">{{ __('shop.fields.count') }}</x-hrace009::label>
    </div>

    <div class="relative z-0 mb-6 w-full group">
        <x-hrace009::input-with-popover id="max_count" name="max_count"
                                        type="text" value="{{ $voucher->max_count }}"
                                        popover="{{ __('shop.fields.max_count_desc') }}"/>
        <x-hrace009::label for="max_count">{{ __('shop.fields.max_count') }}</x-hrace009::label>
    </div>

    <div class="relative z-0 mb-6 w-full group">
        <x-hrace009::input-with-popover id="protection_type" name="protection_type"
                                        type="text" value="{{ $voucher->protection_type }}"
                                        popover="{{ __('shop.fields.protection_type_desc') }}"/>
        <x-hrace009::label for="protection_type">{{ __('shop.fields.protection_type') }}</x-hrace009::label>
    </div>

    <div class="relative z-0 mb-6 w-full group">
<div class="relative z-0 mb-6 w-full group">
    <x-hrace009::select-with-popover popover="{{ __('shop.fields.expire_date_desc') }}" id="expire_date" name="expire_date">
        <option class="dark:text-gray-500" value="0" {{ $voucher->expire_date == 0 ? 'selected' : '' }}>-</option>
        <option class="dark:text-gray-500" value="{{ now()->addDay()->timestamp }}" {{ $voucher->expire_date == now()->addDay()->timestamp ? 'selected' : '' }}>1 Day (until {{ now()->addDay()->format('Y-m-d H:i') }})</option>
        <option class="dark:text-gray-500" value="{{ now()->addDays(2)->timestamp }}" {{ $voucher->expire_date == now()->addDays(2)->timestamp ? 'selected' : '' }}>2 Days (until {{ now()->addDays(2)->format('Y-m-d H:i') }})</option>
        <option class="dark:text-gray-500" value="{{ now()->addDays(3)->timestamp }}" {{ $voucher->expire_date == now()->addDays(3)->timestamp ? 'selected' : '' }}>3 Days (until {{ now()->addDays(3)->format('Y-m-d H:i') }})</option>
        <option class="dark:text-gray-500" value="{{ now()->addWeek()->timestamp }}" {{ $voucher->expire_date == now()->addWeek()->timestamp ? 'selected' : '' }}>7 Days (until {{ now()->addWeek()->format('Y-m-d H:i') }})</option>
        <option class="dark:text-gray-500" value="{{ now()->addMonth()->timestamp }}" {{ $voucher->expire_date == now()->addMonth()->timestamp ? 'selected' : '' }}>30 Days (until {{ now()->addMonth()->format('Y-m-d H:i') }})</option>
    </x-hrace009::select-with-popover>
    <x-hrace009::label for="expire_date">{{ __('shop.fields.expire_date') }}</x-hrace009::label>
</div>

    <x-hrace009::button-with-popover class="w-auto" popover="{{ __('voucher.update_desc') }}">
        {{ __('voucher.update') }}
    </x-hrace009::button-with-popover>
</form>
        </div>
    </x-slot>
</x-hrace009.layouts.admin>
