@props(['id' => 'toggle', 'label' => '', 'labelExtra' => '', 'color' => '', 'required' => false, 'disabled' => false])
@php
    $labelAttributes = $attributes->only(['label:class']);
    $extraLabelAttributes = $attributes->only(['labelExtra:class']);
@endphp

<label {{ $attributes->class([
    'inline-flex items-center cursor-pointer'
]) }}>
    {{-- Extra label --}}
    @if ($labelExtra)
        <span class="select-none me-3 text-sm font-medium {{ $disabled ? 'text-gray-500 dark:text-gray-400' : 'text-heading' }} {{ $extraLabelAttributes->get('labelExtra:class') }}">
            {{ $labelExtra }}
        </span>
    @endif

    {{-- Checkbox --}}
    <input 
        type="checkbox" 
        id="{{ $id }}" 
        @if($required) required @endif 
        @if($disabled) disabled @endif
        class="sr-only peer"
    />

    <div class="relative w-9 h-5 {{ $disabled ? 'bg-gray-300': 'bg-gray-400' }} peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-brand-soft dark:peer-focus:ring-brand-soft rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-buffer after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-{{ $color ? $color : 'brand' }}"></div>

    {{-- Label --}}
    @if ($label)
        <span class="select-none ms-3 text-sm font-medium {{ $disabled ? 'text-gray-500 dark:text-gray-400' : 'text-heading' }} {{ $labelAttributes->get('label:class') }}">
            {{ $label }}
            @if ($required) <span class="text-red-400">*</span> @endif
        </span>
    @endif
</label>