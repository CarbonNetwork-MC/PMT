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
                {{ __('sprints.create_sprint') }}
            </button>
        </div>
    </div>

    <div class="w-full grid grid-cols-2 gap-x-2 md:grid-cols-3 lg:grid-cols-4">
        @forelse ($sprints as $sprint)
            <div class="col-span-1 bg-white dark:bg-gray-800 shadow-md rounded-lg p-4 mt-4">
                <div class="flex justify-between items-center">
                    <a href="{{ route('projects.board.render', ['uuid' => $uuid]) }}" class="text-lg font-bold dark:text-white hover:text-blue-500">{{ $sprint->name }}</a>
                    <p class="text-xs font-semibold text-gray-500 dark:text-white">{{ $sprint->start_date }} - {{ $sprint->end_date }}</p>
                </div>
                <div class="flex justify-between">
                    {{-- Icons Left --}}
                    <div class="flex gap-x-2">
                        <div class="flex flex-col">
                            @if ($sprint->status == 'done')
                                <div>
                                    <p class="text-2xs font-semibold uppercase text-gray-500 dark:text-white">{{ __('sprints.done') }}</p>
                                </div>
                                <div class="flex justify-center">
                                    <i class="fi fi-sr-calendar-check dark:text-white"></i>
                                </div>
                            @elseif ($sprint->status == 'active')
                                <div>
                                    <p class="text-2xs font-semibold uppercase text-gray-500 dark:text-white">{{ __('sprints.days_left') }}</p>
                                </div>
                                <div class="flex justify-center gap-x-2">
                                    <i class="fi fi-ss-calendar dark:text-white"></i>
                                    @if (\Carbon\Carbon::parse($sprint->end_date)->format('d') - \Carbon\Carbon::now()->format('d') < 0)
                                        <p class="text-red-300">
                                            {{ \Carbon\Carbon::parse($sprint->end_date)->format('d') - \Carbon\Carbon::now()->format('d') }}
                                        </p>
                                    @else
                                        <p class="dark:text-white">
                                            {{ \Carbon\Carbon::parse($sprint->end_date)->format('d') - \Carbon\Carbon::now()->format('d') }}
                                        </p>
                                    @endif
                                </div>
                            @elseif ($sprint->status == 'inactive')
                                <div>
                                    <p class="text-2xs font-semibold uppercase text-gray-500 dark:text-white">{{ __('sprints.days_to_start') }}</p>
                                </div>
                                <div class="flex justify-center gap-x-2">
                                    <i class="fi fi-sr-clock-five"></i>
                                    <p class="dark:text-white">
                                        @if (\Carbon\Carbon::parse($sprint->start_date)->format('d') - \Carbon\Carbon::now()->format('d') < 0)
                                            {{ \Carbon\Carbon::parse($sprint->start_date)->format('d') - \Carbon\Carbon::now()->format('d') }}
                                        @else
                                            {{ \Carbon\Carbon::parse($sprint->start_date)->format('d') - \Carbon\Carbon::now()->format('d') }}
                                        @endif
                                    </p>
                                </div>
                            @endif
                        </div>
                        <div class="flex flex-col">
                            <div>
                                <p class="text-2xs font-semibold uppercase text-gray-500 dark:text-white">{{ __('sprints.cards') }}</p>
                            </div>
                            <div class="flex gap-x-2">
                                <i class="fi fi-sr-list-check"></i>
                                <p class="dark:text-white">{{ count($sprint->cards) }}</p>
                            </div>
                        </div>
                    </div>
                    {{-- Icons Right --}}
                    <div class="flex gap-x-2">
                        <div class="flex flex-col">
                            @if ($sprint->status == 'active')
                                <div class="group">
                                    <p class="text-2xs font-semibold uppercase text-gray-500 dark:text-white group-hover:text-blue-500">{{ __('sprints.complete') }}</p>
                                    <button wire:click="completeSprint('{{ $sprint->id }}')" class="w-full flex justify-center">
                                        <i class="fi fi-br-stop-circle group-hover:text-blue-500"></i>
                                    </button>
                                </div>
                            @elseif ($sprint->status == 'inactive')
                                <div class="group">
                                    <p class="text-2xs font-semibold uppercase text-gray-500 dark:text-white group-hover:text-blue-500">{{ __('sprints.start') }}</p>
                                    <button wire:click="startSprint('{{ $sprint->id }}')" class="w-full flex justify-center">
                                        <i class="fi fi-br-play-circle group-hover:text-blue-500"></i>
                                    </button>
                                </div>
                            @endif
                        </div>
                        <div>
                            <div class="group">
                                <p class="text-2xs font-semibold uppercase text-gray-500 dark:text-white group-hover:text-blue-500">{{ __('sprints.edit') }}</p>
                                <button wire:click="editSprintSetId('{{ $sprint->id }}')" class="w-full flex justify-center">
                                    <i class="fi fi-br-edit group-hover:text-blue-500"></i>
                                </button>
                            </div>
                        </div>
                        <div>
                            <div class="group">
                                <p class="text-2xs font-semibold uppercase text-gray-500 dark:text-white group-hover:text-red-500">{{ __('sprints.delete') }}</p>
                                <button wire:click="deleteSprint('{{ $sprint->id }}')" class="w-full flex justify-center">
                                    <i class="fi fi-br-trash group-hover:text-red-500"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-2 md:col-span-3 lg:col-span-4 bg-white dark:bg-gray-800 shadow-md rounded-lg p-4 mt-4">
                <p class="text-lg text-center font-bold dark:text-white">{{ __('sprints.no_sprints_found') }}</p>
            </div>
        @endforelse
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

    {{-- Edit Sprint Modal --}}
    <x-pmt-modal wire:model="editSprintModal" id="">
        <x-slot name="title">
            {{ __('sprints.edit_sprint') }}
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
            <x-primary-button wire:click="updateSprint" wire:loading.attr="disabled" id="test">
                {{ __('sprints.update') }}
            </x-primary-button>
            <x-secondary-button wire:click="$toggle('editSprintModal')" wire:loading.attr="disabled">
                {{ __('sprints.cancel') }}
            </x-secondary-button>
        </x-slot>
    </x-pmt-modal>

    {{-- Delete Sprint Modal --}}
    <x-dialog-modal wire:model="deleteSprintModal">
        <x-slot name="title">
            {{ __('sprints.dialog_delete_title') }}
        </x-slot>

        <x-slot name="content">
            {{ __('sprints.dialog_delete_text') }}
        </x-slot>

        <x-slot name="footer">
            <x-danger-button class="ml-2" wire:click="destroySprint" wire:loading.attr="disabled">
                {{ __('sprints.delete') }}
            </x-danger-button>

            <x-secondary-button wire:click="$toggle('deleteSprintModal')" wire:loading.attr="disabled">
                {{ __('sprints.cancel') }}
            </x-secondary-button>            
        </x-slot>
    </x-dialog-modal>
</div>
