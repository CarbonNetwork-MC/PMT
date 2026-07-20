<div>
    {{-- Page Title --}}
    @section('title', __('titles.projects.settings.admin') . ' | ' . $project->name)

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
                'url' => route('projects.settings.admin.render', ['uuid' => $project->uuid]),
                'label' => __('settings.titles.admin'),
            ]
        ]" />
    </x-slot>

    <div class="flex justify-center">
        <x-containers.main class="w-2/3">
            <x-navigation.tabs 
                :active="'admin'"
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
                    ],
                    [
                        'key' => 'admin',
                        'label' => __('settings.nav.admin'),
                        'href' => route('projects.settings.admin.render', ['uuid' => $project->uuid])
                    ],
                ]"
            />

            <div class="mt-8 mx-4">
                <x-project.settings-card 
                    title="{{ __('settings.titles.change_owner') }}"
                    description="{!! __('settings.descriptions.change_owner') !!}"
                >
                    <x-slot name="content">
                        <div class="flex justify-end">
                            <x-buttons.danger-button wire:click="$set('showChangeOwnerModal', true)">
                                {{ __('settings.buttons.change_owner') }}
                            </x-buttons.danger-button>
                        </div>
                    </x-slot>
                </x-project.settings-card>

                <x-containers.divider height="0.5" />

                <x-project.settings-card 
                    title="{{ __('settings.titles.delete_project') }}"
                    description="{{ __('settings.descriptions.delete_project') }}"
                >
                    <x-slot name="content">
                        <div class="flex justify-end">
                            <x-buttons.danger-button wire:click="$set('showDeleteProjectModal', true)">
                                {{ __('settings.buttons.delete_project') }}
                            </x-buttons.danger-button>
                        </div>
                    </x-slot>
                </x-project.settings-card>
            </div>
        </x-containers.main>
    </div>

    {{-- Transfer Ownership Modal --}}
    <x-modals.modal size="lg" wire:model="showChangeOwnerModal">
        <x-slot name="title">
            <p class="text-center">
                {{ __('settings.titles.change_owner') }}
            </p>
        </x-slot>
        <x-slot name="content">
            <p class="text-gray-600 dark:text-gray-300 text-center">
                {{ __('settings.messages.change_owner_warning') }}
            </p>
            <p class="text-red-500 text-center">
                {!! __('settings.messages.action_cannot_be_undone') !!}
            </p>

            {{-- Select new owner --}}
            <div class="flex justify-center mt-4">
                <div class="w-[50%]">
                    <x-forms.select 
                        wire:model="newOwnerId"
                        label="{{ __('settings.labels.new_owner') }}"
                        placeholder="{{ __('settings.placeholders.select_new_owner') }}"
                        :options="$projectMembers->map(function($member) {
                            return [
                                'value' => $member->user_uuid,
                                'label' => $member->user->name . ' (' . $member->role->name . ')'
                            ];
                        })"
                        required
                    />
                </div>
            </div>
        </x-slot>
        <x-slot name="footer">
            <div class="flex justify-end space-x-2">
                <x-buttons.secondary-button wire:click="$set('showChangeOwnerModal', false)">
                    {{ __('general.buttons.cancel') }}
                </x-buttons.secondary-button>
                <x-buttons.danger-button wire:click="confirmChangeOwner" :disabled="!$newOwnerId">
                    {{ __('general.buttons.confirm') }}
                </x-buttons.danger-button>
            </div>
        </x-slot>
    </x-modals.modal>

    {{-- Delete Project Modal --}}
    <x-modals.modal size="lg" wire:model="showDeleteProjectModal">
        <x-slot name="title">
            <p class="text-center">
                {{ __('settings.titles.delete_project') }}
            </p>
        </x-slot>
        <x-slot name="content">
            <p class="text-gray-600 dark:text-gray-300 text-center">
                {{ __('settings.messages.delete_project_warning') }}
            </p>
            <p class="text-red-500 text-center">
                {!! __('settings.messages.action_cannot_be_undone') !!}
            </p>
        </x-slot>
        <x-slot name="footer">
            <div class="flex justify-end space-x-2">
                <x-buttons.secondary-button wire:click="$set('showDeleteProjectModal', false)">
                    {{ __('general.buttons.cancel') }}
                </x-buttons.secondary-button>
                <x-buttons.danger-button wire:click="confirmDeleteProject">
                    {{ __('general.buttons.confirm') }}
                </x-buttons.danger-button>
            </div>
        </x-slot>
    </x-modals.modal>
</div>
