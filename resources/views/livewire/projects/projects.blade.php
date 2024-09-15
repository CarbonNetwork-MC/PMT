<div>
    <div class="flex justify-end items-center mb-6">
        <button wire:click="$toggle('createProjectModal')" class="flex items-center px-3 py-1.5 bg-blue-500 text-white rounded-md hover:bg-blue-600 ml-2">
            <i class="fi fi-sr-plus"></i>
            <span class="ml-2">{{ __('projects.create_project') }}</span>
        </button>
    </div>

    <div class="grid grid-cols-3 lg:grid-cols-4 3xl:grid-cols-5">
        @foreach ($projects as $project)
            <a href="{{ route('projects.overview.render', ['uuid' => $project->uuid]) }}" class="col-span-1 block max-w-sm p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700">

                <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">{{ $project->name }}</h5>
                <div class="flex gap-x-4">
                    <div class="flex">
                        <i class="fi fi-sr-running text-xl dark:text-white"></i>                  
                        <p class="ml-2 text-gray-800 dark:text-white">{{ $project->sprints->count() }}</p>                    
                    </div>
                    <div class="flex">
                        <i class="fi fi-sr-users-alt dark:text-white"></i>
                        <p class="ml-2 text-gray-800 dark:text-white">{{ $project->members->count() }}</p>
                    </div>
                </div>
            </a>
        @endforeach
    </div>

    {{-- Create Project Modal --}}
    <x-pmt-modal wire:model="createProjectModal">
        <x-slot name="title">
            {{ __('projects.create_project') }}
        </x-slot>

        <x-slot name="content">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3 p-4">
                <div class="col-span-1">
                    <x-label for="name" value="{{ __('projects.name') }}" />
                    <x-input id="name" type="text" class="mt-1 block w-full" wire:model.defer="name" />
                    <x-input-error for="name" class="mt-2" />
                </div>
                <div class="col-span-1"></div>
                <div class="col-span-3">
                    <x-label for="description" value="{{ __('projects.description') }}" />
                    <x-textarea id="description" class="mt-1 block w-full" wire:model.defer="description" />
                    <x-input-error for="description" class="mt-2" />
                </div>
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-primary-button wire:click="createProject" wire:loading.attr="disabled">
                {{ __('projects.create') }}
            </x-primary-button>
            <x-secondary-button wire:click="$toggle('createProjectModal')" wire:loading.attr="disabled">
                {{ __('projects.cancel') }}
            </x-secondary-button>
        </x-slot>
    </x-pmt-modal>
</div>
