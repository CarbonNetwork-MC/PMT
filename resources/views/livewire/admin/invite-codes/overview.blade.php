<div>
    {{-- Page Title --}}
    @section('title', __('admin.titles.invite_codes.overview'))

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
                'url' => route('admin.invite-codes.render'),
                'label' => __('admin.titles.invite_codes.overview'),
            ]
        ]" />
    </x-slot>

    <x-containers.main>
        <div class="flex justify-between">
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">
                {{ __('admin.titles.invite_codes.overview') }}
            </h1>
            <x-buttons.primary-button wire:click="$set('showCreateInviteCodeModal', true)">
                {{ __('admin.buttons.invite_codes.create') }}
            </x-buttons.primary-button>
        </div>

        <x-tables.table-striped class="mt-4">
            <x-slot name="headers">
                <x-tables.table-header>{{ __('admin.labels.invite_codes.code') }}</x-tables.table-header>
                <x-tables.table-header>{{ __('admin.labels.invite_codes.used') }}</x-tables.table-header>
                <x-tables.table-header>{{ __('admin.labels.invite_codes.used_by') }}</x-tables.table-header>
                <x-tables.table-header></x-tables.table-header>
            </x-slot>
            <x-slot name="rows">
                @forelse ($inviteCodes as $inviteCode)
                    <x-tables.table-row>
                        <x-tables.table-data>{{ $inviteCode->code }}</x-tables.table-data>
                        <x-tables.table-data>{{ $inviteCode->used ? __('general.yes') : __('general.no') }}</x-tables.table-data>
                        <x-tables.table-data>{{ $inviteCode->used_by ?? '-' }}</x-tables.table-data>
                        <x-tables.table-actions>
                            <x-tables.danger-action wire:click="deleteInviteCode('{{ $inviteCode->id }}')">
                                {{ __('general.buttons.delete') }}
                            </x-tables.danger-action>
                        </x-tables.table-actions>
                    </x-tables.table-row>
                @empty
                    <x-tables.table-row>
                        <x-tables.empty-state colspan="4">
                            {{ __('admin.messages.invite_codes.no_invite_codes') }}
                        </x-tables.empty-state>
                    </x-tables.table-row>
                @endforelse
            </x-slot>
            <x-slot name="pagination">

            </x-slot>
        </x-tables.table-striped>
    </x-containers.main>

    {{-- Create Invite Code Modal --}}
    <x-modals.modal wire:model="showCreateInviteCodeModal">
        <x-slot name="title">
            <p class="text-center">
                {{ __('admin.buttons.invite_codes.create') }}
            </p>
        </x-slot>
        <x-slot name="content">
            <div class="flex flex-col gap-4">
                <div class="flex justify-center bg-yellow-100 p-4 rounded-lg">
                    <p class="text-yellow-800 text-center">
                        {{ __('admin.messages.invite_codes.create_invite_code') }}
                    </p>
                </div>
                <div class="flex flex-col gap-2">
                    <x-forms.text-area wire:model.defer="code" placeholder="{{ __('admin.labels.invite_codes.code') }}" />
                    <x-buttons.primary-button wire:click="generateInviteCode">
                        {{ __('admin.buttons.invite_codes.generate') }}
                    </x-buttons.primary-button>
                </div>
            </div>
        </x-slot>
        <x-slot name="footer">
            <div class="flex justify-end gap-2">
                <x-buttons.secondary-button wire:click="cancelCreation">
                    {{ __('general.buttons.cancel') }}
                </x-buttons.secondary-button>
                <x-buttons.primary-button wire:click="createInviteCode" :disabled="!$code">
                    {{ __('general.buttons.create') }}
                </x-buttons.primary-button>
            </div>
        </x-slot>
    </x-modals.modal>

    {{-- Delete Invite Code Modal --}}
    <x-modals.modal wire:model="showDeleteInviteCodeModal">
        <x-slot name="title">
            <p class="text-center">
                {{ __('admin.titles.invite_codes.delete') }}
            </p>
        </x-slot>
        <x-slot name="content">
            <div class="flex justify-center bg-red-100 p-4 rounded-lg">
                <p class="text-red-800 text-center">
                    {{ __('admin.messages.invite_codes.confirm_delete') }}
                </p>
            </div>
        </x-slot>
        <x-slot name="footer">
            <div class="flex justify-end gap-2">
                <x-buttons.secondary-button wire:click="$set('showDeleteInviteCodeModal', false)">
                    {{ __('general.buttons.cancel') }}
                </x-buttons.secondary-button>
                <x-buttons.danger-button wire:click="confirmDeleteInviteCode">
                    {{ __('general.buttons.delete') }}
                </x-buttons.danger-button>
            </div>
        </x-slot>
    </x-modals.modal>
</div>
