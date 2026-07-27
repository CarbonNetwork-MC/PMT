@props([
    'href',
    'active' => false,
    'icon' => null,
    'mobile' => false,
])

@if ($mobile)
    <a
        href="{{ $href }}"
        {{ $attributes->class([
            'flex w-full items-center gap-2 rounded-lg px-3 py-2 text-sm transition',
            'bg-green-500/20 text-white' => $active,
            'text-zinc-300 hover:bg-zinc-800 hover:text-white' => !$active,
        ]) }}
    >
        @if ($icon)
            <i class="fi fi-{{ $icon }} shrink-0 leading-none text-[14px]"></i>
        @endif

        <span class="min-w-0 truncate">
            {{ $slot }}
        </span>
    </a>
@else
    <a
        href="{{ $href }}"
        class="block rounded-lg px-3 py-1.5 text-sm text-white hover:bg-zinc-800"
        :class="navSubLinkClass(
            {{ $active ? 'true' : 'false' }},
            {{ $icon ? 'true' : 'false' }}
        )"
    >
        <span class="truncate {{ $icon ? 'flex items-center gap-x-2' : '' }}">
            @if ($icon)
                <i class="fi fi-{{ $icon }} leading-none text-[14px]"></i>
            @endif

            {{ $slot }}
        </span>
    </a>
@endif