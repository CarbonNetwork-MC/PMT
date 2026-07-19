@props([
    'title' => '',
    'value' => '',
    'icon' => '',
    'iconColor' => 'gray-800',
    'iconColorDark' => 'gray-300',
    'titleColor' => 'black',
    'titleColorDark' => 'white',
    'textColor' => 'black',
    'textColorDark' => 'white',
    'size' => 'sm',
    'align' => 'center'
])

<div class="flex flex-col bg-gray-100 dark:bg-gray-900 rounded-lg px-4 py-1.5">
    <p class="text-{{ $titleColor }} dark:text-{{ $titleColorDark }} text-{{ $size }} font-bold">
        {{ $title }}
    </p>
    <div class="flex justify-{{ $align }} gap-x-2">
        <i class="fi fi-{{ $icon }} text-{{ $iconColor }} dark:text-{{ $iconColorDark }}"></i>
        <p class="text-{{ $textColor }} dark:text-{{ $textColorDark }} text-{{ $size }}">
            {{ $value }}
        </p>
    </div>
</div>