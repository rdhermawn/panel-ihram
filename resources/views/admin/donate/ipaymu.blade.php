@section('title', ' - ' . __('donate.title'))
<x-hrace009.layouts.admin>
    <x-slot name="header">
        <div class="flex items-center justify-between px-4 py-4 border-b lg:py-6 dark:border-primary-darker">
            <h1 class="text-2xl font-semibold">{{ __('donate.ipaymu.title') }}</h1>
        </div>
    </x-slot>
    <x-slot name="content">
        <div class="max-w-2xl mx-auto mt-6">
            <x-hrace009::admin.validation-error/>
            <form method="post" action="{{ route('admin.donate.ipaymu.post') }}">
                {!! csrf_field() !!}
                <div class="relative z-0 mb-6 w-full group">
                    <div id="status_switch" class="flex ml-12">
                        <div class="pretty p-switch">
                            <input type="checkbox" id="status" name="status"
                                   value="{{ config('ipaymu.status') }}"
                                   @if( config('ipaymu.status') === true ) checked @endif
                                @popper({{ __('donate.ipaymu.status_desc') }})
                            />
                            <div class="state p-info">
                                <label for="status">
                                    @if( config('ipaymu.status') === true )
                                        {{ __('donate.on') }}
                                    @else
                                        {{ __('donate.off') }}
                                    @endif</label>
                            </div>
                        </div>
                    </div>
                    <x-hrace009::label for="status_switch">{{ __('donate.status') }}</x-hrace009::label>
                </div>
                @if( config('ipaymu.status') === true )
                    <div class="relative z-0 mb-6 w-full group">
                        <div id="sandbox_switch" class="flex ml-12">
                            <div class="pretty p-switch">
                                <input type="checkbox" id="sandbox" name="sandbox"
                                       value="{{ config('ipaymu.sandbox') }}"
                                       @if( config('ipaymu.sandbox') === true ) checked @endif
                                    @popper({{ __('donate.ipaymu.sandbox_desc') }})
                                />
                                <div class="state p-info">
                                    <label for="sandbox">
                                        @if( config('ipaymu.sandbox') === true )
                                            {{ __('donate.on') }}
                                        @else
                                            {{ __('donate.off') }}
                                        @endif</label>
                                </div>
                            </div>
                        </div>
                        <x-hrace009::label for="status_switch">{{ __('donate.sandbox') }}</x-hrace009::label>
                    </div>
                    <div class="relative z-0 mb-6 w-full group">
                        <div id="double_switch" class="flex ml-12">
                            <div class="pretty p-switch">
                                <input type="checkbox" id="double" name="double"
                                       value="{{ config('ipaymu.double') }}"
                                       @if( config('ipaymu.double') === true ) checked @endif
                                    @popper({{ __('donate.ipaymu.double_desc') }})
                                />
                                <div class="state p-info">
                                    <label for="status">
                                        @if( config('ipaymu.double') === true )
                                            {{ __('donate.on') }}
                                        @else
                                            {{ __('donate.off') }}
                                        @endif</label>
                                </div>
                            </div>
                        </div>
                        <x-hrace009::label for="double_switch">{{ __('donate.ipaymu.double') }}</x-hrace009::label>
                    </div>
                    <div class="relative z-0 mb-6 w-full group">
                        <x-hrace009::input-with-popover id="va" name="va"
                                                        value="{{ config('ipaymu.va') }}"
                                                        placeholder=" " :popover="__('donate.ipaymu.va_desc')"/>
                        <x-hrace009::label for="va">{{ __('donate.ipaymu.va') }}</x-hrace009::label>
                    </div>
                    <div class="relative z-0 mb-6 w-full group">
                        <x-hrace009::input-with-popover id="key" name="key"
                                                        value="{{ config('ipaymu.key') }}"
                                                        placeholder=" " :popover="__('donate.ipaymu.apikey_desc')"/>
                        <x-hrace009::label for="secret">{{ __('donate.ipaymu.apikey') }}</x-hrace009::label>
                    </div>
                    <div class="flex flex-row gap-8">
                        <div class="relative z-0 mb-6 w-full group">
                            <x-hrace009::input-with-popover id="refid" name="refid"
                                                            value="{{ config('ipaymu.refid') }}"
                                                            placeholder=" "
                                                            :popover="__('donate.ipaymu.refid_desc')"/>
                            <x-hrace009::label
                                for="refid">{{ __('donate.ipaymu.refid') }}</x-hrace009::label>
                        </div>
                        <div class="relative z-0 mb-6 w-full group">
                            <x-hrace009::input-with-popover id="currency_per" name="currency_per"
                                                            value="{{ config('ipaymu.currency_per') }}"
                                                            placeholder=" "
                                                            :popover="__('donate.ipaymu.currency_per_desc', ['coin' => config('pw-config.currency_name'), 'currency' => 'IDR' ])"/>
                            <x-hrace009::label
                                for="currency_per">{{ __('donate.ipaymu.currency_per', ['coin' => config('pw-config.currency_name'),'currency' => 'IDR' ]) }}</x-hrace009::label>
                        </div>
                        <div class="relative z-0 mb-6 w-full group">
                            <x-hrace009::input-with-popover id="maximum" name="maximum"
                                                            value="{{ config('ipaymu.maximum') }}"
                                                            placeholder=" "
                                                            :popover="__('donate.ipaymu.maximum_desc')"/>
                            <x-hrace009::label for="maximum">{{ __('donate.ipaymu.maximum') }}</x-hrace009::label>
                        </div>
                        <div class="relative z-0 mb-6 w-full group">
                            <x-hrace009::input-with-popover id="minimum" name="minimum"
                                                            value="{{ config('ipaymu.minimum') }}"
                                                            placeholder=" "
                                                            :popover="__('donate.ipaymu.minimum_desc')"/>
                            <x-hrace009::label for="minimum">{{ __('donate.ipaymu.minimum') }}</x-hrace009::label>
                        </div>
                    </div>
                    <div class="flex flex-row gap-8">
                        <div class="relative z-0 mb-6 w-full group">
                            <x-hrace009::input-with-popover id="mingetbonus" name="mingetbonus"
                                                            value="{{ config('ipaymu.mingetbonus') }}"
                                                            placeholder=" "
                                                            :popover="__('donate.ipaymu.mingetbonus_desc', ['currency' => config('pw-config.currency_name')])"/>
                            <x-hrace009::label
                                for="mingetbonus">{{ __('donate.ipaymu.mingetbonus') }}</x-hrace009::label>
                        </div>
                        =>
                        <div class="relative z-0 mb-6 w-full group">
                            <x-hrace009::input-with-popover id="bonusess" name="bonusess"
                                                            value="{{ config('ipaymu.bonusess') }}"
                                                            placeholder=" "
                                                            :popover="__('donate.ipaymu.bonusess_desc')"/>
                            <x-hrace009::label for="bonusess">{{ __('donate.ipaymu.bonusess') }}</x-hrace009::label>
                        </div>
                    </div>
                    <div class="flex flex-row gap-8">
                        <div class="relative z-0 mb-6 w-full group">
                            <x-hrace009::input-with-popover id="route" name="route"
                                                            value="{{ config('ipaymu.route') }}"
                                                            placeholder=" "
                                                            :popover="__('donate.ipaymu.route_desc')"/>
                            <x-hrace009::label
                                for="route">{{ __('donate.ipaymu.route') }}</x-hrace009::label>
                        </div>
                    </div>

<div class="flex flex-row gap-8">
    <div class="relative z-0 mb-6 w-full group">
        <x-hrace009::input-with-popover id="promo_code" name="promo_code"
                                        value="{{ config('ipaymu.promo_code') }}"
                                        placeholder=" "
                                        :popover="__('donate.ipaymu.promo_code_desc')"/>
        <x-hrace009::label for="promo_code">{{ __('donate.ipaymu.promo_code') }}</x-hrace009::label>
    </div>
</div>

<div class="flex flex-row gap-8">
    <div class="relative z-0 mb-6 w-full group">
        <x-hrace009::input-with-popover id="promo_bonus_percent" name="promo_bonus_percent"
                                        value="{{ config('ipaymu.promo_bonus_percent') }}"
                                        placeholder=" "
                                        :popover="__('donate.ipaymu.promo_bonus_percent_desc')"/>
        <x-hrace009::label for="promo_bonus_percent">{{ __('donate.ipaymu.promo_bonus_percent') }}</x-hrace009::label>
    </div>
</div>

                    {{-- Broadcast in-game --}}
                    <h2 class="mt-4 mb-6 text-lg font-semibold">{{ __('donate.ipaymu.broadcast_title') }}</h2>
                    <div class="relative z-0 mb-6 w-full group">
                        <div id="broadcast_enabled_switch" class="flex ml-12 pt-5">
                            <div class="pretty p-switch">
                                <input type="checkbox" id="broadcast_enabled" name="broadcast_enabled"
                                       @if( config('ipaymu.broadcast_enabled') === true ) checked @endif
                                    @popper({{ __('donate.ipaymu.broadcast_enabled_desc') }})
                                />
                                <div class="state p-info">
                                    <label for="broadcast_enabled">
                                        @if( config('ipaymu.broadcast_enabled') === true )
                                            {{ __('donate.on') }}
                                        @else
                                            {{ __('donate.off') }}
                                        @endif</label>
                                </div>
                            </div>
                        </div>
                        <x-hrace009::label for="broadcast_enabled_switch">{{ __('donate.ipaymu.broadcast_enabled') }}</x-hrace009::label>
                    </div>
                    <div class="flex flex-row gap-8">
                        <div class="relative z-0 mb-6 w-1/4 group">
                            <x-hrace009::input-with-popover id="broadcast_channel" name="broadcast_channel" type="number"
                                                            value="{{ config('ipaymu.broadcast_channel') ?: 9 }}"
                                                            placeholder=" "
                                                            :popover="__('donate.ipaymu.broadcast_channel_desc')"/>
                            <x-hrace009::label for="broadcast_channel">{{ __('donate.ipaymu.broadcast_channel') }}</x-hrace009::label>
                        </div>
                        <div class="relative z-0 mb-6 w-full group">
                            <x-hrace009::input-with-popover id="broadcast_message" name="broadcast_message" maxlength="255"
                                                            value="{{ config('ipaymu.broadcast_message') }}"
                                                            placeholder=" "
                                                            :popover="__('donate.ipaymu.broadcast_message_desc')"/>
                            <x-hrace009::label for="broadcast_message">{{ __('donate.ipaymu.broadcast_message') }}</x-hrace009::label>
                        </div>
                    </div>
                    <div class="mb-6 text-xs text-gray-500">
                        Placeholder: <code>{player_name}</code> <code>{amount}</code> <code>{coin}</code> <code>{currency}</code> <code>{payment_method}</code>
                        &mdash; {{ __('donate.ipaymu.template_default') }}
                        <code>{{ \App\Services\IpaymuBroadcastService::DEFAULT_MESSAGE }}</code>
                    </div>

                    {{-- Discord --}}
                    <h2 class="mt-4 mb-6 text-lg font-semibold">{{ __('donate.ipaymu.discord_title') }}</h2>
                    <div class="flex flex-row gap-8">
                        <div class="relative z-0 mb-6 w-full group">
                            <div id="discord_enabled_switch" class="flex ml-12 pt-5">
                                <div class="pretty p-switch">
                                    <input type="checkbox" id="discord_enabled" name="discord_enabled"
                                           @if( config('ipaymu.discord_enabled') === true ) checked @endif
                                        @popper({{ __('donate.ipaymu.discord_enabled_desc') }})
                                    />
                                    <div class="state p-info">
                                        <label for="discord_enabled">
                                            @if( config('ipaymu.discord_enabled') === true )
                                                {{ __('donate.on') }}
                                            @else
                                                {{ __('donate.off') }}
                                            @endif</label>
                                    </div>
                                </div>
                            </div>
                            <x-hrace009::label for="discord_enabled_switch">{{ __('donate.ipaymu.discord_enabled') }}</x-hrace009::label>
                        </div>
                        <div class="relative z-0 mb-6 w-full group">
                            <div id="discord_mention_everyone_switch" class="flex ml-12 pt-5">
                                <div class="pretty p-switch">
                                    <input type="checkbox" id="discord_mention_everyone" name="discord_mention_everyone"
                                           @if( config('ipaymu.discord_mention_everyone') === true ) checked @endif
                                        @popper({{ __('donate.ipaymu.discord_mention_everyone_desc') }})
                                    />
                                    <div class="state p-info">
                                        <label for="discord_mention_everyone">
                                            @if( config('ipaymu.discord_mention_everyone') === true )
                                                {{ __('donate.on') }}
                                            @else
                                                {{ __('donate.off') }}
                                            @endif</label>
                                    </div>
                                </div>
                            </div>
                            <x-hrace009::label for="discord_mention_everyone_switch">{{ __('donate.ipaymu.discord_mention_everyone') }}</x-hrace009::label>
                        </div>
                    </div>
                    <div class="relative z-0 mb-6 w-full group">
                        <x-hrace009::input-with-popover id="discord_webhook_url" name="discord_webhook_url" type="url"
                                                        value="{{ config('ipaymu.discord_webhook_url') }}"
                                                        placeholder=" "
                                                        :popover="__('donate.ipaymu.discord_webhook_url_desc')"/>
                        <x-hrace009::label for="discord_webhook_url">{{ __('donate.ipaymu.discord_webhook_url') }}</x-hrace009::label>
                    </div>
                    <div class="relative z-0 mb-6 w-full group">
                        <x-hrace009::input-with-popover id="discord_message" name="discord_message" maxlength="1000"
                                                        value="{{ config('ipaymu.discord_message') }}"
                                                        placeholder=" "
                                                        :popover="__('donate.ipaymu.discord_message_desc')"/>
                        <x-hrace009::label for="discord_message">{{ __('donate.ipaymu.discord_message') }}</x-hrace009::label>
                    </div>
                    <div class="mb-6 text-xs text-gray-500">
                        Placeholder: <code>{player_name}</code> <code>{amount}</code> <code>{streamer}</code> <code>{reference_id}</code> <code>{payment_method}</code>
                        &mdash; {{ __('donate.ipaymu.discord_note') }}
                    </div>

                    <div class="flex flex-row gap-8">
                        <code>Your current Route for Callback is: {{ route('api.ipaymu') }}</code>
                    </div>
                    <div class="flex flex-row gap-8">
                        <code>NOTE: Please change this route regular to avoid hacking or injection </code>
                    </div>
                @endif

                <!-- Submit Button -->
                <x-hrace009::button-with-popover class="w-auto" popover="{{ __('general.config_save_desc') }}">
                    {{ __('general.Save') }}
                </x-hrace009::button-with-popover>
            </form>
        </div>
    </x-slot>
</x-hrace009.layouts.admin>
