<tr {{ $attributes->merge([
    'class' => "odd:bg-white even:bg-gray-100 dark:odd:bg-gray-600 dark:even:bg-gray-700 border-b border-default hover:bg-gray-200 dark:hover:bg-gray-500 transition-colors duration-200",
]) }}>

    {{ $slot }}
</tr>