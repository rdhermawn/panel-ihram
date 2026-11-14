@section('title', ' - ' . __('chat.title'))
<x-hrace009.layouts.admin>
    <x-slot name="header">
        <div class="flex items-center justify-between px-4 py-4 border-b lg:py-6 dark:border-primary-darker">
            <h1 class="text-2xl font-semibold">{{ __('manage.watch') }}</h1>
        </div>
    </x-slot>
    <x-slot name="content">
        <div class="max-w mx-auto mx-6 my-6">
            <div class="flex flex-wrap items-center gap-3 mb-4">
                <x-hrace009::button id="chat_refresh" name="chat_refresh" type="button" class="w-auto">
                    {{ __('manage.buttons.refresh') }}
                </x-hrace009::button>
                <span class="text-xs opacity-70">Live Chat kompatibel panel lama — database tidak diubah.</span>
            </div>
            <div class="overflow-x-auto live-chat">
                <table id="live_chat" class="w-full table table-auto px-2 py-2 text-left">
                    <thead>
                    <tr>
                        <th class="border-b dark:border-primary-light">Waktu</th>
                        <th class="border-b dark:border-primary-light">Karakter</th>
                        <th class="border-b dark:border-primary-light">Role ID</th>
                        <th class="border-b dark:border-primary-light">Channel</th>
                        <th class="border-b dark:border-primary-light">Target</th>
                        <th class="border-b dark:border-primary-light">Pesan</th>
                    </tr>
                    </thead>
                    <tbody class="reverse"></tbody>
                </table>
            </div>
        </div>
    </x-slot>
</x-hrace009.layouts.admin>
