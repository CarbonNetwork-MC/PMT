@props(['id', 'content'])

<div id="{{ $id }}" role="tooltip" class="absolute z-30 invisible inline-block px-3 py-2 text-xs font-medium text-white dark:text-black bg-gray-800 dark:bg-gray-100 rounded-lg shadow-sm opacity-0 tooltip">
    {{ $content }}
    <div class="tooltip-arrow" data-popper-arrow></div>
</div>