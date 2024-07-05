<div class="flex justify-center">
    <div class="w-1/2 bg-gray-100 dark:bg-gray-800 shadow-md rounded-lg p-4">
        <h1 class="text-xl font-bold">{{ __('projects.create_project') }}</h1>

        <div>
            <div class="mt-4">
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('projects.name') }}</label>
                <input type="text" wire:model="name" id="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:focus:border-indigo-600 dark:focus:ring dark:focus:ring-indigo-600 dark:focus:ring-opacity-50">
                @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="mt-4">
                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('projects.description') }}</label>
                <textarea wire:model="description" id="description" rows="5" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:focus:border-indigo-600 dark:focus:ring dark:focus:ring-indigo-600 dark:focus:ring-opacity-50"></textarea>
                @error('description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="mt-4">
                <button type="submit" wire:click="createProject" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">{{ __('projects.create') }}</button>
            </div>
        </div>
    </div>
</div>
