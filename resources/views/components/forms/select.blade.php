@props(['id' => 'select', 'label' => '', 'size' => '', 'required' => false, 'disabled' => false])
@php
    $sizeClasses = match($size) {
        'large' => 'px-3.5 py-3',
        'extra-large' => 'px-4 py-3.5',
        default => 'px-3 py-2.5',
    };

    $wrapperAttributes = $attributes->only(['wrapper:class']);
    $labelAttributes = $attributes->only(['label:class']);
@endphp

<div class="max-w-sm {{ $wrapperAttributes->get('wrapper:class') }}">
    {{-- Label --}}
    @if ($label)
        <label
            for="{{ $id }}"
            class="block mb-2.5 text-sm font-medium text-heading {{ $labelAttributes->get('label:class') }}"
        >
            {{ $label }}
            @if ($required) <span class="text-red-400">*</span> @endif
        </label>
    @endif
    
    {{-- Select --}}
    <select 
        id="{{ $id }}" 
        @if($required) required @endif
        @if($disabled) disabled @endif
        {{ $attributes->class([
            'block w-full bg-gray-100 border border-default-medium text-black text-sm rounded-base focus:ring-brand focus:border-brand shadow-xs placeholder:text-body ' . $sizeClasses
        ]) }}
    >
        {{ $slot }}
    </select>
</div>