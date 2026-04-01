@props(['href' => null, 'disabled' => false])

@if ($href)
    <a href="{{ $href }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-700 {{ $disabled ? 'cursor-not-allowed' : 'cursor-pointer' }}">
        {{ $slot }}
    </a>
@else
    <span {{ $attributes->merge([
        'class' => "text-gray-600 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-700 " . ($disabled ? 'cursor-not-allowed' : 'cursor-pointer')
    ]) }}>
        {{ $slot }}
    </span>
@endif