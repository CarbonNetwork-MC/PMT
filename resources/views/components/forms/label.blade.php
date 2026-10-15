@props(['required' => false])

<label {{ $attributes->merge([
    'class' => "block text-sm font-medium text-gray-700 dark:text-white mb-2.5"
]) }}>
    {{ $slot }}
    @if ($required) <span class="text-red-500">*</span> @endif
</label>