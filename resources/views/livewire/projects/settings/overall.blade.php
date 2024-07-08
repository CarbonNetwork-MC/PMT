<div class="flex justify-center mt-8">
    <div class="w-4/5 bg-white dark:bg-gray-800 shadow-md rounded-md">
        <div class="p-4">
            {{-- Tabs --}}
            @livewire('components.settings-tabs', ['uuid' => $uuid])

            {{-- Content --}}
            <div class="mt-4 p-4">
                <div class="grid grid-cols-3">
                    <div class="col-span-1">
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-200">{{ __('settings.project_name') }}</label>
                        <input type="text" id ="name" wire:model="name" class="w-full dark:bg-gray-900 dark:text-white border border-gray-300 dark:border-gray-700 rounded p-2 mt-2" placeholder="{{ __('settings.project_name') }}" />
                    </div>
                </div>
                <hr class="h-px my-8 bg-gray-300 border-0 dark:bg-gray-700">
                <div class="grid grid-cols-3">
                    <div class="col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-200">{{ __('settings.project_description') }}</label>
                        <textarea id="description" rows="5" wire:model="description" class="w-full dark:bg-gray-900 dark:text-white border border-gray-300 dark:border-gray-700 rounded resize-none p-2 mt-2" placeholder="{{ __('settings.project_description') }}"></textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex justify-end bg-gray-100 dark:bg-gray-700 rounded-b-md p-4">
            <button wire:click="save" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">{{ __('settings.save') }}</button>
        </div>
    </div>
</div>