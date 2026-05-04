<div wire:sortable-group.handle class="w-full bg-white dark:bg-gray-700 rounded-md p-2 text-sm cursor-grabbing">
    {{-- Card Header - ID and Actions --}}
    <div class="flex justify-between">
        <p class="text-xs text-gray-500 dark:text-gray-400">#{{ $card->id }}</p>
        <div x-data="{ open: false, showMoveOptions: false }" class="relative">
            <x-dropdown.wrapper
                state="open"
                :useOwnState="false"
                icon="bs-menu-dots"
                tooltipId="card-actions-{{ $card->id }}"
                tooltip="{{ __('board.labels.card_actions') }}"
            >
                {{-- Dropdown Header --}}
                <div class="text-sm text-gray-900">
                    <p class="text-center font-bold">
                        {{ __('board.labels.actions') }} - Card #{{ $card->id }}
                    </p>
                </div>

                <x-containers.divider margin="my-2" color="gray-400" />

                {{-- Assign To Me --}}
                <x-dropdown.dropdown-button icon="rr-assign" label="{{ __('board.buttons.assign_to_me') }}" wireClick="assignToMe" alpineClick="open = false" />

                <x-containers.divider margin="my-2" color="gray-400" />

                {{-- Move to --}}
                @if ($isProjectAdminOrOwner)
                    <x-dropdown.dropdown-button icon="rr-move-to-folder-2" label="{{ __('board.buttons.move_to') }}" alpineClick="open = false; showMoveOptions = true; showActions = false" />
                @endif

                {{-- Make a copy --}}
                <x-dropdown.dropdown-button icon="rr-copy" label="{{ __('board.buttons.make_a_copy') }}" wireClick="makeACopy" alpineClick="open = false" />

                @if($isProjectAdminOrOwner)
                    <x-containers.divider margin="my-2" color="gray-400" />

                    {{-- Delete --}}
                    <x-dropdown.dropdown-button icon="rr-trash" label="{{ __('board.buttons.delete') }}" color="red-500" wireClick="deleteCard" alpineClick="open = false" />
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

    {{-- Card Name --}}
    <p class="font-bold text-gray-800 dark:text-gray-200 hover:text-blue-500 dark:hover:text-blue-400 cursor-pointer" wire:click="selectCard">{{ $card->title }}</p>

    {{-- Information - Approval Status, Number of tasks, Has Description --}}
    <div class="flex gap-x-2">
        @if ($card->approval_status !== 'None')
            @php
                $statusColors = [
                    'Approved' => 'bg-green-400',
                    'Rejected' => 'bg-red-400',
                    'Needs Work' => 'bg-orange-400',
                ];

                $icons = [
                    'Approved' => 'rr-check',
                    'Rejected' => 'rr-cross text-xs',
                    'Needs Work' => 'rr-time-fast',
                ];

                $translationKeys = [
                    'Approved' => 'approved',
                    'Rejected' => 'rejected',
                    'Needs Work' => 'needs_work',
                ];

                $bgColor = $statusColors[$card->approval_status] ?? 'bg-gray-400';
                $icon = $icons[$card->approval_status] ?? 'rr-question';
                $translationKey = $translationKeys[$card->approval_status] ?? 'unknown';
            @endphp

            <div class="flex items-center {{ $bgColor }} rounded-md text-sm cursor-help px-2 py-0.5" data-tooltip-target="approval-tooltip-{{ $card->id }}">
                <i class="fi fi-{{ $icon }} text-white"></i>
            </div>

            <x-tooltip id="approval-tooltip-{{ $card->id }}" content="{{ __('board.messages.' . $translationKey) }}" />
        @endif
        @if ($card->tasks()->count() > 0)
            @php
                $tasksCount = $card->tasks()->count();
                $doingTasksCount = $card->tasks()->where('status', 'doing')->count();
                $completedTasksCount = $card->tasks()->where('status', 'done')->count();

                $bgColor = $doingTasksCount > 0 && $completedTasksCount < $tasksCount
                    ? 'bg-blue-400'
                    : ($completedTasksCount === $tasksCount
                        ? 'bg-green-400'
                        : 'bg-purple-400');

                $icon = $doingTasksCount > 0 && $completedTasksCount < $tasksCount
                    ? 'rr-time-fast'
                    : ($completedTasksCount === $tasksCount
                        ? 'rr-check'
                        : 'rr-task-checklist');

                $translationKey = $doingTasksCount > 0 && $completedTasksCount < $tasksCount
                    ? 'tasks_in_progress'
                    : ($completedTasksCount === $tasksCount
                        ? 'tasks_completed'
                        : 'no_tasks_started');
            @endphp

            <div class="flex gap-x-2 {{ $bgColor }} rounded-md text-sm cursor-help px-2 py-0.5" data-tooltip-target="tasks-tooltip-{{ $card->id }}">
                <i class="fi fi-{{ $icon }} text-white"></i>
                <p class="text-white text-sm">{{ $completedTasksCount }} / {{ $tasksCount }}</p>
            </div>

            <x-tooltip id="tasks-tooltip-{{ $card->id }}" content="{{ __('board.messages.' . $translationKey) }}" />
        @endif
        @if ($card->description)
            <div class="flex items-center bg-gray-200 dark:bg-gray-900 rounded-md text-sm cursor-help px-2 py-0.5" data-tooltip-target="description-tooltip-{{ $card->id }}">
                <i class="fi fi-rr-poll-h text-black dark:text-white"></i>
            </div>

            <x-tooltip id="description-tooltip-{{ $card->id }}" content="{{ __('board.messages.has_description') }}" />
        @endif
    </div>

    {{-- Assignees --}}
    @php
        $assignees = $card->assignees;
        $maxVisible = 3;

        $visibleAssignees = $assignees->take($maxVisible);
        $remainingCount = $assignees->count() - $maxVisible;
    @endphp

    <div class="flex justify-end">
        <div x-data="{ open: false }" class="relative flex items-center gap-x-2 bg-gray-100 dark:bg-gray-900 rounded-lg px-2.5 py-1.5">
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
                        class="w-full text-xs bg-red-500 hover:bg-red-600 text-white rounded px-2 py-1"
                        wire:click="clearAssignees"
                    >
                        Clear all
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
</div>
