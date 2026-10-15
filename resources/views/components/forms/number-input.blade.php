@props(['id' => 'number-input', 'label' => '', 'size' => 'md', 'placeholder' => '', 'required' => false, 'disabled' => false, 'min' => null, 'max' => null])
@php
    $sizeClasses = match($size) {
        'sm' => 'px-2.5 py-2',
        'md' => 'px-3 py-2.5',
        'lg' => 'px-3.5 py-3',
        'xl' => 'px-4 py-3.5',
        default => 'md',
    };

    $wrapperAttributes = $attributes->only(['wrapper:class']);
    $labelAttributes = $attributes->only(['label:class']);

    $model = $attributes->wire('model')->value();
    $hasError = $model && $errors->has($model);
@endphp

<div class="{{ $wrapperAttributes->get('wrapper:class') }}">
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

    {{-- Input --}}
    <input 
        type="number"
        min="{{ $min }}"
        max="{{ $max }}"
        id="{{ $id }}" 
        placeholder="{{ $placeholder }}" 
        @if($required) required @endif 
        @if($disabled) disabled @endif
        {{ $attributes->merge([
            'class' =>
            'block w-full rounded-base text-sm text-black dark:text-gray-800 shadow-xs ' . $sizeClasses,
            'bg-gray-100 placeholder:text-gray-500 dark:placeholder:text-gray-600',
            $hasError
                ? 'border border-red-500 focus:ring-red-500 focus:border-red-500'
                : 'border border-default-medium focus:ring-brand focus:border-brand',
            $disabled
                ? 'cursor-not-allowed bg-gray-300'
                : 'focus:outline-none',
        ]) }}
    />

    {{-- Error Message --}}
    @if ($hasError)
        <p class="mt-1.5 text-sm text-red-500">{{ $errors->first($model) }}</p>
    @endif
</div>