@props(['id' => 'per-page-select'])
@php
    $wrapperClasses = $attributes->get('wrapper:class', '');
@endphp

<div class="{{ $wrapperClasses }}">
    <select
        id="{{ $id }}"
        {{ $attributes->class([
            'block w-full bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand shadow-xs placeholder:text-body px-3 py-2 cursor-pointer'
        ]) }}
    >
        {{ $slot }}
    </select>
</div>