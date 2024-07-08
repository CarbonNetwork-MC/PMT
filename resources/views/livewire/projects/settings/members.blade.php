<div class="flex justify-center mt-8">
    <div class="w-4/5 bg-white dark:bg-gray-800 shadow-md rounded-md">
        <div class="p-4">
            {{-- Tabs --}}
            @livewire('components.settings-tabs', ['uuid' => $uuid])

            {{-- Content --}}
            
        </div>
        <div class="flex justify-end bg-gray-100 dark:bg-gray-700 rounded-b-md p-4">
            <input type="button" wire:click="save" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" value="{{ __('settings.save') }}" />
        </div>
    </div>
</div>