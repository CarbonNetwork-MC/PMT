@props(['title' => null, 'content' => null, 'footer' => null])

<div 
    x-data="{ show: @entangle($attributes->wire('model')) }"
    x-on:close.stop="show = false"
    x-on:keydown.escape.window="show = false"
    x-trap.inert.noscroll="show"
    x-show="show"
    x-on:show.window="$nextTick(() => $el.querySelector('[data-autofocus]')?.focus())"
    class="fixed inset-0 z-50 overflow-y-auto"
>
    <!-- Background overlay -->
    <div class="fixed inset-0 bg-gray-900/40"></div>

    <!-- Modal container -->
    <div class="relative flex justify-center pt-16 px-4">
        <div 
            x-show="show"
            class="relative w-full max-w-3xl bg-white dark:bg-gray-700 text-black dark:text-white rounded-lg shadow-lg"
        >
            <!-- Header -->
            <div class="flex justify-between px-4 py-2 bg-gray-200 dark:bg-gray-800 rounded-t-lg">
                <h2 class="w-full font-bold text-xl">{{ $title ?? '' }}</h2>
                
                <button 
                    type="button" 
                    @click="show = false"
                    class="text-gray-400 hover:bg-gray-200 hover:text-gray-900 dark:hover:bg-gray-600 dark:hover:text-white rounded-lg w-8 h-8 flex items-center justify-center focus:outline-none cursor-pointer"
                >
                    <svg class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                </button>
            </div>

            <!-- Content -->
            <div class="px-6 py-4">
                {{ $content ?? '' }}
            </div>

            <!-- Footer -->
            <div class="flex gap-x-2 justify-end px-6 py-3 bg-gray-100 dark:bg-gray-800 rounded-b-lg">
                {{ $footer ?? '' }}
            </div>
        </div>
    </div>
</div>