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
                'url' => route('projects.settings.general.render', ['uuid' => $project->uuid]),
                'label' => __('settings.titles.settings'),
            ],
            [
                'icon' => '',
                'url' => route('projects.settings.general.render', ['uuid' => $project->uuid]),
                'label' => __('settings.titles.general'),
            ]
        ]" />
    </x-slot>

    <div class="flex justify-center">
        <x-containers.main class="w-2/3">
            <x-navigation.tabs 
                :active="'general'"
                :tabs="[
                    [
                        'key' => 'general', 
                        'label' => 'General', 
                        'href' => route('projects.settings.general.render', ['uuid' => $project->uuid])
                    ],
                    [
                        'key' => 'members', 
                        'label' => 'Members', 
                        'href' => route('projects.settings.members.render', ['uuid' => $project->uuid])
                    ],
                    [
                        'key' => 'admin',
                        'label' => 'Admin',
                        'href' => route('projects.settings.admin.render', ['uuid' => $project->uuid]),
                        'disabled' => !$isProjectOwner
                    ],
                ]"
            />

            <div class="mt-8 mx-4">
                <x-project.settings-card 
                    title="{{ __('settings.titles.name') }}"
                    description="{{ __('settings.descriptions.name') }}"
                >
                    <x-slot name="content">
                        <x-forms.text-input wire:model="name" placeholder="{{ __('settings.placeholders.name') }}" />
                    </x-slot>
                </x-project.settings-card>

                <x-containers.divider height="0.5" />

                <x-project.settings-card 
                    title="{{ __('settings.titles.description') }}"
                    description="{{ __('settings.descriptions.description') }}"
                >
                    <x-slot name="content">
                        <x-forms.text-area wire:model="description" placeholder="{{ __('settings.placeholders.description') }}" />
                    </x-slot>
                </x-project.settings-card>

                <div class="flex justify-end items-center mt-16">
                    <x-buttons.primary-button wire:click="save">
                        {{ __('general.buttons.save') }}
                    </x-buttons.primary-button>
                </div>
            </div>
        </x-containers.main>
    </div>
</div>
