@if (session('success'))
    <div x-data="{ show: true }" x-show="show" class="w-full flex justify-between items-center mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
        <div class="flex items-center gap-x-2 p-4">
            <i class="fi fi-sr-circle-i"></i>
            <span class="sr-only">Info</span>
            <div>
                <span>{!! session('success') !!}</span>
            </div>
        </div>
        <div class="hover:bg-green-200 rounded-md p-2 cursor-pointer mr-2" @click="show = false">
            <i class="fi fi-br-cross"></i>
        </div>
    </div>
@endif

@if (session('error'))
    <div x-data="{ show: true }" x-show="show" class="w-full flex justify-between items-center mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
        <div class="flex items-center gap-x-2 p-4">
            <i class="fi fi-sr-circle-i"></i>
            <span class="sr-only">Error</span>
            <div>
                <span>{!! session('error') !!}</span>
            </div>
        </div>
        <div class="hover:bg-red-200 rounded-md p-2 cursor-pointer mr-2" @click="show = false">
            <i class="fi fi-br-cross"></i>
        </div>
    </div>
@endif