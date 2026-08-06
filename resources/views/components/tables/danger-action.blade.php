@props(['href' => null])

@if ($href)
    <a href="{{ $href }}" class="text-red-500 hover:text-red-700">
        {{ $slot }}
    </a>
@else
    <span {{ $attributes->merge([
        'class' => "text-red-500 hover:text-red-700 cursor-pointer"
    ]) }}>
        {{ $slot }}
    </span>
@endif