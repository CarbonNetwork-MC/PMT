<div class="flex justify-center mt-8">
    <div class="w-4/5 bg-white dark:bg-gray-800 shadow-md rounded-md">
        <div class="p-4">
            {{-- Tabs --}}
            @livewire('components.settings-tabs', ['uuid' => $uuid])

            {{-- Content --}}
            <div class="p-4">
                <div class="grid grid-cols-8 gap-x-4">
                    <div class="col-span-8 md:col-span-7">
                        <input type="text" id="search" wire:model.live="search" autocomplete="off" class="w-full dark:bg-gray-900 dark:text-white border border-gray-300 dark:border-gray-700 rounded p-2 mt-2" placeholder="{{ __('settings.filter_users') }}" />
                    </div>
                    @if ($project->owner_id == auth()->user()->uuid)
                        <div class="col-span-8 md:col-span-1">
                            <button wire:click="$toggle('addMemberModal')" class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mt-2">{{ __('settings.add_user') }}</button>
                        </div>
                    @endif
                </div>
                <div class="mt-8">
                    @forelse ($projectMembers as $member)
                        <div class="flex justify-between items-center border-b border-gray-300 dark:border-gray-700 p-2">
                            <div class="flex items-center gap-x-2">
                                <img src="{{ $member->user->profile_photo_url }}" class="w-8 h-8 rounded-full" alt="{{ $member->user->name }}" />
                                <p class="text-sm dark:text-white">{{ $member->user->name }}</p>
                            </div>
                            <div class="flex gap-x-4">
                                @if ($member->role_id == 3)
                                    <span class="text-sm dark:text-white font-semibold">{{ __('settings.project_owner') }}</span>
                                @else
                                    @if ($userRole == 1)
                                        <span class="text-sm dark:text-white font-semibold">{{ $member->role->name }}</span>
                                    @else
                                        <select class="dark:bg-gray-900 dark:text-white border border-gray-300 dark:border-gray-700 rounded p-2" wire:model="role.{{ $member->id }}" wire:change="updateRole({{ $member->id }}, $event.target.value)">
                                            @foreach ($roles as $role)
                                                @if ($role->id == 3)
                                                    @continue
                                                @endif
                                                <option value="{{ $role->id }}" {{ $member->role_id === $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                                            @endforeach
                                        </select>

                                        <button wire:click="initializeRemoveMember({{ $member->id }})" class="flex items-center justify-center bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                            <i class="fi fi-br-cross text-sm"></i>
                                        </button>
                                    @endif
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center">{{ __('settings.no_members') }}</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Add Member Modal --}}
    <x-pmt-modal wire:model="addMemberModal">
        <x-slot name="title">
            {{ __('settings.add_user') }}
        </x-slot>

        <x-slot name="content">
            <p class="text-black dark:text-white">Grant access to the project by adding members via email (separated by comma's)</p>
            <div class="grid grid-cols-5 gap-x-4">
                <div class="col-span-5 md:col-span-3">
                    <textarea wire:model="emails" class="w-full dark:bg-gray-900 dark:text-white border border-gray-300 dark:border-gray-700 rounded p-2 mt-2 resize-none" rows="5" placeholder="Email addresses"></textarea>
                </div>
                <div class="col-span-5 md:col-span-1">
                    <select wire:model="role_id" class="w-full dark:bg-gray-900 dark:text-white border border-gray-300 dark:border-gray-700 rounded p-2 mt-2">
                        @foreach ($roles as $role)
                            @if ($role->id == 3)
                                @continue
                            @endif
                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-primary-button wire:click="addMember">
                {{ __('settings.add_user') }}
            </x-primary-button>

            <x-secondary-button wire:click="$set('addMemberModal', false)">
                {{ __('settings.cancel') }}
            </x-secondary-button>
        </x-slot>
    </x-pmt-modal>

    {{-- Delete Member Modal --}}
    <x-dialog-modal wire:model="deleteMemberModal">
        <x-slot name="title">
            {{ __('settings.dialog_remove_member_title') }}
        </x-slot>

        <x-slot name="content">
            {{ __('settings.dialog_remove_member_text') }}
        </x-slot>

        <x-slot name="footer">
            <x-danger-button class="ml-2" wire:click="removeMember">
                {{ __('settings.remove') }}
            </x-danger-button>

            <x-secondary-button wire:click="$set('deleteMemberModal', false)">
                {{ __('settings.cancel') }}
            </x-secondary-button>
        </x-slot>
    </x-dialog-modal>

    <script>
        window.addEventListener('DOMContentLoaded', () => {
            toastr.options.positionClass = 'toast-bottom-right';
            window.addEventListener('roleUpdated', event => {
                toastr.success(event.detail[0].message);
            })

            window.addEventListener('memberRemoved', event => {
                toastr.success(event.detail[0].message);
            })

            window.addEventListener('memberAdded', event => {
                toastr.success(event.detail[0].message);
            })
        })
    </script>
</div>