@props([
    'margin' => 'my-2',
])

<hr
    {{ $attributes->merge([
        'class' => ($margin ?? 'my-2') . ' border-t border-zinc-400',
    ]) }}
/>