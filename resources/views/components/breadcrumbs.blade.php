@props(['items' => [], 'margin' => 'mb-4'])

<div class="bg-white dark:bg-gray-800 rounded-md shadow-sm p-4 {{ $margin }}">
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-2">
            @foreach ($items as $index => $item)
                @if ($index > 0)
                    <li>
                        <i class="fi fi-br-angle-small-right dark:text-white"></i>
                    </li>
                @endif

                <li class="inline-flex items-center">
                    @if (!empty($item['url']))
                        <a
                            href="{{ $item['url'] }}"
                            class="inline-flex items-center text-sm font-medium
                                   {{ $loop->last ? 'text-emerald-500' : 'text-gray-700 dark:text-gray-400' }}
                                   hover:text-emerald-600 dark:hover:text-white"
                        >
                            @if (!empty($item['icon']))
                                <i class="{{ $item['icon'] }} {{ $item['label'] ? 'me-2.5' : '' }}"></i>
                            @endif
                            {{ $item['label'] }}
                        </a>
                    @else
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            {{ $item['label'] }}
                        </span>
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>
</div>