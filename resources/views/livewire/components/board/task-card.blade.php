<div 
    wire:key="column" 
    class="bg-white dark:bg-gray-700 p-2 mb-2 rounded-md {{ $task->card->sprint->status === 'active' ? 'cursor-grabbing' : '' }}"
    @if ($task->card->sprint->status === 'active')
        wire:sortable-group.handle
    @endif
>
    {{-- Top Bar - ID & Actions Menu --}}
    <div class="flex items-center justify-between mb-2">
        <p class="text-xs text-gray-500 dark:text-gray-400 font-mono">#{{ $task->id }}</p>

        {{-- Actions Dropdown --}}
        <x-dropdown.wrapper
            state="open"
            :useOwnState="true"
            icon="bs-menu-dots-vertical"
            tooltipId="stask-actions-{{ $task->id }}"
            tooltip="{{ __('board.labels.task_actions') }}"
            width="w-52"
            align="right"
            margin="mt-4"
        >
            <div class="text-sm text-gray-900">
                <p class="text-center font-bold">
                    {{ __('board.labels.actions') }} - Task #{{ $task->id }}
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

            <x-dropdown.dropdown-button
                icon="rr-copy"
                :label="__('board.buttons.make_a_copy')"
                wireClick="makeACopy"
                alpineClick="open = false"
            />

            <x-dropdown.dropdown-button
                icon="rr-convert-shapes"
                :label="__('board.buttons.convert_to_card')"
                wireClick="convertToCard"
                alpineClick="open = false"
            />

            @if($isProjectAdminOrOwner)
                <x-containers.divider margin="my-2" color="gray-400" />

                <x-dropdown.dropdown-button
                    icon="rr-trash"
                    :label="__('board.buttons.delete_task')"
                    color="red-500"
                    wireClick="deleteTask"
                    alpineClick="open = false"
                />
            @endif
        </x-dropdown.wrapper>
    </div>

    {{-- Description --}}
    <p class="text-sm text-gray-700 dark:text-gray-300 mb-2">{{ $task->description }}</p>

    {{-- Bottom Bar - Time Management & Users --}}
    <div class="flex items-center justify-end">
        <div class="flex items-center gap-1">
            {{-- Actual Time --}}
            <div x-data="{ editing: false }" wire:key="actual-time-{{ $task->id }}">
                <div 
                    @click="editing = true" 
                    class="flex items-center gap-2 bg-gray-200 dark:bg-gray-900 rounded-md px-2.5 py-1.5 cursor-pointer"
                    data-tooltip-target="actual-time-{{ $task->id }}"
                >
                    <i class="fi fi-sr-hourglass text-xs text-gray-700 dark:text-white"></i>
                    <span class="text-xs text-gray-700 dark:text-white">
                        {{ $task->actual_time ? \App\Helpers\TimeFormatter::minutesToHuman($task->actual_time) : '-' }}
                    </span>

                    <x-tooltip id="actual-time-{{ $task->id }}" content="{{ __('board.labels.actual_time') }}" />
                </div>

                {{-- Edit Actual Time --}}
                <div 
                    x-show="editing" 
                    @click.outside="editing = false" 
                    class="absolute mt-1 z-10 bg-gray-200 dark:bg-gray-900 rounded-lg p-2 shadow-lg"
                >
                    <input 
                        type="text"
                        class="w-full text-sm px-2 py-1 rounded border border-gray-300 focus:outline-none"
                        wire:model.live="actualTimeInput"
                        wire:blur="updateActualTime"
                        @keydown.enter.prevent="$el.blur(); editing = false"
                    />
                </div>
            </div>

            {{-- Estimated Time --}}
            <div x-data="{ editing: false }" wire:key="estimated-time-{{ $task->id }}">
                <div 
                    @click="editing = true" 
                    class="flex items-center gap-2 bg-gray-200 dark:bg-gray-900 rounded-md px-2.5 py-1.5 cursor-pointer"
                    data-tooltip-target="estimated-time-{{ $task->id }}"
                >
                    <i class="fi fi-sr-clock text-xs text-gray-700 dark:text-white"></i>
                    <span class="text-xs text-gray-700 dark:text-white">
                        {{ $task->estimated_time ? \App\Helpers\TimeFormatter::minutesToHuman($task->estimated_time) : '-' }}
                    </span>

                    <x-tooltip id="estimated-time-{{ $task->id }}" content="{{ __('board.labels.estimated_time') }}" />
                </div>

                {{-- Edit Estimated Time --}}
                <div 
                    x-show="editing" 
                    @click.outside="editing = false" 
                    class="absolute mt-1 z-10 bg-gray-200 dark:bg-gray-900 rounded-lg p-2 shadow-lg"
                >
                    <input 
                        type="text" 
                        class="w-full text-sm px-2 py-1 rounded border border-gray-300 focus:outline-none"
                        wire:model.live="estimatedTimeInput"
                        wire:blur="updateEstimatedTime"
                        @keydown.enter.prevent="$el.blur(); editing = false"
                    />
                </div>
            </div>

            {{-- Deadline --}}
            <div x-data="{ editing: false }" wire:key="deadline-{{ $task->id }}">
                <div 
                    @click="editing = true" 
                    class="flex items-center gap-2 bg-gray-200 dark:bg-gray-900 rounded-md px-2.5 py-1.5 cursor-pointer"
                    data-tooltip-target="deadline-{{ $task->id }}"
                >
                    <i class="fi fi-sr-calendar text-xs text-gray-700 dark:text-white"></i>
                    <span class="text-xs text-gray-700 dark:text-white">
                        {{ $task->deadline ? \Carbon\Carbon::parse($task->deadline)->format('M d, H:i') : '-' }}
                    </span>

                    <x-tooltip id="deadline-{{ $task->id }}" content="{{ __('board.labels.deadline') }}" />
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
                        wire:keydown.enter.prevent="updateDeadline"
                        wire:blur="updateDeadline"
                        @keydown.enter="editing = false"
                    />
                </div>
            </div>

            {{-- Assigned Users --}}
            @php
                $assignees = $task->assignees;
                $maxVisible = 3;

                $visibleAssignees = $assignees->take($maxVisible);
                $remainingCount = $assignees->count() - $maxVisible;
            @endphp

            <div class="flex justify-end">
                <div x-data="{ open: false }" class="relative flex items-center gap-x-2 bg-gray-200 dark:bg-gray-900 rounded-md px-2.5">
                    <i 
                        @click="open = !open"
                        class="fi fi-sr-users text-xs text-gray-700 dark:text-white cursor-pointer"
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
                        <ul wire:key="users-{{ $task->id }}-{{ $task->assignees->count() }}" class="h-48 p-2 text-sm overflow-y-auto" id="user-list-{{ $task->id }}">
                            @foreach ($filteredUsers as $user)
                                @php
                                    $isAssigned = $task->assignees->contains('user_uuid', $user->uuid);
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
                        <p class="text-xs text-gray-700 dark:text-white py-1.5">
                            {{ __('board.messages.no_users_assigned') }}
                        </p>
                    @else
                        <div class="flex -space-x-2 py-1">
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
    </div>
</div>
