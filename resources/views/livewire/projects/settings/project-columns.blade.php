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
                'url' => route('projects.settings.columns.render', ['uuid' => $project->uuid]),
                'label' => __('settings.titles.columns'),
            ]
        ]" />
    </x-slot>

    <div class="flex justify-center">
        <x-containers.main class="w-2/3">
            <x-navigation.tabs 
                :active="'columns'"
                :tabs="[
                    [
                        'key' => 'general', 
                        'label' => __('settings.nav.general'), 
                        'href' => route('projects.settings.general.render', ['uuid' => $project->uuid])
                    ],
                    [
                        'key' => 'members', 
                        'label' => __('settings.nav.members'), 
                        'href' => route('projects.settings.members.render', ['uuid' => $project->uuid])
                    ],
                    [
                        'key' => 'columns',
                        'label' => __('settings.nav.columns'),
                        'href' => route('projects.settings.columns.render', ['uuid' => $project->uuid]),
                        'disabled' => !$isProjectAdmin && !$isProjectOwner
                    ],
                    [
                        'key' => 'admin',
                        'label' => __('settings.nav.admin'),
                        'href' => route('projects.settings.admin.render', ['uuid' => $project->uuid]),
                        'disabled' => !$isProjectOwner
                    ],
                ]"
            />

            <div class="mt-8 mx-4">
                columns
            </div>
        </x-containers.main>
    </div>
</div>
