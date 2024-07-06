<div>
    <div class="w-full flex justify-between bg-white dark:bg-gray-800 shadow-md rounded-lg p-4">
        <div class="flex gap-x-4">
            <div>
                <p class="text-sm font-bold uppercase dark:text-white">Sprints</p>
                <div class="flex gap-x-2">
                    <i class="fi fi-sr-running dark:text-white"></i>
                    <p class="dark:text-white">{{ count($sprints) }}</p>
                </div>
            </div>
            <div>
                <p class="text-sm font-bold uppercase dark:text-white">Active</p>
                <div class="flex gap-x-2">
                    <i class="fi fi-ss-calendar dark:text-white"></i>
                    <p class="dark:text-white">{{ count($activeSprints) }}</p>
                </div>
            </div>
            <div>
                <p class="text-sm font-bold uppercase dark:text-white">Done</p>
                <div class="flex gap-x-2">
                    <i class="fi fi-sr-calendar-check dark:text-white"></i>
                    <p class="dark:text-white">{{ count($doneSprints) }}</p>
                </div>
            </div>
        </div>
        <div>
            <button wire:click="$toggle('createSprintModal')" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Create Sprint
            </button>
        </div>
    </div>

    {{-- Create Sprint Modal --}}
    <x-pmt-modal wire:model="createSprintModal" id="">
        <x-slot name="title">
            {{ __('sprints.create_sprint') }}
        </x-slot>
    
        <x-slot name="content">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="col-span-1">
                    <x-label for="name" value="{{ __('sprints.name') }}" />
                    <x-input id="name" type="text" class="mt-1 block w-full" wire:model.defer="name" />
                    <x-input-error for="name" class="mt-2" />
                </div>
                <div class="col-span-1"></div>
                <div class="col-span-1">
                    <x-label for="start_date" value="{{ __('sprints.start_date') }}" />
                    <x-input id="start_date" type="date" class="mt-1 block w-full" wire:model.defer="start_date" />
                    <x-input-error for="start_date" class="mt-2" />
                </div>
                <div class="col-span-1">
                    <x-label for="end_date" value="{{ __('sprints.end_date') }}" />
                    <x-input id="end_date" type="date" class="mt-1 block w-full" wire:model.defer="end_date" />
                    <x-input-error for="end_date" class="mt-2" />
                </div>
            </div>
        </x-slot>
    
        <x-slot name="footer">
            <x-primary-button wire:click="createSprint" wire:loading.attr="disabled" id="test">
                {{ __('sprints.create') }}
            </x-primary-button>
            <x-secondary-button wire:click="$toggle('createSprintModal')" wire:loading.attr="disabled">
                {{ __('sprints.cancel') }}
            </x-secondary-button>
        </x-slot>
    </x-pmt-modal>
</div>
