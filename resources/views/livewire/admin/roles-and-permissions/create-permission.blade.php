<div>
    {{-- Page Title --}}
    @section('title', __('titles.admin.roles_and_permissions.create_permission'))

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
                'url' => route('admin.roles-and-permissions.create-permission.render'),
                'label' => __('admin.titles.create_permission'),
            ]
        ]" />
    </x-slot>

    <x-containers.main>
        <h1 class="font-bold text-lg">
            {{ __('admin.titles.create_permission') }}
        </h1>

        <div class="mt-4 space-y-2">
            <div class="w-full lg:w-1/2 xl:w-1/4">
                <x-forms.text-input
                    wire:model.live="permissionName"
                    label="{{ __('admin.labels.name') }}"
                    required
                />
            </div>

            <div class="w-full lg:w-1/2">
                <x-forms.text-area
                    wire:model.live="permissionDescription"
                    label="{{ __('admin.labels.description') }}"
                />
            </div>
        </div>

        <div class="flex justify-end items-center gap-4 mt-4">
            <x-forms.required-fields />
            <x-buttons.primary-button wire:click="createPermission" size="sm">
                {{ __('admin.buttons.create_permission') }}
            </x-buttons.primary-button>
        </div>
    </x-containers.main>
</div>
