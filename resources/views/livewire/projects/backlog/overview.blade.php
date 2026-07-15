<div>
    {{-- Breadcrumbs --}}
    <x-slot name="breadcrumbs">
        <x-breadcrumbs margin="mb-2" :items="[
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
                'url' => route('projects.backlog.render', ['uuid' => $project->uuid]),
                'label' => __('backlog.titles.backlog_overview'),
            ]
        ]" />
    </x-slot>

    {{-- Top Bar --}}
    <x-containers.main padding="4">
        <div class="flex justify-between items-center">
            <div class="flex gap-4">
                <div>
                    <p class="text-sm font-bold uppercase dark:text-white">
                        {{ __('backlog.labels.buckets') }}
                    </p>
                    <div class="flex justify-center gap-2">
                        <i class="fi fi-rr-bucket dark:text-white"></i>
                        <p class="dark:text-white">{{ $backlogs->count() }}</p>
                    </div>
                </div>
                <div>
                    <p class="text-sm font-bold uppercase dark:text-white">
                        {{ __('backlog.labels.total_cards') }}
                    </p>
                    <div class="flex justify-center gap-2">
                        <i class="fi fi-rr-cards-blank dark:text-white"></i>
                        <p class="dark:text-white">{{ $backlogs->sum(fn($backlog) => $backlog->cards->count()) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </x-containers.main>

    {{-- Content --}}
    <div class="md:h-[80vh] 3xl:h-[85vh] flex gap-4 mt-4">
        <x-containers.main padding="4" class="w-1/5 overflow-y-auto">
            <div class="flex justify-between">
                <h2 class="font-bold text-lg dark:text-white">{{ __('backlog.labels.buckets') }}</h2>
                <i class="fi fi-rr-plus text-gray-800 dark:text-gray-200 me-1 cursor-pointer" wire:click="$set('showBucketCreationModal', true)"></i>
            </div>
            <div class="flex flex-col gap-y-2 mt-4">
                @foreach ($backlogs as $backlog)
                    <div 
                        @if ($backlog->uuid === $selectedBacklog?->uuid) 
                            class="w-full bg-blue-300 hover:bg-blue-400 dark:bg-blue-700 dark:hover:bg-blue-600 rounded-md p-2 cursor-pointer"
                        @else
                            class="w-full bg-gray-200 hover:bg-gray-300 dark:bg-gray-900 dark:hover:bg-gray-800 rounded-md p-2 cursor-pointer"
                        @endif
                        wire:click="openBacklog('{{ $backlog->uuid }}')"
                        wire:key="backlog-{{ $backlog->uuid }}"
                    >
                        <p class="dark:text-white">{{ $backlog->name }}</p>
                    </div>
                @endforeach
            </div>
        </x-containers.main>
        <div class="w-4/5 overflow-y-auto overflow-x-hidden">
            @if ($selectedBacklog)
                @if ($selectedBacklog->cards->count() > 0)
                    @foreach ($selectedBacklog->cards as $card)
                        <div 
                            class="flex justify-between bg-white dark:bg-gray-800 rounded-lg px-4 py-2 mb-4 shadow-md"
                            wire:key="card-{{ $card->id }}"
                        >
                            {{-- ID & Title --}}
                            <div class="flex items-center gap-2">
                                <p class="text-sm text-gray-500 dark:text-gray-400">#{{ $card->id }}</p>
                                <h3 
                                    class="text-lg font-semibold dark:text-white hover:text-blue-500 cursor-pointer"
                                    wire:click="selectCard('{{ $card->id }}')"
                                >
                                    {{ $card->title }}
                                </h3>
                            </div>

                            {{-- Approval Status, Task Count, Actions --}}
                            <div class="flex gap-4">
                                {{-- Approval Status --}}
                                @php
                                    $approvalStatus = strtolower($card->approval_status);
                                    $approvalStatusKey = str_replace(' ', '_', $approvalStatus);
                                    $statusColors = [
                                        'approved' => 'text-green-500 hover:bg-green-500 hover:text-white border-green-500 px-4',
                                        'needs work' => 'text-yellow-500 hover:bg-yellow-500 hover:text-white border-yellow-500 px-2',
                                        'rejected' => 'text-red-500 hover:bg-red-500 hover:text-white border-red-500 px-4',
                                    ];
                                    $statusColor = $statusColors[$approvalStatus] ?? 'text-gray-800 dark:text-gray-400 border-gray-800 dark:border-gray-400 px-4';
                                @endphp

                                <x-dropdown.wrapper
                                    state="open"
                                    :useOwnState="true"
                                    tooltipId="approval-status-{{ $card->id }}"
                                    :tooltip="__('board.labels.change_approval_status')"
                                    width="w-52"
                                    align="right"
                                    margin="mt-4"
                                >
                                    <x-slot name="handle">
                                        <div 
                                            class="flex items-center py-1.5 rounded text-sm font-semibold border {{ $statusColor }} cursor-pointer"
                                            data-tooltip-target="approval-status-{{ $card->id }}"
                                        >
                                            <p>{{ __('board.status.' . $approvalStatusKey) }}</p>
                                        </div>
                                    </x-slot>
                                
                                    <p class="text-gray-900 text-center text-sm font-bold">
                                        {{ __('board.labels.change_approval_status') }}
                                    </p>

                                    <x-containers.divider margin="my-2" color="gray-400" />

                                    @foreach ($approvalStatuses as $status)
                                        @php
                                            $optionStatusKey = str_replace(' ', '_', strtolower($status));
                                        @endphp
                                        <x-dropdown.dropdown-button icon="" margin="mb-1" label="{{ __('board.status.' . $optionStatusKey) }}" wireClick="updateApprovalStatus('{{ $card->id }}', '{{ $status }}')" alpineClick="open = false" />
                                    @endforeach
                                </x-dropdown.wrapper>

                                {{-- Task Count --}}
                                <div class="flex items-center gap-2">
                                    <i class="fi fi-rr-list-check text-gray-800 dark:text-gray-200"></i>
                                    <p class="dark:text-white">{{ $card->tasks->count() }}</p>
                                </div>

                                {{-- Actions --}}
                                <div 
                                    x-data="{ open: false, showMoveOptions: false }"
                                    class="relative flex items-center"
                                >
                                    <x-dropdown.wrapper
                                        state="open"
                                        :useOwnState="false"
                                        icon="bs-menu-dots-vertical"
                                        tooltipId="scard-actions-{{ $card->id }}"
                                        tooltip="{{ __('board.labels.card_actions') }}"
                                        width="w-52"
                                        align="right"
                                        margin="mt-4"
                                    >
                                        <div class="text-sm text-gray-900">
                                            <p class="text-center font-bold">
                                                {{ __('board.labels.actions') }} - Card #{{ $card->id }}
                                            </p>
                                        </div>

                                        <x-containers.divider margin="my-2" color="gray-400" />

                                        <x-dropdown.dropdown-button
                                            icon="rr-assign"
                                            :label="__('board.buttons.assign_to_me')"
                                            wireClick="assignToMe"
                                            alpineClick="open = false"
                                        />

                                        <x-containers.divider margin="my-2" color="gray-400" />

                                        @if ($isProjectAdminOrOwner)
                                            <x-dropdown.dropdown-button
                                                icon="rr-move-to-folder-2"
                                                :label="__('board.buttons.move_to')"
                                                alpineClick="open = false; showMoveOptions = true"
                                            />
                                        @endif

                                        <x-dropdown.dropdown-button
                                            icon="rr-copy"
                                            :label="__('board.buttons.make_a_copy')"
                                            wireClick="makeACopy('{{ $card->id }}')"
                                            alpineClick="open = false"
                                        />

                                        @if($isProjectAdminOrOwner)
                                            <x-containers.divider margin="my-2" color="gray-400" />

                                            <x-dropdown.dropdown-button
                                                icon="rr-trash"
                                                :label="__('board.buttons.delete')"
                                                color="red-500"
                                                wireClick="deleteCard('{{ $card->id }}')"
                                                alpineClick="open = false"
                                            />
                                        @endif
                                    </x-dropdown.wrapper>

                                    <x-dropdown.wrapper
                                        state="showMoveOptions"
                                        :useOwnState="false"
                                        width="w-48"
                                        align="right"
                                        margin="mt-4"
                                    >
                                        <p class="text-gray-900 text-center text-sm font-bold">
                                            {{ __('board.buttons.move_to') }}
                                        </p>

                                        <x-containers.divider margin="my-2" color="gray-400" />

                                        <div class="flex flex-col gap-2">
                                            @php
                                                $selectedProjectModel = $projects->firstWhere('uuid', $selectedProjectUuid);
                                            @endphp

                                            <p class="text-center">
                                                {{ __('board.titles.select_destination') }}
                                            </p>

                                            <x-forms.select 
                                                wire:key="projects-{{ $selectedProjectUuid }}"
                                                :options="$projects->map(fn($project) => ['value' => $project->uuid, 'label' => $project->name])->values()->toArray()"
                                                wire:model="selectedProjectUuid"
                                            />

                                            <x-forms.select
                                                :options="[
                                                    ['value' => 'sprint', 'label' => __('board.labels.sprints')],
                                                    ['value' => 'backlog', 'label' => __('board.labels.backlogs')]
                                                ]"
                                                wire:model="sprintOrBacklog"
                                            />

                                            <x-forms.select
                                                wire:key="entities-{{ $selectedProjectUuid }}"
                                                :options="$entities->map(fn($entity) => ['value' => $entity->uuid, 'label' => $entity->name])->values()->toArray()"
                                                wire:model="selectedEntityUuid"
                                            />

                                            @if ($sprintOrBacklog === 'sprint')
                                                <x-forms.select
                                                    wire:key="columns-{{ $selectedProjectUuid }}"
                                                    :options="$selectedProjectModel?->columns
                                                        ->sortBy('position')
                                                        ->map(fn($column) => ['value' => $column->id, 'label' => $column->name])
                                                        ->values()
                                                        ->toArray()
                                                    "
                                                    wire:model="column"
                                                />
                                            @endif

                                            <x-forms.select
                                                :options="[
                                                    ['value' => 'top', 'label' => __('board.labels.top')],
                                                    ['value' => 'bottom', 'label' => __('board.labels.bottom')]
                                                ]"
                                                wire:model="position"
                                            />

                                            <x-buttons.primary-button wire:click="moveCard('{{ $card->id }}')">
                                                {{ __('board.buttons.move') }}
                                            </x-buttons.primary-button>
                                        </div>
                                    </x-dropdown.wrapper>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="flex justify-center bg-yellow-100 p-4 rounded-lg">
                        <p class="text-yellow-800">{{ __('backlog.labels.no_cards') }}</p>
                    </div>
                @endif
            @elseif ($backlogs->count() === 0)
                <div class="flex justify-center bg-red-100 p-4 rounded-lg">
                    <p class="text-red-800">{{ __('backlog.labels.no_backlog') }}</p>
                </div>
            @else
                <div class="flex justify-center bg-yellow-100 p-4 rounded-lg">
                    <p class="text-yellow-800">{{ __('backlog.labels.select_bucket') }}</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Selected Card Modal --}}
    {{-- 
        TODO: Rebuild a modal for the backlog, 
        TODO: because the current modal is designed for sprints and trying to support both is going to be hell 
    --}}
    @if ($selectedCard)
        <livewire:components.backlog.modal 
            :project="$project" 
            :card="$selectedCard" 
            :users="$users" 
            :key="'backlog-card-modal-' . $selectedCard->id"
        />
    @endif

    {{-- Delete Card Modal --}}
    <x-modals.modal wire:model="showDeleteCardModal">
        <x-slot name="title">
            <p class="text-center">
                {{ __('backlog.titles.delete_card') }}
            </p>
        </x-slot>
        <x-slot name="content">
            <p class="text-center">
                {!! __('backlog.messages.confirm_delete_card') !!}
            </p>
        </x-slot>
        <x-slot name="footer">
            <x-buttons.secondary-button wire:click="$set('showDeleteCardModal', false)">
                {{ __('general.buttons.cancel') }}
            </x-buttons.secondary-button>
            <x-buttons.danger-button wire:click="confirmDeleteCard">
                {{ __('general.buttons.delete') }}
            </x-buttons.danger-button>
        </x-slot>
    </x-modals.modal>

    {{-- Delete Task Modal --}}
    <x-modals.modal wire:model="showDeleteTaskModal">
        <x-slot name="title">
            <p class="text-center">
                {{ __('backlog.titles.delete_task') }}
            </p>
        </x-slot>
        <x-slot name="content">
            <p class="text-center">
                {!! __('backlog.messages.confirm_delete_task') !!}
            </p>
        </x-slot>
        <x-slot name="footer">
            <x-buttons.secondary-button wire:click="$set('showDeleteTaskModal', false)">
                {{ __('general.buttons.cancel') }}
            </x-buttons.secondary-button>
            <x-buttons.danger-button wire:click="confirmDeleteTask">
                {{ __('general.buttons.delete') }}
            </x-buttons.danger-button>
        </x-slot>
    </x-modals.modal>
</div>
