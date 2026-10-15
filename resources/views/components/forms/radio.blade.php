@props(['id' => 'radio-input', 'label' => '', 'helper' => '', 'required' => false, 'disabled' => false])
@php
    $wrapperAttributes = $attributes->only(['wrapper:class']);
    $labelAttributes = $attributes->only(['label:class']);
@endphp

<div class="{{ $wrapperAttributes->get('wrapper:class') }} flex {{ $helper ? 'items-start' : 'items-center' }}">
    {{-- Radio --}}
    <div class="{{ $helper ? 'flex items-center h-5' : '' }}">
        <input
            type="radio"
            id="{{ $id }}"
            @if($required) required @endif
            @if($disabled) disabled @endif
            {{ $attributes->class([
                'w-4 h-4 text-neutral-primary ' . ($disabled ? 'border-light-medium' : 'border-default-medium') . ' bg-neutral-secondary-medium rounded-full checked:border-brand focus:ring-2 focus:outline-none focus:ring-brand-subtle border border-default appearance-none'
            ]) }}
        />
    </div>

    {{-- Label + Helper --}}
    @if ($label)
        <div class="ms-2 select-none text-sm">
            <label
                for="{{ $id }}"
                class="font-medium {{ $helper ? 'mb-1' : '' }} {{ $disabled ? "text-fg-disabled" : "text-heading" }} {{ $labelAttributes->get('label:class') }}"
            >
                {{ $label }}
            </label>

            @if ($helper)
                <p id="helper-{{ $id }}" class="text-xs font-normal text-body">
                    {{ $helper }}
                </p>
            @endif
        </div>
    @endif
</div>