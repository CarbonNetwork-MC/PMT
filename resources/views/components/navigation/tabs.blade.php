<div class="text-sm font-medium text-center text-gray-800 dark:text-gray-200 border-b border-gray-200 dark:border-gray-700">
    <ul class="flex flex-wrap">
        @foreach ($tabs as $tab)
            @php
                $isActive = $active === $tab['key'];
                $disabled = $tab['disabled'] ?? false;
            @endphp

            <li class="me-2">
                @if ($disabled)
                    <span class="inline-block p-4 text-fg-disabled rounded-t-base cursor-not-allowed dark:text-body">
                        {{ $tab['label'] }}
                    </span>
                @else
                    <a href="{{ $tab['href'] ?? '#' }}"
                    class="inline-block p-4 border-b rounded-t-base
                    {{ $isActive 
                            ? 'border-blue-600 text-blue-500 active' 
                            : 'border-transparent text-black dark:text-white hover:text-blue-500 hover:border-blue-600' }}">
                        {{ $tab['label'] }}
                    </a>
                @endif
            </li>
        @endforeach
    </ul>
</div>
