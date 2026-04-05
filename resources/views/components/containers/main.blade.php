@props(['rounded' => 'lg', 'padding' => '6'])

<div
    {{ $attributes->merge([
        'class' => "p-{$padding} bg-white dark:bg-gray-800 rounded-{$rounded} shadow-sm"
    ]) }}
>
    {{ $slot }}
</div>