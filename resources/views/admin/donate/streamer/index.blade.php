@section('title', ' - ' . __('donate.streamer.title'))
<x-hrace009.layouts.admin>
    <x-slot name="header">
        <div class="flex items-center justify-between px-4 py-4 border-b lg:py-6 dark:border-primary-darker">
            <h1 class="text-2xl font-semibold">{{ __('donate.streamer.title') }}</h1>
        </div>
    </x-slot>
    <x-slot name="content">
        <x-hrace009::admin.validation-error/>
        <div class="mx-4 my-6 md:mx-6">
            <div class="bg-white dark:bg-darker rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-200">{{ __('donate.streamer.add') }}</h2>
                <form method="post" action="{{ route('admin.donate.streamer.store') }}">
                    @csrf
                    @include('admin.donate.streamer.fields', ['streamer' => null])
                    <div class="mt-6">
                        <x-hrace009::button>{{ __('donate.streamer.add') }}</x-hrace009::button>
                    </div>
                </form>
            </div>

            <div class="bg-white dark:bg-darker rounded-lg shadow-md overflow-hidden border dark:border-primary-darker">
                @if ($streamers->isEmpty())
                    <p class="p-6 text-sm text-gray-600 dark:text-gray-300">{{ __('donate.streamer.empty') }}</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-200 text-gray-700 dark:bg-darker dark:text-light border-b dark:border-primary-darker uppercase text-xs">
                                <tr>
                                    <th scope="col" class="px-3 py-3 text-left border-r dark:border-primary-darker">{{ __('donate.streamer.name') }}</th>
                                    <th scope="col" class="px-3 py-3 text-left border-r dark:border-primary-darker">{{ __('donate.streamer.code') }}</th>
                                    <th scope="col" class="px-3 py-3 text-center border-r dark:border-primary-darker whitespace-nowrap">{{ __('donate.streamer.bonus_percent') }}</th>
                                    <th scope="col" class="px-3 py-3 text-center border-r dark:border-primary-darker whitespace-nowrap">{{ __('donate.streamer.user_id') }}</th>
                                    <th scope="col" class="px-3 py-3 text-left border-r dark:border-primary-darker">{{ __('donate.streamer.account') }}</th>
                                    <th scope="col" class="px-3 py-3 text-center">{{ __('donate.streamer.action') }}</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-700 dark:text-light">
                                @foreach ($streamers as $streamer)
                                    <tr class="border-b dark:border-primary-darker">
                                        <td class="px-3 py-2 align-middle whitespace-nowrap border-r dark:border-primary-darker font-medium">{{ $streamer->name }}</td>
                                        <td class="px-3 py-2 align-middle whitespace-nowrap border-r dark:border-primary-darker font-mono">{{ $streamer->code }}</td>
                                        <td class="px-3 py-2 align-middle whitespace-nowrap border-r dark:border-primary-darker text-center">{{ $streamer->bonus_percent + 0 }}%</td>
                                        <td class="px-3 py-2 align-middle whitespace-nowrap border-r dark:border-primary-darker text-center">{{ $streamer->user_id }}</td>
                                        <td class="px-3 py-2 align-middle whitespace-nowrap border-r dark:border-primary-darker">
                                            @if ($streamer->user)
                                                {{ $streamer->user->name }}
                                            @else
                                                <span class="px-2 py-1 rounded bg-red-600 text-white text-xs">{{ __('donate.streamer.no_account') }}</span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-2 align-middle whitespace-nowrap">
                                            <div class="flex items-center justify-center">
                                                <a href="{{ route('admin.donate.streamer.edit', $streamer) }}"
                                                   class="mr-2 px-4 py-2 text-base font-medium text-center text-white transition-colors duration-200 rounded-md bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-1 dark:focus:ring-offset-darker">{{ __('donate.streamer.edit') }}</a>
                                                <form method="post" action="{{ route('admin.donate.streamer.destroy', $streamer) }}"
                                                      onsubmit="return confirm(@js(__('donate.streamer.delete_confirm', ['name' => $streamer->name, 'code' => $streamer->code])))">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="px-4 py-2 text-base font-medium text-center text-white transition-colors duration-200 rounded-md bg-red-600 hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-1 dark:focus:ring-offset-darker">{{ __('general.delete') }}</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </x-slot>
</x-hrace009.layouts.admin>
