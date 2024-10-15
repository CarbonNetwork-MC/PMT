<div>
    <div class="w-full flex justify-between bg-white dark:bg-gray-800 shadow-md rounded-lg p-4">
        <div class="flex gap-x-4">
            <div>
                <p class="text-sm font-bold uppercase dark:text-white">{{ __('sprints.sprints') }}</p>
                <div class="flex gap-x-2">
                    <i class="fi fi-sr-running dark:text-white"></i>
                    <p class="dark:text-white">{{ count($sprints) }}</p>
                </div>
            </div>
            <div>
                <p class="text-sm font-bold uppercase dark:text-white">{{ __('sprints.active') }}</p>
                <div class="flex gap-x-2">
                    <i class="fi fi-ss-calendar dark:text-white"></i>
                    <p class="dark:text-white">{{ count($activeSprints) }}</p>
                </div>
            </div>
            <div>
                <p class="text-sm font-bold uppercase dark:text-white">{{ __('sprints.done') }}</p>
                <div class="flex gap-x-2">
                    <i class="fi fi-sr-calendar-check dark:text-white"></i>
                    <p class="dark:text-white">{{ count($doneSprints) }}</p>
                </div>
            </div>
            <div>
                <p class="text-sm font-bold uppercase dark:text-white">{{ __('sprints.archived_sprints') }}</p>
                <div class="flex gap-x-2">
                    <i class="fi fi-sr-archive dark:text-white"></i>
                    <p class="dark:text-white">{{ $archivedSprints->count() }}</p>
                </div>
            </div>
        </div>
        <div class="flex gap-x-2">
            <p wire:click="$toggle('showArchivedSprints')" class="flex items-center bg-blue-500 hover:bg-blue-700 text-white font-bold px-4 rounded cursor-pointer">{{ __('sprints.show_archived_sprints') }}</p>
            <p wire:click="$toggle('createSprintModal')" class="flex items-center bg-blue-500 hover:bg-blue-700 text-white font-bold px-4 rounded cursor-pointer">
                {{ __('sprints.create_sprint') }}
            </p>
        </div>
    </div>

    @if ($showArchivedSprints === false)
        <div class="w-full grid grid-cols-2 gap-x-2 md:grid-cols-3 lg:grid-cols-4">
            @forelse ($sprints as $sprint)
                <div class="col-span-1 bg-white dark:bg-gray-800 shadow-md rounded-lg p-4 mt-4">
                    <div class="flex justify-between items-center">
                        <a href="{{ route('projects.board.render', ['uuid' => $sprint->uuid]) }}" class="text-lg font-bold dark:text-white hover:text-blue-500">{{ $sprint->name }}</a>
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
                                            <p class="text-red-600 dark:text-red-400">
                                                {{ $daysRemaining = \Carbon\Carbon::now()->startOfDay()->diffInDays(\Carbon\Carbon::parse($sprint->end_date)->startOfDay(), false) }}
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
                                        <i class="fi fi-sr-clock-five dark:text-white"></i>
                                        @if (\Carbon\Carbon::parse($sprint->start_date)->format('d') - \Carbon\Carbon::now()->format('d') < 0)
                                            <p class="text-red-600 dark:text-red-400">
                                                {{ \Carbon\Carbon::parse($sprint->start_date)->format('d') - \Carbon\Carbon::now()->format('d') }}
                                            </p>
                                        @else
                                            <p class="dark:text-white">
                                                {{ \Carbon\Carbon::parse($sprint->start_date)->format('d') - \Carbon\Carbon::now()->format('d') }}
                                            </p>
                                        @endif
                                    </div>
                                @endif
                            </div>
                            <div class="flex flex-col">
                                <div>
                                    <p class="text-2xs font-semibold uppercase text-gray-500 dark:text-white">{{ __('sprints.cards') }}</p>
                                </div>
                                <div class="flex gap-x-2">
                                    <i class="fi fi-sr-list-check dark:text-white"></i>
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
                                        <button wire:click="completeSprint('{{ $sprint->uuid }}')" class="w-full flex justify-center">
                                            <i class="fi fi-br-stop-circle group-hover:text-blue-500 dark:text-white"></i>
                                        </button>
                                    </div>
                                @elseif ($sprint->status == 'inactive')
                                    <div class="group">
                                        <p class="text-2xs font-semibold uppercase text-gray-500 dark:text-white group-hover:text-blue-500">{{ __('sprints.start') }}</p>
                                        <button wire:click="startSprint('{{ $sprint->uuid }}')" class="w-full flex justify-center">
                                            <i class="fi fi-br-play-circle group-hover:text-blue-500 dark:text-white"></i>
                                        </button>
                                    </div>
                                @elseif ($sprint->status == 'done')
                                    <div class="group">
                                        <p class="text-2xs font-semibold uppercase text-gray-500 dark:text-white group-hover:text-blue-500">{{ __('sprints.archive') }}</p>
                                        <button wire:click="archiveSprint('{{ $sprint->uuid }}')" class="w-full flex justify-center">
                                            <i class="fi fi-sr-box group-hover:text-blue-500 dark:text-white"></i>
                                        </button>
                                    </div>
                                @endif
                            </div>
                            <div>
                                <div class="group">
                                    <p class="text-2xs font-semibold uppercase text-gray-500 dark:text-white group-hover:text-blue-500">{{ __('sprints.edit') }}</p>
                                    <button wire:click="editSprintSetId('{{ $sprint->uuid }}')" class="w-full flex justify-center">
                                        <i class="fi fi-br-edit group-hover:text-blue-500 dark:text-white"></i>
                                    </button>
                                </div>
                            </div>
                            <div>
                                <div class="group">
                                    <p class="text-2xs font-semibold uppercase text-gray-500 dark:text-white group-hover:text-red-500">{{ __('sprints.delete') }}</p>
                                    <button wire:click="deleteSprint('{{ $sprint->uuid }}')" class="w-full flex justify-center">
                                        <i class="fi fi-br-trash group-hover:text-red-500 dark:text-white"></i>
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
    @else
        <div class="w-full flex justify-end mt-4">
            {{-- Search bar --}}
            <div class="w-1/4">
                <x-input type="text" class="mt-1 block w-full" wire:model.live="search" placeholder="{{ __('sprints.search') }}" />
            </div>
        </div>

        <div class="w-full relative overflow-x-auto shadow-md rounded-md mt-4">
            <table class="w-full text-sm text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 text-left uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th class="p-2">{{ __('sprints.name') }}</th>
                        <th class="p-2">{{ __('sprints.start_date') }}</th>
                        <th class="p-2">{{ __('sprints.end_date') }}</th>
                        <th class="p-2">{{ __('sprints.archived_at') }}</th>
                        <th class="p-2">{{ __('sprints.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($archivedSprints as $sprint)
                        <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                            <td class="p-2">{{ $sprint->name }}</td>
                            <td class="p-2">{{ $sprint->start_date }}</td>
                            <td class="p-2">{{ $sprint->end_date }}</td>
                            <td class="p-2">{{ $sprint->archived_at }}</td>
                            <td class="p-2">
                                <div class="flex gap-x-2">
                                    <i wire:click="restoreSprint('{{ $sprint->uuid }}')" class="fi fi-ss-box-open hover:text-sky-500 dark:text-white cursor-pointer"></i>
                                    <i wire:click="deleteSprint('{{ $sprint->uuid }}')" class="fi fi-br-trash hover:text-red-500 dark:text-white cursor-pointer"></i>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="p-2 text-center" colspan="5">{{ __('sprints.no_archived_sprints_found') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif

    {{-- Create Sprint Modal --}}
    <x-big-modal wire:model="createSprintModal" id="">
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
    </x-big-modal>

    {{-- Edit Sprint Modal --}}
    <x-big-modal wire:model="editSprintModal" id="">
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
                <div class="col-span-1">
                    <x-label for="status" value="{{ __('sprints.status') }}" />
                    <select id="status" class="mt-1 block w-full rounded-md shadow-sm form-select border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600" wire:model.defer="status">
                        <option value="active">{{ __('sprints.active') }}</option>
                        <option value="inactive">{{ __('sprints.inactive') }}</option>
                        <option value="done">{{ __('sprints.done') }}</option>
                    </select>
                    <x-input-error for="status" class="mt-2" />
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
    </x-big-modal>

    {{-- Delete Sprint Modal --}}
    <x-dialog-modal wire:model="deleteSprintModal">
        <x-slot name="title">
            {{ __('sprints.dialog_delete_title_sprint') }}
        </x-slot>

        <x-slot name="content">
            {{ __('sprints.dialog_delete_text_sprint') }}
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
