<div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
    <div class="col-span-1 lg:col-span-3">
        <h1 class="font-bold text-black dark:text-white text-xl">
            {{ $title }}
        </h1>
        <span class="text-gray-600 dark:text-gray-400">
            {!! $description ?? '' !!}
        </span>
    </div>
    <div class="col-span-1 lg:col-span-2">
        {{ $content ?? '' }}
    </div>
</div>