@section('title', ' - ' . __('donate.streamer.edit_title'))
<x-hrace009.layouts.admin>
    <x-slot name="header">
        <div class="flex items-center justify-between px-4 py-4 border-b lg:py-6 dark:border-primary-darker">
            <h1 class="text-2xl font-semibold">{{ __('donate.streamer.edit_title') }}: {{ $streamer->name }}</h1>
        </div>
    </x-slot>
    <x-slot name="content">
        <x-hrace009::admin.validation-error/>
        <div class="mx-4 my-6 md:mx-6">
            <div class="bg-white dark:bg-darker rounded-lg shadow-md p-6">
                <form method="post" action="{{ route('admin.donate.streamer.update', $streamer) }}">
                    @csrf
                    @method('PATCH')
                    @include('admin.donate.streamer.fields')
                    <div class="mt-6 flex items-center">
                        <a href="{{ route('admin.donate.streamer.index') }}"
                           class="mr-4 px-4 py-2 rounded-md font-medium text-gray-700 dark:text-gray-200 hover:underline focus:outline-none focus:ring-2 focus:ring-primary">{{ __('donate.streamer.cancel') }}</a>
                        <x-hrace009::button>{{ __('donate.streamer.save') }}</x-hrace009::button>
                    </div>
                </form>
            </div>
        </div>
    </x-slot>
</x-hrace009.layouts.admin>
