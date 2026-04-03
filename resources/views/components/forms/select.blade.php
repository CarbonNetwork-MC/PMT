@props([
    'id' => 'select',
    'label' => '',
    'size' => '',
    'required' => false,
    'disabled' => false,
    'options' => [],
    'placeholder' => 'Select an option',
])

@php
    $sizeClasses = match($size) {
        'large' => 'px-3.5 py-3',
        'extra-large' => 'px-4 py-3.5',
        default => 'px-3 py-2.5',
    };
@endphp

<div
    x-data="{
        open: false,
        value: @entangle($attributes->wire('model')).live,
        options: @js($options),
        isMobile: window.innerWidth < 768,
        activeIndex: -1,

        get selectedLabel() {
            return this.options[this.value] ?? '{{ $placeholder }}';
        },

        openDropdown() {
            this.open = true;
            this.activeIndex = Math.max(
                Object.keys(this.options).indexOf(this.value),
                0
            );
        },

        closeDropdown() {
            this.open = false;
            this.activeIndex = -1;
        },

        select(index) {
            const keys = Object.keys(this.options);
            this.value = keys[index];
            this.closeDropdown();
        }
    }"
    class="relative max-w-sm"
>
    @if ($label)
        <label class="block mb-2.5 text-sm font-medium text-heading">
            {{ $label }}
            @if ($required) <span class="text-red-400">*</span> @endif
        </label>
    @endif

    <select
        x-show="isMobile"
        x-model="value"
        id="{{ $id }}"
        name="{{ $id }}"
        @disabled($disabled)
        class="w-full bg-gray-100 border border-default-medium rounded-base text-sm text-black shadow-xs focus:ring-brand focus:border-brand {{ $sizeClasses }}"
    >
        <option value="" disabled>{{ $placeholder }}</option>
        @foreach ($options as $key => $optionLabel)
            <option value="{{ $key }}">{{ $optionLabel }}</option>
        @endforeach
    </select>

    <button
        x-show="!isMobile"
        type="button"
        role="combobox"
        :aria-expanded="open"
        aria-haspopup="listbox"
        :aria-controls="'{{ $id }}-listbox'"
        @click="open ? closeDropdown() : openDropdown()"
        @keydown.arrow-down.prevent="openDropdown(); activeIndex = Math.min(activeIndex + 1, Object.keys(options).length - 1)"
        @keydown.arrow-up.prevent="openDropdown(); activeIndex = Math.max(activeIndex - 1, 0)"
        @keydown.enter.prevent="if (open && activeIndex >= 0) select(activeIndex)"
        @keydown.escape="closeDropdown()"
        @keydown.tab="closeDropdown()"
        @disabled($disabled)
        class="w-full flex justify-between items-center bg-gray-100 border border-default-medium rounded-base text-sm text-black shadow-xs focus:ring-brand focus:border-brand {{ $sizeClasses }}"
    >
        <span x-text="selectedLabel" class="truncate"></span>

        <svg class="w-4 h-4 ml-2 transition-transform" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24">
            <path stroke="currentColor" stroke-width="2" d="M6 9l6 6 6-6"/>
        </svg>
    </button>

    <div
        role="listbox"
        :id="'{{ $id }}-listbox'"
        x-show="open && !isMobile"
        @click.outside="closeDropdown()"
        x-transition
        class="absolute z-50 mt-2 w-full bg-white text-black border border-default-medium rounded-base shadow-lg max-h-60 overflow-auto"
    >
        <template x-for="(key, index) in Object.keys(options)" :key="key">
            <div
                role="option"
                :aria-selected="value === key"
                @click="select(index)"
                class="px-3 py-2 hover:bg-gray-200 cursor-pointer"
                :class="{ 'bg-blue-400': value === key }"
            >
                <span x-text="options[key]"></span>
            </div>
        </template>
    </div>
</div>