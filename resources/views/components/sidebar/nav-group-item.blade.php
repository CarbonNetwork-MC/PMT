@props(['href', 'active' => false, 'icon' => null])

<a
    href="{{ $href }}"
    class="block rounded-lg px-3 py-1.5 text-sm
           text-zinc-600 dark:text-zinc-300
           hover:bg-zinc-100 dark:hover:bg-zinc-800"
    :class="navSubLinkClass({{ $active ? 'true' : 'false' }}, {{ $icon ? 'true' : 'false' }})">

    <span class="truncate {{ $icon ? 'flex items-center gap-x-2' : '' }}">
        @if ($icon)
            <i class="fi fi-{{ $icon }} leading-none text-[14px]"></i>
        @endif
        {{ $slot }}
    </span>
</a>