<div 
    class="fixed inset-0 overflow-y-auto w-full h-full z-50 bg-gray-900/60 transform transition-all" 
    x-data="{ show: @entangle($attributes->wire('model')) }" 
    x-on:close.stop="show = false" 
    x-on:keydown.escape.window="show = false" 
    x-show="show" 
    style="display: none;"
    x-transition:enter="ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0">

    {{-- Close Button top right --}}
    <div class="absolute top-0 right-0 p-4">
        {{ $closeButton }}
    </div>

    {{-- Modal --}}

    <div 
        class="w-full flex justify-center mt-12 transform transition-all"
        x-trap.inert.noscroll="show"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">

        <div class="bg-gray-100 dark:bg-gray-800 rounded-sm w-5/6 p-4">
            {{-- Topbar (card id, name, status, users, menu button?) --}}
            <div class="grid grid-cols-4">
                <div class="col-span-3 flex gap-x-4">
                    <h1 class="text-lg text-gray-700">{{ $cardId }}</h1>
                    <h1 class="text-lg text-gray-700">{{ $cardName }}</h1>
                </div>
                <div class="col-span-1 flex gap-x-4 justify-end">
                    <div>
                        {{ $adminStatus }}
                    </div>
                    <div>
                        {{ $status }}
                    </div>
                    <div>
                        {{ $users }}
                    </div>
                    <div>
                        {{ $menuButton }}
                    </div>
                </div>
            </div>
        </div>
    
        {{-- <div class="bg-white dark:bg-gray-800 w-4/5">
            <div class="bg-gray-100 dark:bg-gray-800 dark:text-white border-b border-gray-200 dark:border-gray-900 px-6 py-4">
                {{ $title }}
            </div>
            <div class="px-6 py-4">
                {{ $content }}
            </div>
            <div class="flex justify-end gap-x-2 bg-gray-100 dark:bg-gray-800 border-t border-gray-200 dark:border-gray-900 px-6 py-4">
                {{ $footer }}
            </div>
        </div> --}}
    </div>
</div>