<div class="grid grid-cols-5 gap-x-4">
    <div class="col-span-2 mt-4">
        <h1 class="font-bold text-black dark:text-white text-xl">
            {{ $title }}
        </h1>
        <span class="text-gray-600 dark:text-gray-400">
            {{ $description ?? '' }}
        </span>
    </div>
    <x-containers.main class="col-span-3">
        {{ $form ?? '' }}
    </x-containers.main>
</div>