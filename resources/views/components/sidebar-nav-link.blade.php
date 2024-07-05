@props(['active'])

@php
$classes = ($active ?? false)
            ? 'flex items-center p-2 text-white rounded-lg bg-carbon-500 group'
            : 'flex items-center p-2 text-white rounded-lg hover:bg-carbon-700 group';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
