@props([
    'groupKey',
    'icon',
    'label',
    'mobile' => false,
])

<div>
    @if ($mobile)
        <button
            type="button"
            @click="toggleGroup('{{ $groupKey }}')"
            class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5
                   text-sm font-medium text-white transition
                   hover:bg-zinc-800 cursor-pointer"
            :aria-expanded="isGroupOpen('{{ $groupKey }}').toString()"
        >
            <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-zinc-800">
                <i class="fi fi-{{ $icon }} leading-none text-[16px]"></i>
            </span>

            <span class="min-w-0 truncate">
                {{ $label }}
            </span>

            <span
                class="ml-auto flex h-4 w-4 items-center justify-center
                       text-zinc-300 transition-transform"
                :class="isGroupOpen('{{ $groupKey }}') ? 'rotate-180' : ''"
            >
                <i class="fi fi-rr-angle-small-down leading-none"></i>
            </span>
        </button>

        <div
            x-show="isGroupOpen('{{ $groupKey }}')"
            x-collapse
            class="mt-1 ml-4 space-y-1"
        >
            {{ $slot }}
        </div>
    @else
        <button
            type="button"
            @click="isCollapsed
                ? expandFromIcon()
                : toggleGroup('{{ $groupKey }}')"
            class="w-full items-center gap-3 rounded-xl px-2 text-sm
                   font-medium text-white hover:bg-zinc-800 cursor-pointer"
            :class="!isCollapsed ? 'flex py-2' : ''"
        >
            <span
                class="inline-flex h-8 w-8 items-center justify-center
                       rounded-xl bg-zinc-900 dark:bg-zinc-800"
                :class="isCollapsed ? 'py-2' : 'py-1.5'"
            >
                <i class="fi fi-{{ $icon }} leading-none text-[16px]"></i>
            </span>

            <span x-show="!isCollapsed" class="truncate">
                {{ $label }}
            </span>

            <span
                x-show="!isCollapsed"
                class="ml-auto h-4 w-4 text-zinc-300 transition-transform"
                :class="isGroupOpen('{{ $groupKey }}') ? 'rotate-180' : ''"
            >
                <i class="fi fi-rr-angle-small-down"></i>
            </span>
        </button>

        <div
            x-show="!isCollapsed && isGroupOpen('{{ $groupKey }}')"
            x-collapse
            class="mt-1 space-y-1"
        >
            {{ $slot }}
        </div>
    @endif
</div>