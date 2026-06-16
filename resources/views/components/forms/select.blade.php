@props([
    'id' => 'select', 
    'label' => '', 
    'size' => '', 
    'width' => 'max-w-sm',
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

<div class="{{ $width }}">
    @if ($label)
        <label for="{{ $id }}" class="block mb-2.5 text-sm font-medium text-heading">
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
            'block w-full bg-gray-100 dark:bg-gray-900 border border-default-medium text-black dark:text-white text-sm rounded-base focus:ring-brand focus:border-brand shadow-xs placeholder:text-body ' . $sizeClasses
        ]) }}
    >
        <option value="" disabled>{{ $placeholder }}</option>

        @foreach ($options as $option)
            <option
                value="{{ $option['value'] }}"
                @disabled($option['disabled'] ?? false)
            >
                {{ $option['label'] }}
            </option>
        @endforeach
    </select>
</div>
