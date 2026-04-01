@props(['id' => 'checkbox', 'label' => '', 'checked' => false, 'disabled' => false, 'required' => false])
@php
    $wrapperAttributes = $attributes->only(['wrapper:class']);
    $labelAttributes = $attributes->only(['label:class']);
@endphp

<div class="flex items-center {{ $wrapperAttributes->get('wrapper:class') }}">
    {{-- Checkbox --}}
    <input 
        type="checkbox" 
        id="{{ $id }}" 
        @if($checked) checked @endif
        @if($disabled) disabled @endif
        @if($required) required @endif
        {{ $attributes->class([
            'w-4 h-4 border border-default-medium rounded-xs bg-neutral-secondary-medium focus:ring-2 focus:ring-brand-soft',
        ]) }}
    />

    {{-- Label --}}
    @if ($label)
        <label 
            for="{{ $id }}" 
            class="select-none ms-2 text-sm font-medium text-heading {{ $labelAttributes->get('label:class') }}"
        >
            {{ $label }}
            @if ($required) <span class="text-red-400">*</span> @endif
        </label>
    @endif
</div>