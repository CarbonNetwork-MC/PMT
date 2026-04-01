@props(['width' => '', 'height' => '', 'margin' => 'my-8'])

<hr class="{{ $margin }} bg-gray-300 dark:bg-gray-700 border-0 rounded-sm {{ $width ? 'w-' . $width : '' }} {{ $height ? 'h-' . $height : 'h-px' }}" />
