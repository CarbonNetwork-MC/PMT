@props(['active', 'dropdownName'])

@php
    $classes = ($active ?? false)
        ? 'flex items-center justify-between p-2 text-white rounded-lg bg-carbon-500 group cursor-pointer'
        : 'flex items-center justify-between p-2 text-white rounded-lg hover:bg-carbon-700 group cursor-pointer';
@endphp

<div class="relative" x-data="{ open: localStorage.getItem('{{$dropdownName}}') === 'true', sidebarState: localStorage.getItem('sidebarState') }" x-init="$watch(open = localStorage.getItem('{{ $dropdownName }}'))">
    <div @click="open = !open; localStorage.setItem('{{$dropdownName}}', open)" {{ $attributes->merge(['class' => $classes]) }}>
        <div class="flex">{{ $trigger }}</div>
        <div id="chevrons">
            <svg x-show="!open" class="w-6 h-6 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m8 10 4 4 4-4"/>
            </svg>
            <svg x-show="open" class="w-6 h-6 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m16 14-4-4-4 4"/>
            </svg>
        </div>
    </div>

    <div x-show="open" id="dropdown-content" @click="open = false; localStorage.setItem('{{ $dropdownName }}', false)" class="flex flex-col">
        {{ $content }}
    </div>
</div>
