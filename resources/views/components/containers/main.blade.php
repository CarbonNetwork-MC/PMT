@props(['rounded' => 'lg', 'padding' => '6', 'color' => 'white', 'darkColor' => 'gray-800'])

<div
    {{ $attributes->merge([
        'class' => "p-{$padding} bg-{$color} dark:bg-{$darkColor} rounded-{$rounded} shadow-sm"
    ]) }}
>
    {{ $slot }}
</div>