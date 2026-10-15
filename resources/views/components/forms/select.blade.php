@props([
    'id' => null,
    'label' => '',
    'size' => '',
    'width' => 'max-w-sm',
    'options' => [],
    'placeholder' => 'Select an option',
    'labelColor' => 'text-black dark:text-white',
    'required' => false,
    'disabled' => false,
])

@php
    $sizeClasses = match ($size) {
        'large' => 'px-3.5 py-3',
        'extra-large' => 'px-4 py-3.5',
        default => 'px-3 py-2.5',
    };

    $selectId = $id ?? $attributes->wire('model')->value() ?? 'select';
@endphp

<div class="{{ $width }}">
    @if ($label)
        <label
            for="{{ $selectId }}"
            class="block mb-2.5 text-sm font-medium {{ $labelColor }}"
        >
            {{ $label }}

            @if ($required)
                <span class="text-red-400">*</span>
            @endif
        </label>
    @endif

    <select
        id="{{ $selectId }}"
        @required($required)
        @disabled($disabled)
        {{ $attributes->class([
            'block w-full border border-gray-200
             text-sm text-black rounded-base focus:ring-brand
             focus:border-brand shadow-xs placeholder:text-gray-400',
            $sizeClasses,
        ]) }}
    >
        @if ($placeholder)
            <option value="" disabled>{{ $placeholder }}</option>
        @endif

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