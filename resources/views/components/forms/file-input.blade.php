@props(['id' => 'file-input', 'label' => '', 'size' => 'md', 'helper' => '', 'required' => false, 'disabled' => false])
@php
    $sizeClasses = match($size) {
        'md' => 'text-sm',
        'lg' => 'text-lg',
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
        <label for="{{ $id }}" class="block mb-2.5 text-sm font-medium text-heading {{ $labelAttributes->get('label:class') }}">
            {{ $label }}
        </label>
    @endif

    {{-- Input --}}
    <input
        type="file"
        id="{{ $id }}"
        @if($required) required @endif
        @if($disabled) disabled @endif
        {{ $attributes->class([
            'block w-full text-sm text-heading rounded-base file:bg-gray-100 file:placeholder:text-gray-500 dark:file:placeholder:text-gray-600 shadow-xs',
            $hasError
                ? 'border border-red-500 focus:ring-red-500 focus:border-red-500'
                : 'border border-default-medium focus:ring-brand focus:border-brand',
        ]) }}
    />

    {{-- Helper Text --}}
    @if ($helper)
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            {{ $helper }}
        </p>
    @endif

    {{-- Error Message --}}
    @if ($hasError)
        <p class="mt-1.5 text-sm text-red-500">{{ $errors->first($model) }}</p>
    @endif
</div>