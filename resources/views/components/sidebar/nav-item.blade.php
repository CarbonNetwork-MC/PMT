@props(['href', 'active' => false, 'icon', 'label'])

<a href="{{ $href }}"
    :class="navLinkClass({{ $active ? 'true' : 'false' }})">

    <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-zinc-100 dark:bg-zinc-800"
        :class="isCollapsed ? 'mb-2 py-2' : 'py-1.5'">
        
        <i class="{{ $icon }} leading-none text-[16px]"></i>
    </span>

    <span x-show="!isCollapsed" class="truncate">{{ $label }}</span>
</a>