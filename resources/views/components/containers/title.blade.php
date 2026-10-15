@props(['marginBottom' => false, 'href' => null])

@if ($href)
    <a href="{{ $href }}" class="text-xl font-semibold dark:text-white hover:text-blue-500 {{ $marginBottom ? 'mb-4' : '' }}">
        {{ $slot }}
    </a>
@else
    <h1 class="text-xl font-semibold dark:text-white {{ $marginBottom ? 'mb-4' : '' }}">
        {{ $slot }}
    </h1>
@endif