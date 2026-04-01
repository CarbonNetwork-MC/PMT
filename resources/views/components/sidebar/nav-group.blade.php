@props(['groupKey', 'icon', 'label'])

<div>
    <button
        type="button"
        @click="isCollapsed ? expandFromIcon() : toggleGroup('{{ $groupKey }}')"
        class="w-full items-center gap-3 rounded-xl px-2 text-sm font-medium text-zinc-700 dark:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-800 cursor-pointer"
        :class="!isCollapsed ? 'flex py-2' : ''">

        <span 
            class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-zinc-100 dark:bg-zinc-800"
            :class="isCollapsed ? 'py-2' : 'py-1.5'">

            <i class="fi fi-{{ $icon }} leading-none text-[16px]"></i>
        </span>

        <span x-show="!isCollapsed" class="truncate">
            {{ $label }}
        </span>

        <span
            x-show="!isCollapsed"
            class="ml-auto h-4 w-4 text-zinc-500 transition-transform"
            :class="isGroupOpen('{{ $groupKey }}') ? 'rotate-180' : ''">

            <i class="fi fi-rr-angle-small-down"></i>
        </span>
    </button>

    <div
        x-show="!isCollapsed && isGroupOpen('{{ $groupKey }}')"
        x-collapse
        class="mt-1 space-y-1">

        {{ $slot }}
    </div>
</div>