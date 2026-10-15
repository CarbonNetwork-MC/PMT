@props(['href' => null])

@if ($href)
    <a href="{{ $href }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-500">
        {{ $slot }}
    </a>
@else
    <span {{ $attributes->merge([
        'class' => "text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-500 cursor-pointer"
    ]) }}>
        {{ $slot }}
    </span>
@endif