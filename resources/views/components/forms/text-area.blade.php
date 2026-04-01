@props(['id' => 'textarea', 'label' => '', 'rows' => 4, 'placeholder' => '', 'required' => false, 'disabled' => false])
@php
    $wrapperAttributes = $attributes->only(['wrapper:class']);
    $labelAttributes = $attributes->only(['label:class']);
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

    {{-- Textarea --}}
    <textarea 
        id="{{ $id }}" 
        rows="{{ $rows }}" 
        placeholder="{{ $placeholder }}" 
        @if($required) required @endif
        @if($disabled) disabled @endif
        {{ $attributes->class([
            'bg-gray-100 border border-default-medium text-black text-sm rounded-base focus:ring-brand focus:border-brand block w-full p-3.5 shadow-xs placeholder:text-body dark:placeholder:text-gray-800'
        ]) }}
    ></textarea>
</div>