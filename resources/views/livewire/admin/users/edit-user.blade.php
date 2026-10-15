<div>
    {{-- Page Title --}}
    @section('title', __('titles.admin.users.edit'))

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
            ],
            [
                'icon' => '',
                'url' => route('admin.users.edit.render', ['uuid' => $user->uuid]),
                'label' => $user->name,
            ]
        ]" />
    </x-slot>

    <x-containers.main>
        <h1 class="font-bold text-lg dark:text-white">
            {{ __('admin.titles.users.edit') }}
        </h1>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mt-4">
            <x-forms.text-input
                wire:model.live="username"
                label="{{ __('admin.labels.name') }}"
                placeholder="{{ __('admin.placeholders.users.username') }}"
            />
            <x-forms.text-input
                wire:model.live="email"
                label="{{ __('admin.labels.users.email') }}"
                placeholder="{{ __('admin.placeholders.users.email') }}"
            />
        </div>

        <div class="flex justify-end mt-4">
            <x-buttons.primary-button wire:click="saveUser">
                {{ __('general.buttons.save') }}
            </x-buttons.primary-button>
        </div>
    </x-containers.main>

    {{-- Permissions --}}
    <x-containers.main class="mt-4">
        <h1 class="font-semibold text-lg dark:text-white">
            {{ __('admin.titles.permissions') }}
        </h1>

        <div class="bg-gray-100 dark:bg-gray-900 rounded-lg p-6 mt-2">
            @if ($user->hasRole('Superadmin'))
                <div class="flex items-center gap-2 rounded-lg border border-red-200 bg-red-50 p-4">
                    <i class="fi fi-rr-diamond-exclamation text-red-700"></i>
                    <p class="font-medium text-red-700">
                        {!! __('admin.messages.users.user_is_superadmin') !!}
                    </p>
                </div>
            @else
                <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 lg:gap-4">
                    @forelse ($allPermissions as $permission)
                        <label
                            for="permission_{{ $permission['uuid'] }}"
                            class="flex items-center gap-3 rounded-lg border bg-white dark:bg-gray-800 border-gray-300 p-3 cursor-pointer transition 
                            hover:border-blue-500 hover:bg-blue-50 dark:hover:bg-gray-800 has-checked:border-blue-600 has-checked:bg-blue-100 dark:has-checked:bg-gray-700"
                            wire:key="permission_{{ $permission['uuid'] }}"
                        >
                            <input
                                type="checkbox"
                                id="permission_{{ $permission['uuid'] }}"
                                wire:model.live="permissions"
                                value="{{ $permission['uuid'] }}"
                            />
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">
                                    {{ $permission['display_name'] ?? $permission['name'] }}
                                </p>
                                @if (!empty($permission['description']))
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $permission['description'] }}
                                    </p>
                                @endif
                                @if ($permission['has_permission_via_role'])
                                    <p class="text-sm text-red-500">
                                        {!! __('admin.messages.users.permission_via_role', [
                                            'roles' => implode(', ', $permission['roles']),
                                        ]) !!}
                                    </p>
                                @endif
                            </div>
                        </label>
                    @empty
                        <p class="text-gray-400 dark:text-gray-500">
                            {{ __('admin.messages.no_permissions') }}
                        </p>
                    @endforelse
                </div>
            @endif
        </div>
    </x-containers.main>

    {{-- Roles --}}
    <x-containers.main class="mt-4">
        <h1 class="font-semibold text-lg dark:text-white">
            {{ __('admin.titles.roles') }}
        </h1>
        <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-4 lg:gap-4 bg-gray-100 dark:bg-gray-900 rounded-lg p-6 mt-2">
            @forelse ($allRoles as $role)
                @if ($role['name'] === 'Superadmin' && !$user->hasRole('Superadmin'))
                    @continue
                @endif
                <label
                    for="role_{{ $role['uuid'] }}"
                    class="flex items-center gap-3 rounded-lg border bg-white dark:bg-gray-800 border-gray-300 p-3 cursor-pointer 
                    transition hover:border-blue-500 hover:bg-blue-50 has-checked:border-blue-600 has-checked:bg-blue-100 dark:has-checked:bg-gray-700"
                    wire:key="role_{{ $role['uuid'] }}"
                >
                    <input
                        type="checkbox"
                        id="role_{{ $role['uuid'] }}"
                        wire:model.live="roles"
                        value="{{ $role['uuid'] }}"
                    />
                    <div>
                        <p class="font-medium text-gray-900 dark:text-white">
                            {{ $role['name'] ?? '' }}
                        </p>
                    </div>
                </label>
            @empty
                <p class="text-gray-400 dark:text-gray-500">
                    {{ __('admin.messages.no_roles') }}
                </p>
            @endforelse
        </div>
    </x-containers.main>
</div>
