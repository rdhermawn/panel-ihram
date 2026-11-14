@section('title', ' - ' . __('donate.title'))
<x-hrace009.layouts.app>
    <x-slot name="header">
        <div class="flex items-center justify-between px-4 py-4 border-b lg:py-6 dark:border-primary-darker">
            <h1 class="text-2xl font-semibold">{{ __('general.menu.donate.ipaymu') }}</h1>
        </div>
    </x-slot>

    <x-slot name="content">
        <form action="{{ route('app.donate.ipaymu.submit') }}" onsubmit="return donation_check();" method="post">
            {!! csrf_field() !!}

            @if(config('ipaymu.double'))
                <div class="flex flex-row mb-6 w-full bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded" role="alert">
                    <span class="block sm:inline">{!! __('donate.double_notice') !!}</span>
                </div>
            @endif

            @if(config('ipaymu.sandbox'))
                <div class="flex flex-row mb-6 w-full bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded" role="alert">
                    <span class="block sm:inline">{!! __('donate.sandboxActive') !!}</span>
                </div>
            @endif

            {{-- Coin Display --}}
            <div class="flex justify-center mb-6">
                <div class="flex items-center space-x-2">
                    <span id="donation_dollars_display" class="text-green-700 font-semibold text-sm">Rp 0</span>
                    <span class="text-sm font-medium">IDR</span>
                    <span class="text-sm">=</span>
                    <x-hrace009::input-with-popover id="donation_tokens" name="tokens"
                        type="number"
                        placeholder=" "
                        popover="{{ __('donate.ipaymu.amount_receive', ['currency' => config('pw-config.currency_name')]) }}"
                        readonly
                        class="w-20 px-2 py-1 text-sm text-center"
                    />
                    <span class="text-sm font-medium">Coin</span>
                    <span id="bonus_inline_display" class="text-green-600 text-sm font-medium"></span>
                    <input type="hidden" id="donation_dollars" name="dollars">
                </div>
            </div>

            {{-- Preset Amounts --}}
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-6">
                @foreach ([50, 100, 200, 500, 1000, 5000] as $amount)
                    <x-hrace009::button type="button" onclick="setDonation({{ $amount }})">
                        <span class="flex items-center gap-2">
                            💰 {{ $amount }}
                        </span>
                    </x-hrace009::button>
                @endforeach
            </div>

            {{-- Code Selection --}}
            <div x-data="{ selected: 'streamer' }" class="mb-6 space-y-4">
                <div class="flex justify-center space-x-6">
                    <label class="inline-flex items-center space-x-2">
                        <input type="radio" name="code_type" value="promo" x-model="selected" class="form-radio text-primary">
                        <span class="text-sm">Promo Code</span>
                    </label>
                    <label class="inline-flex items-center space-x-2">
                        <input type="radio" name="code_type" value="guild" x-model="selected" class="form-radio text-primary">
                        <span class="text-sm">Guild Code</span>
                    </label>
                    <label class="inline-flex items-center space-x-2">
                        <input type="radio" name="code_type" value="streamer" x-model="selected" class="form-radio text-primary">
                        <span class="text-sm">Streaming Code</span>
                    </label>
                </div>

                {{-- PROMO --}}
                <div x-show="selected === 'promo'" x-transition>
                    <x-hrace009::input-with-popover
                        id="promo_code"
                        name="promo_code"
                        type="text"
                        placeholder="Promo Code"
                        popover="Masukkan kode promo Anda"
                        class="w-full md:w-1/2 mx-auto text-center"
                        oninput="updateBonusDisplay()"
                    />
                </div>

                {{-- GUILD --}}
                <div x-show="selected === 'guild'" x-transition>
                    <x-hrace009::input-with-popover
                        id="guild_code"
                        name="guild_code"
                        type="text"
                        placeholder="Guild Code"
                        popover="Masukkan kode guild Anda"
                        class="w-full md:w-1/2 mx-auto text-center"
                        oninput="updateBonusDisplay()"
                    />
                </div>

                {{-- STREAMER --}}
                <div x-show="selected === 'streamer'" x-transition>
                    <x-hrace009::input-with-popover
                        id="streamer_code"
                        name="streamer_code"
                        type="text"
                        placeholder="Streaming Code"
                        popover="Masukkan kode streamer Anda"
                        class="w-full md:w-1/2 mx-auto text-center"
                        oninput="updateBonusDisplay()"
                    />
                </div>
            </div>

            {{-- Submit --}}
            <div class="flex flex-row mb-6 w-full justify-center">
                <x-hrace009::button type="submit" class="w-1/2">
                    {{ __('shop.buy') }}
                </x-hrace009::button>
            </div>
        </form>

        {{-- JS --}}
        <script>
            function setDonation(tokens) {
                const currencyPer = {{ config('ipaymu.currency_per') }};
                const dollars = Math.round(tokens / currencyPer);
                document.getElementById('donation_tokens').value = tokens;
                document.getElementById('donation_dollars').value = dollars;
                document.getElementById('donation_dollars_display').textContent = `Rp ${dollars.toLocaleString('id-ID')}`;
                updateBonusDisplay();
            }

            const promoCode = '{{ config('ipaymu.promo_code') }}';
            const promoBonusPercent = parseFloat('{{ config('ipaymu.promo_bonus_percent') }}');
            const guilds = @json(config('ipaymu.guild', []));
            const streamers = @json(config('ipaymu.streamers', []));
            const getMinBonus = parseFloat('{{ config('ipaymu.bonusess', 0) }}');
            const minGetBonus = parseInt('{{ config('ipaymu.mingetbonus', 0) }}');

            function getPromoBonus(tokens, code) {
                return (code?.trim().toLowerCase() === promoCode.toLowerCase())
                    ? Math.floor(tokens * promoBonusPercent / 100)
                    : 0;
            }

            function getGuildBonus(tokens, code) {
                code = (code || '').trim().toLowerCase();
                for (let g of guilds) {
                    if (code === (g.code_guild || '').toLowerCase()) {
                        return Math.floor(tokens * parseFloat(g.guild_bonus_percent) / 100);
                    }
                }
                return 0;
            }

            function getStreamerBonus(tokens, code) {
                code = (code || '').trim().toLowerCase();
                for (let s of streamers) {
                    if (code === (s.streamer_code || '').toLowerCase()) {
                        return Math.floor(tokens * parseFloat(s.streamer_bonus_percent) / 100);
                    }
                }
                return 0;
            }

            function updateBonusDisplay() {
                let tokens = parseInt(document.getElementById('donation_tokens').value) || 0;
                let promo = document.getElementById('promo_code')?.value || '';
                let guild = document.getElementById('guild_code')?.value || '';
                let streamer = document.getElementById('streamer_code')?.value || '';
                let selectedType = document.querySelector('input[name="code_type"]:checked')?.value;

                let codeBonus = 0;
                if (selectedType === 'promo') codeBonus = getPromoBonus(tokens, promo);
                if (selectedType === 'guild') codeBonus = getGuildBonus(tokens, guild);
                if (selectedType === 'streamer') codeBonus = getStreamerBonus(tokens, streamer);

                let bonusMin = (getMinBonus > 0 && tokens >= minGetBonus) ? Math.floor(tokens * getMinBonus / 100) : 0;
                let total = codeBonus + bonusMin;

                document.getElementById('bonus_inline_display').textContent = total > 0 ? `+ bonus ${total} Coin` : '';
            }

            window.addEventListener('DOMContentLoaded', updateBonusDisplay);
        </script>
    </x-slot>
</x-hrace009.layouts.app>
