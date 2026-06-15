<div class="fixed inset-0 z-50 overflow-y-auto">

    {{-- Background overlay --}}
    <div class="fixed inset-0 bg-gray-900/75"></div>

    {{-- Close Button --}}
    <div class="relative z-50 flex justify-end mr-8 pt-4">
        <div
            wire:click="closeModal"
            class="bg-gray-800 text-white rounded-lg w-8 h-8 flex items-center justify-center cursor-pointer">
            <i class="fi fi-br-cross"></i>
        </div>
    </div>

    {{-- Modal Content --}}
    <div class="relative z-40 flex justify-center w-[85%] h-[90vh] mx-auto">
        <div class="w-full h-full flex flex-col bg-gray-100 dark:bg-gray-800 rounded-sm p-4">
            {{-- Top Bar --}}
            <div class="flex justify-between">
                {{-- Title --}}
                <div x-data="{ isEditing: false }">
                    <div x-show="!isEditing" class="flex gap-4 mt-2">
                        <p class="text-gray-600">#{{ $card->id }}</p>
                        <p class="text-gray-900 dark:text-gray-400 font-bold">{{ $card->title }}</p>
                        <i class="fi fi-bs-pencil dark:text-gray-400 hover:text-blue-500 cursor-pointer" @click="isEditing = true"></i>
                    </div>

                    <div x-show="isEditing" class="flex gap-4 mt-2">
                        <p class="text-gray-600">#{{ $card->id }}</p>
                        <x-forms.text-input 
                            wire:model="cardTitle" 
                            @keydown.enter.prevent="isEditing = false"
                            x-on:blur="isEditing = false"
                            wire:blur="saveTitle"
                            autofocus
                        />
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    {{-- Approval Status --}}
                    @php
                        $approvalStatus = strtolower($card->approval_status);
                        $approvalStatusKey = str_replace(' ', '_', $approvalStatus);
                        $statusColors = [
                            'approved' => 'text-green-500 hover:bg-green-500 hover:text-white border-green-500 px-4',
                            'needs work' => 'text-yellow-500 hover:bg-yellow-500 hover:text-white border-yellow-500 px-2',
                            'rejected' => 'text-red-500 hover:bg-red-500 hover:text-white border-red-500 px-4',
                        ];
                        $statusColor = $statusColors[$approvalStatus] ?? 'text-gray-800 border-gray-800 px-4';
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
                            <x-dropdown.dropdown-button icon="" margin="mb-1" label="{{ __('board.status.' . $optionStatusKey) }}" wireClick="updateApprovalStatus('{{ $status }}')" alpineClick="open = false" />
                        @endforeach
                    </x-dropdown.wrapper>

                    {{-- Total Actual Time --}}
                    <div class="flex items-center gap-2 text-white bg-gray-200 dark:bg-gray-900 rounded-md px-2.5 py-1.5" data-tooltip-target="modal-actual-time-{{ $card->id }}">
                        <i class="fi fi-sr-hourglass text-sm"></i>
                        <p class="text-sm">{{ $card->tasks->sum('actual_time') }}h</p>

                        <x-tooltip id="modal-actual-time-{{ $card->id }}" content="{{ __('board.labels.total_actual_time') }}" />
                    </div>

                    {{-- Total Estimated Time --}}
                    <div class="flex items-center gap-2 text-white bg-gray-200 dark:bg-gray-900 rounded-md px-2.5 py-1.5" data-tooltip-target="modal-estimated-time-{{ $card->id }}">
                        <i class="fi fi-sr-clock text-sm"></i>
                        <p class="text-sm">{{ $card->tasks->sum('estimated_time') }}h</p>

                        <x-tooltip id="modal-estimated-time-{{ $card->id }}" content="{{ __('board.labels.total_estimated_time') }}" />
                    </div>

                    {{-- Deadline --}}
                    <div x-data="{ editing: false }" wire:key="deadline-{{ $card->id }}" data-tooltip-target="modal-deadline-{{ $card->id }}">
                        <div 
                            @click="editing = true" 
                            class="flex items-center gap-2 bg-gray-200 dark:bg-gray-900 rounded-md px-2.5 py-1.5 cursor-pointer"
                        >
                            <i class="fi fi-sr-calendar text-gray-700 dark:text-white text-sm"></i>
                            <span class="text-gray-700 dark:text-white text-sm">
                                {{ $card->deadline ? \Carbon\Carbon::parse($card->deadline)->format('M d, H:i') : '-' }}
                            </span>

                            <x-tooltip id="modal-deadline-{{ $card->id }}" content="{{ __('board.labels.deadline') }}" />
                        </div>

                        {{-- Edit Deadline --}}
                        <div 
                            x-show="editing" 
                            @click.outside="editing = false" 
                            class="absolute mt-1 z-10 bg-gray-200 dark:bg-gray-900 rounded-lg p-2 shadow-lg"
                        >
                            <input 
                                type="datetime-local"
                                class="w-full text-sm px-2 py-1 rounded border border-gray-300 focus:outline-none"
                                wire:model.live="deadlineInput"
                                wire:keydown.enter.prevent="updateCardDeadline"
                                wire:blur="updateCardDeadline"
                                @keydown.enter="editing = false"
                            />
                        </div>
                    </div>

                    {{-- Users --}}
                    @php
                        $assignees = $card->assignees;
                        $maxVisible = 3;

                        $visibleAssignees = $assignees->take($maxVisible);
                        $remainingCount = $assignees->count() - $maxVisible;
                    @endphp

                    <div class="flex justify-end">
                        <div x-data="{ open: false }" class="relative flex items-center gap-x-2 bg-gray-200 dark:bg-gray-900 rounded-md px-2.5 py-1.5">
                            <i 
                                @click="open = !open"
                                class="fi fi-sr-users text-sm text-gray-700 dark:text-white cursor-pointer"
                            ></i>

                            <div 
                                x-show="open"
                                x-transition
                                @click.outside="open = false"
                                class="absolute right-0 top-full mt-2 z-50 bg-gray-200 dark:bg-gray-100 border border-zinc-400 rounded shadow-lg w-64"
                            >

                                {{-- Search --}}
                                <div class="p-2 border-b border-gray-300">
                                    <input 
                                        x-ref="search"
                                        x-init="$watch('open', value => value && $refs.search.focus())"
                                        type="text"
                                        placeholder="Search users..."
                                        class="w-full text-sm px-2 py-1 rounded border border-gray-300 focus:outline-none"
                                        wire:model.live.debounce.300ms="search"
                                    />
                                </div>

                                {{-- User list --}}
                                <ul wire:key="users-{{ $card->id }}-{{ $card->assignees->count() }}" class="h-48 p-2 text-sm overflow-y-auto" id="user-list-{{ $card->id }}">
                                    @foreach ($filteredUsers as $user)
                                        @php
                                            $isAssigned = $card->assignees->contains('user_uuid', $user->uuid);
                                            $profilePicture = $user->profile_picture
                                                ? asset('storage/' . $user->profile_picture)
                                                : 'https://ui-avatars.com/api/?name=' . urlencode($user->name);
                                        @endphp

                                        <li class="user-item flex items-center p-2 hover:bg-gray-300 rounded"
                                            data-name="{{ strtolower($user->name) }}">

                                            <label class="w-full flex items-center justify-between cursor-pointer">
                                                
                                                {{-- Left: avatar + name --}}
                                                <div class="flex items-center">
                                                    <img class="w-5 h-5 me-2 rounded-full" src="{{ $profilePicture }}">
                                                    <span>{{ $user->name }}</span>
                                                </div>

                                                {{-- Right: checkbox --}}
                                                <input 
                                                    type="checkbox"
                                                    value="{{ $user->uuid }}"
                                                    {{ $isAssigned ? 'checked' : '' }}
                                                    class="w-4 h-4 border border-gray-400 rounded bg-white"
                                                    wire:change="toggleAssignee('{{ $user->uuid }}', $event.target.checked)"
                                                >
                                            </label>
                                        </li>
                                    @endforeach
                                </ul>

                                {{-- Footer --}}
                                <div class="p-2 border-t border-gray-300">
                                    <button 
                                        type="button"
                                        class="w-full text-xs bg-red-500 hover:bg-red-600 text-white rounded px-2 py-1 cursor-pointer"
                                        wire:click="clearAssignees"
                                    >
                                        {{ __('board.labels.clear_assignees') }}
                                    </button>
                                </div>
                            </div>

                            @if ($assignees->count() === 0)
                                <p class="text-xs text-gray-700 dark:text-white">
                                    {{ __('board.messages.no_users_assigned') }}
                                </p>
                            @else
                                <div class="flex -space-x-2">
                                    @foreach ($visibleAssignees as $assignee)
                                        @php
                                            $profilePicture = $assignee->user->profile_picture
                                                ? asset('storage/' . $assignee->user->profile_picture)
                                                : null;
                                        @endphp

                                        <img 
                                            src="{{ $profilePicture ?? 'https://ui-avatars.com/api/?name=' . urlencode($assignee->user->name ?? 'U') . '&background=16a34a&color=ffffff' }}" 
                                            alt="{{ $assignee->user->name }}" 
                                            title="{{ $assignee->user->name }}"
                                            class="w-5 h-5 rounded-full border border-white dark:border-gray-800"
                                        />
                                    @endforeach

                                    @if ($remainingCount > 0)
                                        <div class="w-5 h-5 flex items-center justify-center rounded-full bg-gray-300 dark:bg-gray-700 text-2xs text-gray-800 dark:text-white border border-white dark:border-gray-800">
                                            +{{ $remainingCount }}
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div 
                        x-data="{ open: false, showMoveOptions: false }"
                        class="relative"
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
                                wireClick="makeACopy"
                                alpineClick="open = false"
                            />

                            @if($isProjectAdminOrOwner)
                                <x-containers.divider margin="my-2" color="gray-400" />

                                <x-dropdown.dropdown-button
                                    icon="rr-trash"
                                    :label="__('board.buttons.delete')"
                                    color="red-500"
                                    wireClick="deleteCard"
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

                                <x-buttons.primary-button wire:click="moveCard">
                                    {{ __('board.buttons.move') }}
                                </x-buttons.primary-button>
                            </div>
                        </x-dropdown.wrapper>
                    </div>
                </div>
            </div>

            <div class="w-full h-full flex-1 min-h-0 flex flex-col bg-white dark:bg-gray-900 rounded-sm p-4 mt-2">
                {{-- Description --}}
                <div 
                    x-data="{ isEditing: false }"
                    class="w-full flex gap-2"
                >
                    <div
                        x-show="isEditing"
                        class="w-full"
                    >
                        <x-forms.text-area
                            wire:model="cardDescription"
                            rows="3"
                            @keydown.enter.prevent="isEditing = false"
                            x-on:blur="isEditing = false"
                            wire:blur="saveDescription"
                        />
                    </div>

                    <div
                        x-show="!isEditing"
                    >
                        <p class="text-gray-500 dark:text-gray-400">{{ $cardDescription ? $cardDescription : __('board.messages.no_description') }}</p>
                    </div>

                    <i class="fi fi-bs-pencil dark:text-gray-400 hover:text-blue-500 cursor-pointer" x-show="!isEditing" @click="isEditing = true"></i>
                </div>

                <x-containers.divider margin="my-6" color="gray-400" />

                {{-- Task Columns --}}
                <div wire:sortable-group="updateCardOrder" class="h-full grid grid-cols-3 gap-4 flex-1 min-h-0">
                    @foreach ($columns as $column)
                        <div 
                            class="bg-gray-100 dark:bg-gray-800 rounded-sm p-2 h-full flex flex-col min-h-0 sortable-column"
                            wire:key="column-{{ $column['type'] }}"
                        >
                            {{-- <p class="text-gray-900 dark:text-gray-400 font-bold mb-2">{{ $column['name'] }}</p> --}}
                            <div class="flex justify-between mb-2">
                                {{-- Count + Title --}}
                                <div class="flex gap-2">
                                    <div class="flex items-center justify-center rounded-md text-sm font-bold bg-{{ $column['color'] }} text-white px-1.5 py-0.5">{{ count($column['cards']) }}</div>
                                    <p class="text-{{ $column['color'] }} font-bold">{{ $column['name'] }}</p>
                                </div>

                                {{-- Add task button --}}

                            </div>

                            {{-- Tasks --}}
                            <div class="flex-1 min-h-32 space-y-2" wire:sortable-group.item-group="{{ $column['type'] }}" wire:sortable-group.options="{ animation: 100 }">
                                @foreach ($column['cards'] as $task)
                                    <div wire:key="task-{{ $task->id }}" wire:sortable-group.item="{{ $task->id }}">
                                        <livewire:components.board.task-card 
                                            :task="$task"
                                            :users="$users"
                                            wire:key="task-{{ $task->id }}"
                                        />
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

</div>