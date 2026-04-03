@props(['title' => null, 'content' => null, 'footer' => null])

<div 
    x-data="{ show: @entangle($attributes->wire('model')) }"
    x-on:close.stop="show = false"
    x-on:keydown.escape.window="show = false"
    x-trap.inert.noscroll="show"
    x-show="show"
    x-on:show.window="$nextTick(() => $el.querySelector('[data-autofocus]')?.focus())"
    class="overflow-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full h-full"
>
    <div x-show="show" class="relative p-4 w-full h-full flex items-center justify-center bg-gray-900/60">
        <div class="relative flex flex-col lg:w-lg bg-white dark:bg-gray-700 text-black dark:text-white rounded-lg shadow-sm">
            <div class="w-full flex justify-between px-3 py-2 bg-gray-200 dark:bg-gray-800 rounded-t-lg">
                <h2 class="w-full font-bold text-lg">{{ $title ?? '' }}</h2>
                
                <button type="button" @click="show = false" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer focus:outline-none focus:ring-0 focus:ring-transparent">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <div class="px-4 py-2 md:px-5 md:py-3">
                {{ $content ?? '' }}
            </div>
            <div class="flex gap-x-2 justify-end px-6 py-2 bg-gray-100 dark:bg-gray-800 rounded-b-lg">
                {{ $footer ?? '' }}
            </div>
        </div>
    </div>
</div>