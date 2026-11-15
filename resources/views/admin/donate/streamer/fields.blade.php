@php($inputClass = 'w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-primary bg-white text-black dark:bg-gray-800 dark:text-white')
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('donate.streamer.name') }}</label>
        <input id="name" name="name" type="text" required maxlength="50"
               value="{{ old('name', $streamer->name ?? '') }}" class="{{ $inputClass }}">
    </div>
    <div>
        <label for="user_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('donate.streamer.user_id') }}</label>
        <input id="user_id" name="user_id" type="number" required min="1" inputmode="numeric"
               aria-describedby="user_id_desc"
               value="{{ old('user_id', $streamer->user_id ?? '') }}" class="{{ $inputClass }}">
        <p id="user_id_desc" class="mt-1 text-xs text-gray-600 dark:text-gray-300">{{ __('donate.streamer.user_id_desc') }}</p>
    </div>
    <div>
        <label for="code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('donate.streamer.code') }}</label>
        <input id="code" name="code" type="text" required maxlength="30" pattern="[A-Za-z0-9_\-]+"
               aria-describedby="code_desc" autocomplete="off"
               value="{{ old('code', $streamer->code ?? '') }}" class="{{ $inputClass }}">
        <p id="code_desc" class="mt-1 text-xs text-gray-600 dark:text-gray-300">{{ __('donate.streamer.code_desc') }}</p>
    </div>
    <div>
        <label for="bonus_percent" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('donate.streamer.bonus_percent') }}</label>
        <input id="bonus_percent" name="bonus_percent" type="number" required min="0" max="100" step="0.01"
               aria-describedby="bonus_percent_desc"
               value="{{ old('bonus_percent', isset($streamer) ? $streamer->bonus_percent + 0 : '') }}" class="{{ $inputClass }}">
        <p id="bonus_percent_desc" class="mt-1 text-xs text-gray-600 dark:text-gray-300">{{ __('donate.streamer.bonus_percent_desc') }}</p>
    </div>
</div>
