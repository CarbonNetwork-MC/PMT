@props(['active'])

@php
    $classes = ($active ?? false)
        ? 'flex items-center mt-1 ps-6 py-2 text-white rounded-lg bg-carbon-600 group cursor-pointer'
        : 'flex items-center mt-1 ps-6 py-2 text-white rounded-lg hover:bg-carbon-800 group cursor-pointer';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>