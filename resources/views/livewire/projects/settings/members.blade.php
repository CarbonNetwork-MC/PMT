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
                'url' => route('projects.settings.members.render', ['uuid' => $project->uuid]),
                'label' => __('settings.titles.members'),
            ]
        ]" />
    </x-slot>

    <div class="flex justify-center">
        <x-containers.main class="w-2/3">
            <x-navigation.tabs 
                :active="'members'"
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
                        'key' => 'admin',
                        'label' => __('settings.nav.admin'),
                        'href' => route('projects.settings.admin.render', ['uuid' => $project->uuid]),
                        'disabled' => !$isProjectOwner
                    ],
                ]"
            />

            <div class="mt-8 mx-4">
                <div class="flex justify-end gap-x-4">
                    <x-forms.search-bar id="search" wire:model.live="search" class="w-full" />
                    <x-buttons.primary-button wire:click="$set('showAddMemberModal', true)" size="sm">
                        {{ __('settings.buttons.add_member') }}
                    </x-buttons.primary-button>
                </div>

                <div class="mt-6">
                    <x-tables.table-striped>
                        <x-slot name="headers">
                            <tr>
                                <x-tables.table-header>{{ __('settings.labels.member_name') }}</x-tables.table-header>
                                <x-tables.table-header>{{ __('settings.labels.member_role') }}</x-tables.table-header>
                                @if ($isProjectOwner) <th></th> @endif
                            </tr>
                        </x-slot>
                        <x-slot name="rows">
                            @forelse ($members as $member)
                                <x-tables.table-row>
                                    <x-tables.table-data>
                                        {{ $member['user'] }}
                                    </x-tables.table-data>
                                    <x-tables.table-data>
                                        {{ $member['role'] }}
                                    </x-tables.table-data>
                                    @if ($isProjectOwner && $member['is_owner'] === false)
                                        <x-tables.table-actions>
                                            <x-tables.primary-action wire:click="changeRole('{{ $member['uuid'] }}')">
                                                {{ __('settings.buttons.change_role') }}
                                            </x-tables.primary-action>
                                            <x-tables.danger-action wire:click="removeMember('{{ $member['uuid'] }}')">
                                                {{ __('settings.buttons.remove') }}
                                            </x-tables.danger-action>
                                        </x-tables.table-actions>
                                    @else
                                        <x-tables.table-data></x-tables.table-data>
                                    @endif
                                </x-tables.table-row>
                            @empty

                            @endforelse
                        </x-slot>
                        <x-slot name="pagination">

                        </x-slot>
                    </x-tables.table-striped>
                </div>
            </div>
        </x-containers.main>
    </div>

    {{-- Change Role Modal --}}
    <x-modals.modal wire:model="showChangeRoleModal">
        <x-slot name="title">
            <p class="text-center">
                {{ __('settings.titles.change_role', ['name' => $userToModify->name ?? '']) }}
            </p>
        </x-slot>
        <x-slot name="content">
            <div class="flex justify-center">
                <div class="w-[50%]">
                    <x-forms.select 
                        wire:model="newRole" 
                        :label="__('settings.labels.member_role')" 
                        :options="$roles->mapWithKeys(fn($role) => [$role->id => $role->name])" 
                    />
                </div>
            </div>
        </x-slot>
        <x-slot name="footer">
            <x-buttons.secondary-button wire:click="$set('showChangeRoleModal', false)">
                {{ __('general.buttons.cancel') }}
            </x-buttons.secondary-button>
            <x-buttons.primary-button wire:click="confirmChangeRole">
                {{ __('settings.buttons.change_role') }}
            </x-buttons.primary-button>
        </x-slot>
    </x-modals.modal>

    {{-- Add Member Modal --}}
    <x-modals.big-modal wire:model="showAddMemberModal">
        <x-slot name="title">
            <p class="text-center">
                {{ __('settings.titles.add_member') }}
            </p>
        </x-slot>
        <x-slot name="content">
            <div class="flex justify-center">
                <div class="w-[50%] grid grid-cols-2 gap-x-4">
                    <div class="col-span-1">
                        <x-forms.label for="new-member">{{ __('settings.labels.member_name') }}</x-forms.label>
                        <livewire:async-select
                            id="new-member"
                            wire:model="newMemberUuid" 
                            :options="$users->mapWithKeys(fn($user) => [$user->uuid => $user->name])" 
                        />
                    </div>
                    <div class="col-span-1">
                        <x-forms.select 
                            wire:model="newMemberRole" 
                            :label="__('settings.labels.member_role')" 
                            :options="$roles->mapWithKeys(fn($role) => [$role->id => $role->name])" 
                        />
                    </div>
                </div>
            </div>
        </x-slot>
        <x-slot name="footer">
            <x-buttons.secondary-button wire:click="$set('showAddMemberModal', false)">
                {{ __('general.buttons.cancel') }}
            </x-buttons.secondary-button>
            <x-buttons.primary-button wire:click="addMember">
                {{ __('settings.buttons.add_member') }}
            </x-buttons.primary-button>
        </x-slot>
    </x-modals.big-modal>

    {{-- Remove Member Modal --}}
    <x-modals.modal wire:model="showRemoveMemberModal">
        <x-slot name="title">
            <p class="text-center">
                {{ __('settings.titles.remove_member', ['name' => $userToModify->name ?? '']) }}
            </p>
        </x-slot>
        <x-slot name="content">
            <p class="text-center">
                {{ __('settings.messages.remove_member_warning') }}
            </p>
            <p class="text-center text-red-500">
                {!! __('settings.messages.action_cannot_be_undone') !!}
            </p>
        </x-slot>
        <x-slot name="footer">
            <x-buttons.secondary-button wire:click="$set('showRemoveMemberModal', false)">
                {{ __('general.buttons.cancel') }}
            </x-buttons.secondary-button>
            <x-buttons.danger-button wire:click="confirmRemoveMember">
                {{ __('settings.buttons.remove') }}
            </x-buttons.danger-button>
        </x-slot>
    </x-modals.modal>
</div>
