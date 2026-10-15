@props(['width' => '', 'height' => '', 'margin' => 'my-8', 'color' => 'gray-300', 'darkColor' => 'gray-700'])

<hr class="{{ $margin }} bg-{{ $color }} dark:bg-{{ $darkColor }} border-0 rounded-sm {{ $width ? 'w-' . $width : '' }} {{ $height ? 'h-' . $height : 'h-px' }}" />
