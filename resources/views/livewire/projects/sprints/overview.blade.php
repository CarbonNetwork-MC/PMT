<div>
    {{-- Breadcrumbs --}}
    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[
            [
                'icon' => 'fi fi-rs-house-chimney',
                'url' => route('dashboard.render'),
                'label' => '',
            ],
            [
                'icon' => '',
                'url' => route('projects.render'),
                'label' => __('sidebar.projects.title'),
            ],
            [
                'icon' => '',
                'url' => route('projects.dashboard.render', ['uuid' => $project->uuid]),
                'label' => $project->name,
            ],
            [
                'icon' => '',
                'url' => route('projects.sprints.render', ['uuid' => $project->uuid]),
                'label' => __('sprints.titles.sprint-overview'),
            ]
        ]" />
    </x-slot>

    <x-containers.main>
        <div class="flex justify-between">
            <div class="flex gap-6">
                <div>
                    <p class="text-sm font-bold uppercase dark:text-white">{{ __('sprints.labels.sprints') }}</p>
                    <div class="flex justify-center gap-2">
                        <i class="fi fi-sr-running dark:text-white"></i>
                        <p class="dark:text-white">{{ $sprintCount }}</p>
                    </div>
                </div>
                <div>
                    <p class="text-sm font-bold uppercase dark:text-white">{{ __('sprints.labels.active-sprints') }}</p>
                    <div class="flex justify-center gap-2">
                        <i class="fi fi-sr-running dark:text-white"></i>
                        <p class="dark:text-white">{{ $activeSprints }}</p>
                    </div>
                </div>
                <div>
                    <p class="text-sm font-bold uppercase dark:text-white">{{ __('sprints.labels.completed-sprints') }}</p>
                    <div class="flex justify-center gap-2">
                        <i class="fi fi-sr-check dark:text-white"></i>
                        <p class="dark:text-white">{{ $completedSprints }}</p>
                    </div>
                </div>
                <div>
                    <p class="text-sm font-bold uppercase dark:text-white">{{ __('sprints.labels.archived-sprints') }}</p>
                    <div class="flex justify-center gap-2">
                        <i class="fi fi-sr-archive dark:text-white"></i>
                        <p class="dark:text-white">{{ $archivedSprints }}</p>
                    </div>
                </div>
            </div>
            
            <x-buttons.primary-button href="{{ route('projects.sprints.new.render', ['uuid' => $project->uuid]) }}">
                {{ __('sprints.buttons.new_sprint') }}
            </x-buttons.primary-button>
        </div>
    </x-containers.main>

    <div class="grid grid-cols-3 lg:grid-cols-4 3xl:grid-cols-5 gap-4 mt-4">
        @forelse ($sprints as $sprint)
            {{-- TODO: link to board for $sprint --}}
            {{-- TODO: burndown chart for $sprint --}}
            <x-project.sprint-card :sprint="$sprint" />
        @empty
            <div class="col-span-3 lg:col-span-4 3xl:col-span-5 bg-white dark:bg-gray-800 shadow-md rounded-lg p-4">
                <p class="text-center text-gray-600 dark:text-gray-300">
                    {{ __('sprints.messages.no_sprints') }}
                </p>
            </div>
        @endforelse
    </div>

    {{-- Edit Sprint Modal --}}
    <x-modals.modal size="lg" wire:model="showEditModal">
        <x-slot name="title">
            <p class="text-center">
                {!! __('sprints.modals.edit_sprint_title', ['name' => $editingSprint->name ?? '']) !!}
            </p>
        </x-slot>
        <x-slot name="content">
            <div class="flex justify-center py-4">
                <div class="w-4/5 grid grid-cols-2 gap-x-4 gap-y-6">
                    {{-- Name --}}
                    <div class="col-span-1">
                        <x-forms.text-input wire:model="name" label="{{ __('sprints.labels.name') }}" placeholder="{{ __('sprints.labels.name') }}" required />
                    </div>

                    {{-- Status --}}
                    <div class="col-span-1">
                        <x-forms.select
                            placeholder="{{ __('sprints.labels.select_status') }}"
                            wire:model="status"
                            label="{{ __('sprints.labels.status') }}"
                            {{-- :options="collect($statuses)->mapWithKeys(fn($s) => [$s => __('sprints.statuses.' . $s)])->toArray()" --}}
                            :options="collect($statuses)->map(function ($status) {
                                return [
                                    'value' => $status,
                                    'label' => __('sprints.statuses.' . $status),
                                ];
                            })"
                            required
                        />
                    </div>

                    {{-- Start Date --}}
                    <div class="col-span-1">
                        <x-forms.date-input wire:model="start_date" label="{{ __('sprints.labels.start_date') }}" required />
                    </div>

                    {{-- End Date --}}
                    <div class="col-span-1">
                        <x-forms.date-input wire:model="end_date" label="{{ __('sprints.labels.end_date') }}" required />
                    </div>
                </div>
            </div>
        </x-slot>
        <x-slot name="footer">
            <x-buttons.secondary-button wire:click="$set('showEditModal', false)">
                {{ __('general.buttons.cancel') }}
            </x-buttons.secondary-button>
            <x-buttons.primary-button wire:click="updateSprint">
                {{ __('general.buttons.save') }}
            </x-buttons.primary-button>
        </x-slot>
    </x-modals.modal>

    {{-- Delete Sprint Modal --}}
    <x-modals.modal wire:model="showDeleteModal">
        <x-slot name="title">
            <p class="text-center text-red-500">
                {{ __('sprints.modals.delete_sprint_title', ['name' => $deletingSprint->name ?? '']) }}
            </p>
        </x-slot>
        <x-slot name="content">
            <p class="text-center">
                {!! __('sprints.modals.delete_sprint_message', ['name' => $deletingSprint->name ?? '']) !!}
            </p>
        </x-slot>
        <x-slot name="footer">
            <x-buttons.secondary-button wire:click="$set('showDeleteModal', false)">
                {{ __('general.buttons.cancel') }}
            </x-buttons.secondary-button>
            <x-buttons.danger-button wire:click="deleteSprint({{ $deletingSprint->uuid ?? '' }})">
                {{ __('general.buttons.delete') }}
            </x-buttons.danger-button>
        </x-slot>
    </x-modals.modal>
</div>
