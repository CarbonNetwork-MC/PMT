<div>
    {{-- Page Title --}}
    @section('title', __('titles.admin.roles_and_permissions.overview'))

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
                'url' => route('admin.roles-and-permissions.render'),
                'label' => __('admin.titles.roles_and_permissions'),
            ]
        ]" />
    </x-slot>

    {{-- Roles --}}
    <x-containers.main>
        <div class="flex justify-between">
            <h1 class="font-bold text-lg">
                {{ __('admin.titles.roles') }}
            </h1>
            <x-buttons.primary-button href="{{ route('admin.roles-and-permissions.create-role.render') }}" size="sm">
                {{ __('admin.buttons.new_role') }}
            </x-buttons.primary-button>
        </div>

        <x-tables.table-striped class="mt-4">
            <x-slot name="headers">
                <x-tables.table-header>{{ __('admin.labels.name') }}</x-tables.table-header>
                <x-tables.table-header>{{ __('admin.labels.permissions') }}</x-tables.table-header>
                <x-tables.table-header></x-tables.table-header>
            </x-slot>
            <x-slot name="rows">
                @forelse ($roles as $role)
                    <x-tables.table-row>
                        <x-tables.table-data>{{ $role->name }}</x-tables.table-data>
                        <x-tables.table-data>
                            @forelse ($role->permissions as $permission)
                                <span class="inline-flex items-center rounded-full border border-blue-200 bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700">
                                    {{ $permission->display_name ?? $permission->name }}
                                </span>
                            @empty
                                @if ($role->name === 'Superadmin')
                                    <span class="rounded-full border border-red-200 bg-red-50 px-3 py-1 text-xs font-medium text-red-700">
                                        {{ __('admin.labels.all_permissions') }}
                                    </span>
                                @else
                                    <span class="text-gray-400">No permissions</span>
                                @endif
                            @endforelse
                        </x-tables.table-data>
                        <x-tables.table-actions>
                            <x-tables.primary-action href="{{ route('admin.roles-and-permissions.edit-role.render', $role->uuid) }}">
                                {{ __('general.buttons.edit') }}
                            </x-tables.primary-action>
                            <x-tables.danger-action wire:click="deleteRole('{{ $role->uuid }}')">
                                {{ __('general.buttons.delete') }}
                            </x-tables.danger-action>
                        </x-tables.table-actions>
                    </x-tables.table-row>
                @empty
                    <x-tables.table-row>
                        <x-tables.empty-state colspan="3">
                            {{ __('admin.messages.no_roles_found') }}
                        </x-tables.empty-state>
                    </x-tables.table-row>
                @endforelse
            </x-slot>
            <x-slot name="pagination">
                @if ($roles->hasPages())
                    <div class="w-full flex items-center gap-4 mt-4">
                        {{ $roles->links() }}
                        <x-tables.per-page-select wire:model.live="rolesPerPage">
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

    {{-- Permissions --}}
    <x-containers.main class="mt-4">
        <div class="flex justify-between">
            <h1 class="font-bold text-lg">
                {{ __('admin.titles.permissions') }}
            </h1>
            <x-buttons.primary-button href="{{ route('admin.roles-and-permissions.create-permission.render') }}" size="sm">
                {{ __('admin.buttons.new_permission') }}
            </x-buttons.primary-button>
        </div>

        <x-tables.table-striped class="mt-4">
            <x-slot name="headers">
                <x-tables.table-header>{{ __('admin.labels.name') }}</x-tables.table-header>
                <x-tables.table-header>{{ __('admin.labels.display_name') }}</x-tables.table-header>
                <x-tables.table-header>{{ __('admin.labels.description') }}</x-tables.table-header>
                <x-tables.table-header></x-tables.table-header>
            </x-slot>
            <x-slot name="rows">
                @forelse ($permissions as $permission)
                    <x-tables.table-row>
                        <x-tables.table-data>
                            {{ $permission->name }}
                        </x-tables.table-data>
                        <x-tables.table-data>
                            @if ($permission->display_name)
                                {{ $permission->display_name }}
                            @else
                                <span class="italic text-gray-500">
                                    {{ __('admin.labels.no_display_name') }}
                                </span>
                            @endif
                        </x-tables.table-data>
                        <x-tables.table-data>
                            @if ($permission->description)
                                {{ $permission->description }}
                            @else
                                <span class="italic text-gray-500">
                                    {{ __('admin.labels.no_description') }}
                                </span>
                            @endif
                        </x-tables.table-data>
                        <x-tables.table-actions>
                            <x-tables.primary-action href="{{ route('admin.roles-and-permissions.edit-permission.render', $permission->uuid) }}">
                                {{ __('general.buttons.edit') }}
                            </x-tables.primary-action>
                            <x-tables.danger-action wire:click="deletePermission('{{ $permission->uuid }}')">
                                {{ __('general.buttons.delete') }}
                            </x-tables.danger-action>
                        </x-tables.table-actions>
                    </x-tables.table-row>
                @empty
                    <x-tables.table-row>
                        <x-tables.empty-state colspan="3">
                            {{ __('admin.messages.no_permissions_found') }}
                        </x-tables.empty-state>
                    </x-tables.table-row>
                @endforelse
            </x-slot>
            <x-slot name="pagination">
                @if ($permissions->hasPages())
                    <div class="w-full flex items-center gap-4 mt-4">
                        {{ $permissions->links() }}
                        <x-tables.per-page-select wire:model.live="permissionsPerPage">
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

    {{-- Delete Role Modal --}}


    {{-- Delete Permission Modal --}}
</div>
