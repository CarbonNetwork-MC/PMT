@props(['href', 'active' => false])

<a
    href="{{ $href }}"
    class="block rounded-lg px-3 py-1.5 text-sm
           text-zinc-600 dark:text-zinc-300
           hover:bg-zinc-100 dark:hover:bg-zinc-800"
    :class="navSubLinkClass({{ $active ? 'true' : 'false' }})">

    <span class="truncate">
        {{ $slot }}
    </span>
</a>