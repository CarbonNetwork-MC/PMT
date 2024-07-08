<div class="flex justify-center mt-8">
    <div class="w-4/5 bg-white dark:bg-gray-800 shadow-md rounded-md">
        <div class="p-4">
            {{-- Tabs --}}
            @livewire('components.settings-tabs', ['uuid' => $uuid])

            {{-- Content --}}
            <div class="mt-4 p-4">
                <!-- Change Ownership -->
                <div class="flex justify-between items-center border-b border-gray-300 dark:border-gray-700 pb-4">
                    <p class="text-lg dark:text-white">{{ __('settings.change_owner_text') }}</p>
                    <button wire:click="$toggle('changeOwnerModal')" class="text-red-500 border border-2 border-red-500 hover:bg-red-500 hover:text-gray-100 font-bold py-2 px-4 rounded">
                        {{ __('settings.change_owner') }}
                    </button>
                </div>

                <!-- Remove Project -->
                <div class="flex justify-between items-center pt-4">
                    <p class="text-lg dark:text-white">{{ __('settings.delete_project_text') }}</p>
                    <button wire:click="initializeProjectDeletion('{{ $uuid }}')" class="text-red-500 border border-2 border-red-500 hover:bg-red-500 hover:text-gray-100 font-bold py-2 px-4 rounded">
                        {{ __('settings.delete_project') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Change Owner Modal --}}
    <x-pmt-modal wire:model="changeOwnerModal">
        <x-slot name="title">
            {{ __('settings.change_owner') }}
        </x-slot>

        <x-slot name="content">
            <p class="dark:text-white font-semibold">{{ __('settings.select_owner') }}</p>
            <div class="grid grid-cols-2 mt-2">
                <div class="col-span-1">
                    <select wire:model="newOwner" class="w-full border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white rounded p-2">
                        <option value="">{{ __('settings.select_new_owner') }}</option>
                        @foreach ($projectMembers as $member)
                            @if ($member->role->id == 3)
                                @continue
                            @endif
                            <option value="{{ $member->user_id }}">{{ $member->user->name }} ({{ $member->role->name }})</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-danger-button wire:click="changeOwner">
                {{ __('settings.change_owner') }}
            </x-danger-button>

            <x-secondary-button wire:click="$toggle('changeOwnerModal')">
                {{ __('settings.cancel') }}
            </x-secondary-button>
        </x-slot>
    </x-pmt-modal>

    {{-- Delete Project Modal --}}
    <x-dialog-modal wire:model="deleteProjectModal">
        <x-slot name="title">
            {{ __('settings.dialog_delete_project_title') }}
        </x-slot>

        <x-slot name="content">
            <p>{{ __('settings.dialog_delete_project_text') }}</p>
        </x-slot>

        <x-slot name="footer">
            <x-danger-button wire:click="deleteProject">
                {{ __('settings.delete') }}
            </x-danger-button>

            <x-secondary-button wire:click="$toggle('deleteProjectModal')">
                {{ __('settings.cancel') }}
            </x-secondary-button>
        </x-slot>
    </x-dialog-modal>

    <script>
        window.addEventListener('DOMContentLoaded', () => {
            toastr.options.positionClass = 'toast-bottom-right';
            window.addEventListener('changedOwner', event => {
                toastr.success(event.detail[0].message);
            });

            window.addEventListener('projectDeleted', event => {
                toastr.success(event.detail[0].message);
            });
        })
    </script>

</div>