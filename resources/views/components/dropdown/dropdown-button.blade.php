@props([
    'icon',
    'label',
    'wireClick' => null,
    'alpineClick' => null,
    'color' => 'gray-900',
    'margin' => '',
])

<div
    class="flex gap-x-2 hover:bg-gray-300 dark:hover:bg-gray-200 {{ $margin }} ps-4 py-1 rounded-sm cursor-pointer"
    @if($wireClick) wire:click="{{ $wireClick }}" @endif
    @if($alpineClick) @click="{{ $alpineClick }}" @endif
>
    @if ($icon)
        <i class="fi fi-{{ $icon }} text-{{ $color }}"></i>
    @endif
    <p class="text-{{ $color }} text-sm">{{ $label }}</p>
</div>