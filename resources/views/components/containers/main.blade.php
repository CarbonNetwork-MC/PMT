@props(['rounded' => 'lg'])

<div
    {{ $attributes->merge([
        'class' => "p-6 bg-white dark:bg-gray-800 rounded-{$rounded} shadow-sm"
    ]) }}
>
    {{ $slot }}
</div>