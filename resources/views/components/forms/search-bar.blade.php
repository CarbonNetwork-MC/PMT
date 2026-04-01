@props(['id' => 'search', 'placeholder' => __('general.placeholders.search')])

<div>
    <label for="{{ $id }}" class="mb-2 text-sm font-medium text-gray-900 sr-only dark:text-white">{{ $placeholder }}</label>
    <div class="relative">
        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
            <svg class="w-4 h-4 text-body" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="m21 21-3.5-3.5M17 10a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z"/></svg>
        </div>
        <input 
            type="search" 
            id="{{ $id }}" 
            placeholder="{{ $placeholder }}"
            class="block w-full p-2 ps-9 bg-gray-100 border border-gray-800 text-black text-sm rounded-base focus:ring-brand focus:border-brand shadow-xs placeholder:text-body dark:placeholder:text-gray-900"
            {{ $attributes }}
        />
    </div>
</div>