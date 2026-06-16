@props(['id' => 'date-input', 'label' => '', 'size' => 'md', 'placeholder' => '', 'required' => false, 'disabled' => false])
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
        type="date" 
        id="{{ $id }}" 
        placeholder="{{ $placeholder }}" 
        @if($required) required @endif 
        @if($disabled) disabled @endif
        {{ $attributes->class([
            'block w-full rounded-base text-sm shadow-xs ' . $sizeClasses,
            'placeholder:text-gray-500 dark:placeholder:text-gray-600',
            'dark:bg-gray-900 text-black dark:text-white',
            $disabled
                ? 'bg-gray-300 cursor-not-allowed'
                : 'bg-gray-100',
            $hasError
                ? 'border border-red-500 focus:ring-red-500 focus:border-red-500'
                : 'border border-default-medium focus:ring-brand focus:border-brand',
        ]) }}
    />
</div>