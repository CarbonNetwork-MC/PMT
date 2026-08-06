<div>
    {{-- Page Title --}}
    @section('title', __('titles.admin.roles_and_permissions.create_role'))

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
            ],
            [
                'icon' => '',
                'url' => route('admin.roles-and-permissions.create-role.render'),
                'label' => __('admin.titles.create_role'),
            ]
        ]" />
    </x-slot>
    
    <x-containers.main>
        <h1 class="font-bold text-lg dark:text-white">
            {{ __('admin.titles.create_role') }}
        </h1>

        <div class="mt-4">
            <div class="w-full lg:w-1/2 xl:w-1/4">
                <x-forms.text-input
                    wire:model.live="roleName"
                    label="{{ __('admin.labels.name') }}"
                    required
                />
            </div>
        </div>

        <h1 class="font-bold mt-6 dark:text-white">
            {{ __('admin.titles.permissions') }}
        </h1>

        <div class="bg-gray-100 dark:bg-gray-900 rounded-lg p-8 mt-2">
            @if ($roleName === 'Superadmin')
                <div class="flex items-center gap-2 rounded-lg border border-red-200 bg-red-50 p-4">
                    <i class="fi fi-rr-diamond-exclamation text-red-700"></i>
                    <p class="font-medium text-red-700">
                        {!! __('admin.messages.superadmin_permissions') !!}
                    </p>
                </div>
            @else
                <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 lg:gap-4">
                    @forelse ($allPermissions as $permission)
                        <label
                            for="permission_{{ $permission->uuid }}"
                            class="flex items-center gap-3 rounded-lg border border-gray-300 
                            p-3 cursor-pointer transition hover:border-blue-500 hover:bg-blue-50 dark:hover:bg-gray-800
                            has-checked:border-blue-600 has-checked:bg-blue-100 dark:has-checked:bg-gray-700"
                        >
                            <input
                                type="checkbox"
                                id="permission_{{ $permission->uuid }}"
                                wire:model="permissions"
                                value="{{ $permission->uuid }}"
                                class="h-5 w-5 rounded text-blue-600 focus:ring-blue-500"
                            >

                            <div>
                                <p class="font-medium dark:text-white">{{ $permission->display_name ?? $permission->name }}</p>
                                <p class="text-sm text-gray-500">
                                    {{ $permission->description ?? __('admin.labels.no_description') }}
                                </p>
                            </div>
                        </label>
                    @empty
                        <div>
                            {{ __('admin.messages.no_permissions_found') }}
                        </div>
                    @endforelse
                </div>
            @endif
        </div>

        <div class="flex justify-end items-center gap-4 mt-4">
            <x-forms.required-fields />
            <x-buttons.primary-button wire:click="createRole" size="sm">
                {{ __('admin.buttons.create_role') }}
            </x-buttons.primary-button>
        </div>
    </x-containers.main>
</div>
