<div>
    {{-- Page Title --}}
    @section('title', __('titles.admin.users.overview'))

    {{-- Breadcrumbs --}}
    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[
            [
                'icon' => 'fi fi-rs-house-chimney',
                'url' => route('admin.dashboard.render'),
                'label' => '',
            ],
            [
                'icon' => '',
                'url' => route('admin.users.render'),
                'label' => __('admin.titles.users.users'),
            ]
        ]" />
    </x-slot>

    <x-containers.main>
        <div class="flex justify-between items-center">
            <h1 class="font-bold text-lg dark:text-white">
                {{ __('admin.titles.users.users') }}
            </h1>
            <div class="flex gap-2">
                <x-forms.text-input
                    wire:model.live="search"
                    placeholder="{{ __('general.placeholders.search') }}"
                />
            </div>
        </div>

        <x-tables.table-striped class="mt-4">
            <x-slot name="headers">
                <x-tables.table-header>{{ __('admin.labels.name') }}</x-tables.table-header>
                <x-tables.table-header>{{ __('admin.labels.users.email') }}</x-tables.table-header>
                <x-tables.table-header>{{ __('admin.labels.permissions') }}</x-tables.table-header>
                <x-tables.table-header></x-tables.table-header>
            </x-slot>
            <x-slot name="rows">
                @forelse ($users as $user)
                    <x-tables.table-row>
                        <x-tables.table-data>{{ $user->name }}</x-tables.table-data>
                        <x-tables.table-data>{{ $user->email }}</x-tables.table-data>
                        <x-tables.table-data>
                            @if ($user->hasRole('Superadmin'))
                                <span class="rounded-full border border-red-200 bg-red-50 px-3 py-1 text-xs font-medium text-red-700">
                                    {{ __('admin.labels.users.superadmin') }}
                                </span>
                            @else
                                @if ($user->getPermissionsViaRoles()->isNotEmpty() || $user->getDirectPermissions()->isNotEmpty())
                                    @foreach ($user->getPermissionsViaRoles()->unique('uuid') as $permission)
                                        <span class="inline-flex items-center rounded-full border border-green-200 bg-green-50 px-2.5 py-1 text-xs font-semibold text-green-700">
                                            {{ $permission->display_name ?? $permission->name }}
                                        </span>
                                    @endforeach

                                    @foreach ($user->getDirectPermissions() as $permission)
                                        <span class="inline-flex items-center rounded-full border border-blue-200 bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700">
                                            {{ $permission->display_name ?? $permission->name }}
                                        </span>
                                    @endforeach
                                @else
                                    <span class="text-gray-400">No permissions</span>
                                @endif
                            @endif
                        </x-tables.table-data>
                        <x-tables.table-actions>
                            <x-tables.primary-action href="{{ route('admin.users.edit.render', ['uuid' => $user->uuid]) }}">
                                {{ __('general.buttons.edit') }}
                            </x-tables.primary-action>
                            <x-tables.danger-action wire:click="deleteUser('{{ $user->uuid }}')">
                                {{ __('general.buttons.delete') }}
                            </x-tables.danger-action>
                        </x-tables.table-actions>
                    </x-tables.table-row>
                @empty
                    <x-tables.table-row>
                        <x-tables.empty-state colspan="4">
                            {{ __('admin.messages.users.no_users') }}
                        </x-tables.empty-state>
                    </x-tables.table-row>
                @endforelse
            </x-slot>
            <x-slot name="pagination">
                @if ($users->hasPages())
                    <div class="w-full flex justify-end items-center gap-4 mt-4">
                        {{ $users->links() }}
                        <x-tables.per-page-select wire:model.live="perPage">
                            <option value="5">5</option>
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </x-tables.per-page-select>
                    </div>
                @endif
            </x-slot>
        </x-tables.table-striped>
    </x-containers.main>

    {{-- Delete User Modal --}}
    <x-modals.modal wire:model="showDeleteUserModal">
        <x-slot name="title">
            <p class="text-center">
                {{ __('admin.titles.users.delete') }}
            </p>
        </x-slot>

        <x-slot name="content">
            <div class="space-y-4">
                <p class="text-center text-gray-700 dark:text-gray-300">
                    {!! __('admin.messages.users.delete_user_confirmation', [
                        'user' => $userToDelete?->name ?? 'Unknown User',
                    ]) !!}
                </p>

                {{-- Progress Indicator --}}
                @php
                    $completedProjectActions = collect($projectsRequiringAction)
                        ->filter(function ($project) use ($projectActions, $projectTransferUsers) {
                            $projectUuid = $project['uuid'];
                            $action = $projectActions[$projectUuid] ?? '';

                            if (!in_array(
                                $action,
                                ['transfer_user', 'transfer_me', 'archive', 'delete'],
                                true
                            )) {
                                return false;
                            }

                            if ($action === 'transfer_user') {
                                return !empty($projectTransferUsers[$projectUuid] ?? null);
                            }

                            return true;
                        })
                        ->count();
                @endphp

                @if (count($projectsRequiringAction) > 0)
                    <div
                        x-data="{
                            collapsedProjects: [],
                            projectIds: @js(collect($projectsRequiringAction)->pluck('uuid')->values()),

                            isCollapsed(projectUuid) {
                                return this.collapsedProjects.includes(projectUuid);
                            },

                            toggleProject(projectUuid) {
                                if (this.isCollapsed(projectUuid)) {
                                    this.collapsedProjects = this.collapsedProjects.filter(
                                        uuid => uuid !== projectUuid
                                    );
                                } else {
                                    this.collapsedProjects.push(projectUuid);
                                }
                            },

                            collapseAll() {
                                this.collapsedProjects = [...this.projectIds];
                            },

                            expandAll() {
                                this.collapsedProjects = [];
                            },

                            allCollapsed() {
                                return this.collapsedProjects.length === this.projectIds.length;
                            }
                        }"
                        class="space-y-3"
                    >
                        <div class="rounded-lg border border-yellow-300 bg-yellow-50 p-4 dark:border-yellow-700 dark:bg-yellow-950/30">
                            <p class="font-medium text-yellow-900 dark:text-yellow-200">
                                {{ __('admin.messages.users.projects_require_action') }}
                            </p>

                            <p class="mt-1 text-sm text-yellow-800 dark:text-yellow-300">
                                {{ __('admin.messages.users.projects_require_action_description') }}
                            </p>
                        </div>

                        <div class="flex items-center justify-between gap-3">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ $completedProjectActions }}
                                /
                                {{ count($projectsRequiringAction) }}
                                {{ __('admin.labels.users.projects_completed') }}
                            </span>

                            <button
                                type="button"
                                x-on:click="allCollapsed() ? expandAll() : collapseAll()"
                                class="text-sm font-medium text-blue-600 hover:underline dark:text-blue-400 cursor-pointer"
                            >
                                <span x-show="!allCollapsed()">
                                    {{ __('general.buttons.collapse_all') }}
                                </span>

                                <span x-show="allCollapsed()" x-cloak>
                                    {{ __('general.buttons.expand_all') }}
                                </span>
                            </button>
                        </div>

                        <div
                            class="max-h-[28rem] space-y-3 overflow-y-auto rounded-lg
                                border border-gray-200 bg-gray-50 p-3
                                dark:border-gray-700 dark:bg-gray-900/40"
                        >
                            @foreach ($projectsRequiringAction as $project)
                                @php
                                    $projectUuid = $project['uuid'];
                                    $selectedAction = $projectActions[$projectUuid] ?? '';

                                    $hasValidAction = in_array(
                                        $selectedAction,
                                        ['transfer_user', 'transfer_me', 'archive', 'delete'],
                                        true
                                    );

                                    $actionIsComplete = $hasValidAction
                                        && (
                                            $selectedAction !== 'transfer_user'
                                            || !empty($projectTransferUsers[$projectUuid] ?? null)
                                        );
                                @endphp

                                <div
                                    wire:key="delete-user-project-{{ $projectUuid }}"
                                    @class([
                                        'rounded-lg border-2 bg-white transition-colors duration-200 dark:bg-gray-800',
                                        'border-green-400 dark:border-green-600' => $actionIsComplete,
                                        'border-red-300 dark:border-gray-600' => !$actionIsComplete,
                                    ])
                                >
                                    {{-- Always-visible project header --}}
                                    <button
                                        type="button"
                                        x-on:click="toggleProject('{{ $projectUuid }}')"
                                        class="flex w-full items-center justify-between gap-3 p-4 text-left"
                                        :aria-expanded="!isCollapsed('{{ $projectUuid }}')"
                                    >
                                        <div class="flex min-w-0 items-center gap-3">
                                            @if ($actionIsComplete)
                                                <span
                                                    class="inline-flex h-6 w-6 shrink-0 items-center
                                                        justify-center rounded-full bg-green-100
                                                        text-green-700 dark:bg-green-900/40
                                                        dark:text-green-400"
                                                >
                                                    ✓
                                                </span>
                                            @else
                                                <span
                                                    class="inline-flex h-6 w-6 shrink-0 items-center
                                                        justify-center rounded-full bg-gray-100
                                                        text-gray-500 dark:bg-gray-700
                                                        dark:text-gray-300"
                                                >
                                                    !
                                                </span>
                                            @endif

                                            <div class="min-w-0">
                                                <p class="truncate font-semibold text-gray-900 dark:text-white">
                                                    {{ $project['name'] }}
                                                </p>

                                                <p
                                                    @class([
                                                        'mt-0.5 text-xs font-medium',
                                                        'text-green-700 dark:text-green-400' => $actionIsComplete,
                                                        'text-gray-500 dark:text-gray-400' => !$actionIsComplete,
                                                    ])
                                                >
                                                    @if ($actionIsComplete)
                                                        {{ __('admin.messages.users.project_action_selected') }}
                                                    @else
                                                        {{ __('admin.messages.users.project_action_not_selected') }}
                                                    @endif
                                                </p>
                                            </div>
                                        </div>

                                        <svg
                                            class="h-5 w-5 shrink-0 text-gray-500 transition-transform duration-200 cursor-pointer"
                                            :class="{ 'rotate-180': !isCollapsed('{{ $projectUuid }}') }"
                                            xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20"
                                            fill="currentColor"
                                        >
                                            <path
                                                fill-rule="evenodd"
                                                d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                                                clip-rule="evenodd"
                                            />
                                        </svg>
                                    </button>

                                    {{-- Collapsible project controls --}}
                                    <div
                                        x-show="!isCollapsed('{{ $projectUuid }}')"
                                        x-collapse
                                    >
                                        <div class="border-t border-gray-200 p-4 dark:border-gray-700">
                                            <div>
                                                <label
                                                    for="project-action-{{ $projectUuid }}"
                                                    class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300"
                                                >
                                                    {{ __('admin.labels.users.project_action') }}
                                                </label>

                                                <select
                                                    id="project-action-{{ $projectUuid }}"
                                                    wire:model.live="projectActions.{{ $projectUuid }}"
                                                    class="block w-full rounded-lg border-gray-300
                                                        bg-white text-gray-900
                                                        focus:border-blue-500 focus:ring-blue-500
                                                        dark:border-gray-600 dark:bg-gray-900
                                                        dark:text-white"
                                                >
                                                    <option value="">
                                                        {{ __('admin.placeholders.users.select_project_action') }}
                                                    </option>

                                                    <option value="transfer_user">
                                                        {{ __('admin.options.users.transfer_to_user') }}
                                                    </option>

                                                    <option value="transfer_me">
                                                        {{ __('admin.options.users.transfer_to_me') }}
                                                    </option>

                                                    <option value="archive">
                                                        {{ __('admin.options.users.archive_project') }}
                                                    </option>

                                                    <option value="delete">
                                                        {{ __('admin.options.users.delete_project') }}
                                                    </option>
                                                </select>

                                                @error("projectActions.$projectUuid")
                                                    <p class="mt-1 text-sm text-red-600">
                                                        {{ $message }}
                                                    </p>
                                                @enderror
                                            </div>

                                            @if ($selectedAction === 'transfer_user')
                                                <div class="mt-3">
                                                    <label
                                                        for="project-owner-{{ $projectUuid }}"
                                                        class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300"
                                                    >
                                                        {{ __('admin.labels.users.new_project_owner') }}
                                                    </label>

                                                    <select
                                                        id="project-owner-{{ $projectUuid }}"
                                                        wire:model.live="projectTransferUsers.{{ $projectUuid }}"
                                                        class="block w-full rounded-lg border-gray-300
                                                            bg-white text-gray-900
                                                            focus:border-blue-500 focus:ring-blue-500
                                                            dark:border-gray-600 dark:bg-gray-900
                                                            dark:text-white"
                                                    >
                                                        <option value="">
                                                            {{ __('admin.placeholders.users.select_new_owner') }}
                                                        </option>

                                                        @foreach ($eligibleTransferUsers as $user)
                                                            <option value="{{ $user['uuid'] }}">
                                                                {{ $user['name'] }} ({{ $user['email'] }})
                                                            </option>
                                                        @endforeach
                                                    </select>

                                                    @error("projectTransferUsers.$projectUuid")
                                                        <p class="mt-1 text-sm text-red-600">
                                                            {{ $message }}
                                                        </p>
                                                    @enderror
                                                </div>
                                            @endif

                                            @if ($selectedAction === 'transfer_me')
                                                <div class="mt-3 rounded-lg bg-blue-50 p-3 dark:bg-blue-950/30">
                                                    <p class="text-sm text-blue-700 dark:text-blue-300">
                                                        {{ __('admin.messages.users.project_will_be_transferred_to_you') }}
                                                    </p>
                                                </div>
                                            @endif

                                            @if ($selectedAction === 'archive')
                                                <div class="mt-3 rounded-lg bg-yellow-50 p-3 dark:bg-yellow-950/30">
                                                    <p class="text-sm text-yellow-700 dark:text-yellow-300">
                                                        {{ __('admin.messages.users.project_will_be_archived') }}
                                                    </p>
                                                </div>
                                            @endif

                                            @if ($selectedAction === 'delete')
                                                <div class="mt-3 rounded-lg bg-red-50 p-3 dark:bg-red-950/30">
                                                    <p class="text-sm font-medium text-red-800 dark:text-red-200">
                                                        {{ __('admin.messages.users.project_will_be_deleted') }}
                                                    </p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($errorMessage)
                    <div class="rounded-lg bg-yellow-100 p-4">
                        <p class="text-yellow-800">
                            {!! $errorMessage !!}
                        </p>
                    </div>
                @endif
            </div>
        </x-slot>

        <x-slot name="footer">
            <div class="flex justify-end gap-2">
                <x-buttons.secondary-button wire:click="closeDeleteUserModal">
                    {{ __('general.buttons.cancel') }}
                </x-buttons.secondary-button>

                <x-buttons.danger-button
                    wire:click="confirmDeleteUser"
                    wire:loading.attr="disabled"
                    wire:target="confirmDeleteUser"
                    :disabled="!$this->allProjectsConfigured"
                    @class([
                        'opacity-50 cursor-not-allowed' => !$this->allProjectsConfigured,
                    ])
                >
                    <span wire:loading.remove wire:target="confirmDeleteUser">
                        {{ __('general.buttons.delete') }}
                    </span>

                    <span wire:loading wire:target="confirmDeleteUser">
                        {{ __('general.labels.processing') }}
                    </span>
                </x-buttons.danger-button>
            </div>
        </x-slot>
    </x-modals.modal>
</div>
