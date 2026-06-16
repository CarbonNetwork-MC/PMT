@props(['href', 'active' => false, 'icon' => null])

<a
    href="{{ $href }}"
    class="block rounded-lg px-3 py-1.5 text-sm hover:bg-zinc-800"
    :class="navSubLinkClass({{ $active ? 'true' : 'false' }}, {{ $icon ? 'true' : 'false' }})">

    <span class="truncate {{ $icon ? 'flex items-center gap-x-2' : '' }}">
        @if ($icon)
            <i class="fi fi-{{ $icon }} leading-none text-[14px]"></i>
        @endif
        {{ $slot }}
    </span>
</a>