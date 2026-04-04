<div 
    x-data="{ show: @entangle($attributes->wire('model')) }"
    x-on:close.stop="show = false"
    x-on:keydown.escape.window="show = false"
    x-trap.inert.noscroll="show"
    x-show="show"
    class="overflow-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full h-full"
>
    <div x-show="show" class="relative p-4 w-full h-full flex items-center justify-center bg-gray-900/60">
        <div class="relative flex flex-col lg:w-lg bg-white rounded-lg shadow-sm dark:bg-gray-700">
            <div class="w-full flex justify-end pr-3 pt-2">
                <button type="button" @click="show = false" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <div class="px-4 pb-4 md:px-5 md:pb-5">
                {{ $slot }}
                
            </div>
        </div>
    </div>
</div>