@props([
    'state' => 'open',
    'useOwnState' => true,
    'icon' => null,
    'tooltipId' => null,
    'tooltip' => null,
    'align' => 'right',
    'width' => 'w-52',
    'margin' => 'mt-4',
])

@php
    $alignment = $align === 'left' ? 'left-0' : 'right-0';
@endphp

<div @if ($useOwnState) x-data="{ {{ $state }}: false }" @endif class="relative">
    @isset($handle)
        <div
            @click="{{ $state }} = !{{ $state }}"
            @if($tooltipId) data-tooltip-target="{{ $tooltipId }}" @endif
        >
            {{ $handle }}
        </div>
    @elseif ($icon)
        <i
            class="fi fi-{{ $icon }} dark:text-white cursor-pointer"
            @click="{{ $state }} = !{{ $state }}"
            @if($tooltipId) data-tooltip-target="{{ $tooltipId }}" @endif
        ></i>
    @endif

    @if ($tooltipId && $tooltip)
        <x-tooltip :id="$tooltipId" :content="$tooltip" />
    @endif

    <div
        x-show="{{ $state }}"
        x-transition
        @click.outside="{{ $state }} = false"
        class="absolute {{ $alignment }} top-full {{ $margin }} z-50 {{ $width }} bg-gray-200 dark:bg-gray-100 border border-zinc-400 rounded px-1 py-2"
    >
        {{ $slot }}
    </div>
</div>