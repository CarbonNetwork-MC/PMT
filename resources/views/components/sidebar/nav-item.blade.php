@props([
    'href',
    'active' => false,
    'icon',
    'label',
    'mobile' => false,
])

@if ($mobile)
    <a
        href="{{ $href }}"
        {{ $attributes->class([
            'flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm transition',
            'bg-green-500/20 text-white' => $active,
            'text-zinc-200 hover:bg-zinc-800' => !$active,
        ]) }}
    >
        <span
            class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-zinc-800"
        >
            <i class="{{ $icon }} leading-none text-[16px] text-white"></i>
        </span>

        <span class="min-w-0 truncate font-rw-semibold">
            {{ $label }}
        </span>
    </a>
@else
    <a
        href="{{ $href }}"
        :class="navLinkClass({{ $active ? 'true' : 'false' }})"
    >
        <span
            class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-zinc-900 dark:bg-zinc-800"
            :class="isCollapsed ? 'mb-2 py-2' : 'py-1.5'"
        >
            <i class="{{ $icon }} leading-none text-[16px] text-white"></i>
        </span>

        <span x-show="!isCollapsed" class="truncate text-white">
            {{ $label }}
        </span>
    </a>
@endif