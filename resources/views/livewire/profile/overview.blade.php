<div>
    {{-- Page Title --}}
    @section('title', __('titles.profile.overview'))

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
                'url' => route('profile.overview.render'),
                'label' => __('profile.titles.profile'),
            ]
        ]" />
    </x-slot>

    <div class="mx-20">
        {{-- Personal Information --}}
        <x-profile.profile-card>
            <x-slot name="title">
                {{ __('profile.titles.personal_information') }}
            </x-slot>
            <x-slot name="description">
                {{ __('profile.descriptions.personal_information') }}
            </x-slot>
            <x-slot name="form">
                <div class="grid grid-cols-3 gap-x-2 gap-y-4">
                    <div class="col-span-1 flex flex-col items-center">
                        <div class="w-1/2 aspect-square rounded-full overflow-hidden">
                            <img src="{{ $currentProfileImage
                                ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name ?? 'U') . '&background=random&color=fff&size=256' }}" 
                                alt="{{ auth()->user()->name }}" class="w-full h-full object-cover"
                            >
                        </div>

                        <x-forms.file-input
                            wire:model.live="profileImage" 
                            wrapper:class="mt-2"
                            helper="{{ __('profile.placeholders.image_upload_helper') }}"
                        />
                    </div>

                    <div class="col-span-2"></div>

                    <div class="col-span-1">
                        <x-forms.text-input label="{{ __('profile.labels.username') }}" wire:model="username" required />
                    </div>

                    <div class="col-span-2"></div>

                    <div class="col-span-2">
                        <x-forms.text-input label="{{ __('profile.labels.email') }}" wire:model="email" required />
                    </div>
                </div>

                <div class="flex justify-end items-center gap-x-4">
                    <x-forms.required-fields />
                    <x-buttons.primary-button wire:click="savePersonalInformation" >
                        {{ __('general.buttons.save') }}
                    </x-buttons.primary-button>
                </div>
            </x-slot>
        </x-profile.profile-card>

        <x-containers.divider height="0.5" />

        {{-- Change Password --}}
        <x-profile.profile-card>
            <x-slot name="title">
                {{ __('profile.titles.change_password') }}
            </x-slot>
            <x-slot name="description">
                {{ __('profile.descriptions.change_password') }}
            </x-slot>
            <x-slot name="form">
                <div class="grid grid-cols-3 space-y-4">
                    <div class="col-span-2">
                        <x-forms.password-input label="{{ __('profile.labels.current_password') }}" type="password" wire:model="currentPassword" required />
                    </div>

                    <div class="col-span-1"></div>

                    <div class="col-span-2">
                        <x-forms.password-input label="{{ __('profile.labels.new_password') }}" type="password" wire:model="newPassword" required />
                    </div>

                    <div class="col-span-1"></div>

                    <div class="col-span-2">
                        <x-forms.password-input label="{{ __('profile.labels.confirm_password') }}" type="password" wire:model="newPassword_confirmation" required />
                    </div>
                </div>

                <div class="flex justify-end mt-4">
                    <x-buttons.primary-button wire:click="updatePassword" >
                        {{ __('general.buttons.save') }}
                    </x-buttons.primary-button>
                </div>
            </x-slot>
        </x-profile.profile-card>

        <x-containers.divider height="0.5" />

        {{-- Language --}}
        <x-profile.profile-card>
            <x-slot name="title">
                {{ __('profile.titles.settings.language') }}
            </x-slot>
            <x-slot name="description">
                {{ __('profile.descriptions.settings.language') }}
            </x-slot>
            <x-slot name="form">
                <div class="grid grid-cols-3 gap-x-2 gap-y-4">
                    <div class="col-span-1">
                        <x-forms.select
                            label="{{ __('profile.labels.language') }}"
                            wire:model="language"
                            :options="$languages->map(fn($label, $lang) => ['value' => $lang, 'label' => $label])->toArray()"
                            required
                        />
                    </div>

                    <div class="col-span-2"></div>
                </div>

                <div class="flex justify-end items-center gap-x-4">
                    <x-buttons.primary-button type="submit" wire:click.prevent="saveLanguage" primary>
                        {{ __('general.buttons.save') }}
                    </x-buttons.primary-button>
                </div>
            </x-slot>
        </x-profile.profile-card>

        <x-containers.divider height="0.5" />

        {{-- Sessions --}}
        <x-profile.profile-card>
            <x-slot name="title">
                {{ __('profile.titles.sessions') }}
            </x-slot>
            <x-slot name="description">
                {{ __('profile.descriptions.sessions') }}
            </x-slot>
            <x-slot name="form">
                <div class="grid grid-cols-3 space-y-4">
                    <div class="w-3/4 col-span-3 text-gray-600 dark:text-gray-400 text-sm">
                        {!! __('profile.messages.sessions') !!}
                    </div>
                    <div class="col-span-3 space-y-2">
                        @forelse ($sessions as $session)
                            <x-profile.session-card :session="$session" />
                        @empty
                            <div class="col-span-3 text-center text-gray-600 dark:text-gray-400 text-sm">
                                {{ __('profile.messages.no_sessions') }}
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="flex justify-end items-center gap-x-4">
                    <x-buttons.secondary-button wire:click="logoutOtherSessions" >
                        {{ __('profile.buttons.logout_other_sessions') }}
                    </x-buttons.secondary-button>
                </div>
            </x-slot>
        </x-profile.profile-card>

        <x-containers.divider height="0.5" />

        {{-- Delete Account --}}
        <x-profile.profile-card>
            <x-slot name="title">
                {{ __('profile.titles.delete_account') }}
            </x-slot>
            <x-slot name="description">
                {{ __('profile.descriptions.delete_account') }}
            </x-slot>
            <x-slot name="form">
                <div class="grid grid-cols-3 space-y-4">
                    <div class="w-3/4 col-span-3 text-gray-600 dark:text-gray-400 text-sm">
                        {!! __('profile.messages.delete_account') !!}
                    </div>
                </div>

                <div class="flex justify-end items-center gap-x-4">
                    <x-buttons.danger-button wire:click="$set('showAccountDeletionModal', true)" >
                        {{ __('profile.buttons.delete_account') }}
                    </x-buttons.danger-button>
                </div>
            </x-slot>
        </x-profile.profile-card>
    </div>

    {{-- Account Deletion Modal --}}
    <x-modals.modal wire:model="showAccountDeletionModal">
        <x-slot name="title">
            <p class="text-center">
                {{ __('profile.titles.delete_account') }}
            </p>
        </x-slot>

        <x-slot name="content">
            <div class="text-gray-600 dark:text-gray-400 text-sm">
                {!! __('profile.messages.delete_account_modal') !!}
            </div>

            <div class="mt-4">
                <x-forms.password-input label="{{ __('profile.labels.current_password') }}" type="password" wire:model="currentPasswordDelete" required />
            </div>
        </x-slot>

        <x-slot name="footer">
            <div class="flex justify-end items-center gap-x-4">
                <x-buttons.secondary-button wire:click="cancelAccountDeletion" >
                    {{ __('general.buttons.cancel') }}
                </x-buttons.secondary-button>

                <x-buttons.danger-button wire:click="deleteAccount" >
                    {{ __('profile.buttons.delete_account') }}
                </x-buttons.danger-button>
            </div>
        </x-slot>
    </x-modals.modal>
</div>
