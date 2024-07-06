<div 
    class="fixed inset-0 overflow-y-auto w-full h-full z-50 bg-gray-900/30" 
    x-data="{ show: @entangle($attributes->wire('model')) }" 
    x-on:close.stop="show = false" 
    x-on:keydown.escape.window="show = false" 
    x-show="show" 
    style="display: none;"
>
    <div class="w-full flex justify-center mt-12">
        <div class="bg-white dark:bg-gray-800 w-3/5">
            <div class="bg-gray-100 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-900 px-6 py-4">
                {{ $title }}
            </div>
            <div class="px-6 py-4">
                {{ $content }}
            </div>
            <div class="flex justify-end gap-x-2 bg-gray-100 dark:bg-gray-800 border-t border-gray-200 dark:border-gray-900 px-6 py-4">
                {{ $footer }}
            </div>
        </div>
    </div>
</div>
