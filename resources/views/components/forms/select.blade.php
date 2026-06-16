@props([
    'id' => 'select', 
    'label' => '', 
    'size' => '', 
    'options' => [],
    'placeholder' => 'Select an option',
    'required' => false, 
    'disabled' => false
    ])
@php
    $sizeClasses = match($size) {
        'large' => 'px-3.5 py-3',
        'extra-large' => 'px-4 py-3.5',
        default => 'px-3 py-2.5',
    };
@endphp

<div class="max-w-sm">
    @if ($label)
        <label class="block mb-2.5 text-sm font-medium text-heading">
            {{ $label }}
            @if ($required) <span class="text-red-400">*</span> @endif
        </label>
    @endif

    <select 
        id="{{ $id }}"
        wire:model.live="{{ $attributes->wire('model')->value() }}"
        @if($required) required @endif
        @if($disabled) disabled @endif
        {{ $attributes->class([
            'block w-full bg-gray-100 border border-default-medium text-black text-sm rounded-base focus:ring-brand focus:border-brand shadow-xs placeholder:text-body ' . $sizeClasses
        ]) }}
    >
        <option value="" disabled>{{ $placeholder }}</option>

        @foreach ($options as $option)
            <option value="{{ $option['value'] }}">
                {{ $option['label'] }}
            </option>
        @endforeach
    </select>
</div>
