<div class="flex flex-col" style="height: 90vh">

    <div class="w-full flex justify-between bg-white dark:bg-gray-800 shadow-md rounded-lg p-4">
        <div class="w-full flex justify-between">
            <div class="flex gap-x-4">
                <div>
                    <p class="text-sm font-bold uppercase dark:text-white">{{ __('backlog.buckets') }}</p>
                    <div class="flex gap-x-2">
                        <i class="fi fi-sr-bucket dark:text-white"></i>
                        <p class="dark:text-white">{{ count($buckets) }}</p>
                    </div>
                </div>
                <div>
                    <p class="text-sm font-bold uppercase dark:text-white">{{ __('backlog.cards_total') }}</p>
                    <div class="flex gap-x-2">
                        <i class="fi fi-ss-membership-vip dark:text-white"></i>
                        <p class="dark:text-white">{{ $numOfCards }}</p>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-x-4">
                @if ($buckets->count() > 0)
                    <div>
                        <div wire:click="$toggle('createCardModal')" class="px-3 py-2 font-medium text-center flex items-center gap-x-2 text-white bg-blue-700 rounded-lg hover:bg-blue-800 dark:bg-blue-600 dark:hover:bg-blue-700 cursor-pointer">
                            <i class="fi fi-sr-plus-small text-lg flex items-center"></i>
                            <p class="text-sm">{{ __('backlog.new_card') }}</p>
                        </div>
                    </div>
                @endif
                <div wire:click="$toggle('selectedCardModal')" class="px-3 py-2 font-medium text-center flex items-center gap-x-2 text-white bg-blue-700 rounded-lg hover:bg-blue-800 dark:bg-blue-600 dark:hover:bg-blue-700 cursor-pointer">
                    {{ $selectedCardModal ? 'Visible' : 'Open' }}
                </div>
            </div>
        </div>
    </div>
    <div class="w-flex flex flex-grow gap-x-4 mt-4">
        <div class="w-1/4 3xl:w-1/5 bg-white dark:bg-gray-800 shadow-md rounded-lg p-4">
            <div class="flex justify-between">
                <p class="text-lg font-bold dark:text-white">{{ __('backlog.buckets') }}</p>
                <button wire:click="$toggle('createBucketModal')" class="text-3xl">
                    <i class="fi fi-sr-plus-small dark:text-white"></i>
                </button>
            </div>

            {{-- Buckets --}}
            <div class="flex">
                <ul class="w-full">
                    @foreach ($buckets as $bucket)
                        <li class="flex justify-between items-center p-2 {{ $bucket->uuid == $selectedBucket->uuid ? 'bg-blue-600 text-white' : 'bg-gray-100 dark:bg-gray-700' }} rounded-lg mt-2 cursor-pointer" wire:click="selectBucket('{{ $bucket->uuid }}')">
                            <div class="flex items-center gap-x-2">
                                <p class="dark:text-white">{{ $bucket->name }}</p>
                            </div>
                            <div class="flex gap-x-2 items-center">
                                <button wire:click="editBucket('{{ $bucket->uuid }}')">
                                    <i class="fi fi-br-edit dark:text-white hover:text-blue-500"></i>
                                </button>
                                <button wire:click="deleteBucket('{{ $bucket->uuid }}')">
                                    <i class="fi fi-br-trash dark:text-white hover:text-red-500"></i>
                                </button>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
        <div class="w-3/4 3xl:w-4/5">
            @if ($selectedBucket)
                @foreach ($selectedBucket->cards as $card)
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-2 mb-2 cursor-pointer">
                        <div class="flex justify-between items-center">
                            <div class="w-5/6 flex items-center gap-x-2 group" wire:click="selectCard({{ $card->id }})">
                                <p class="text-gray-400">#{{ $card->id }}</p>
                                <p class="dark:text-white group-hover:text-blue-500">{{ $card->name }}</p>
                            </div>

                            <div class="w-1/6 flex justify-end" x-data="{ open: false }">
                                <button @click="open = !open" class="dark:text-white">
                                    <i class="fi fi-sr-menu-dots-vertical"></i>
                                </button>
    
                                <div x-show="open" @click.outside="open = false" class="absolute z-10 bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700 dark:divide-gray-600">
                                    <ul class="py-2 text-sm text-gray-700 dark:text-gray-200">
                                        <li>
                                            <p wire:click="editCard({{ $card->id }})" @click="open = false" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Edit</p>
                                        </li>
                                        <li>
                                            <p wire:click="deleteCard({{ $card->id }})" @click="open = false" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Delete</p>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>

    {{-- Create Bucket Modal --}}
    <x-big-modal wire:model="createBucketModal">
        <x-slot name="title">
            {{ __('backlog.create_bucket') }}
        </x-slot>

        <x-slot name="content">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="col-span-2 w-1/3">
                    <x-label for="name" value="{{ __('backlog.name') }}" />
                    <x-input id="name" type="text" class="mt-1 block w-full" wire:model.defer="name" />
                    <x-input-error for="name" class="mt-2" />
                </div>
                <div class="col-span-2">
                    <x-label for="description" value="{{ __('backlog.description') }}" />
                    <x-textarea id="description" class="mt-1 block w-full" wire:model.defer="description" />
                    <x-input-error for="description" class="mt-2" />
                </div>
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-primary-button wire:click="createBucket" wire:loading.attr="disabled">
                {{ __('backlog.create') }}
            </x-primary-button>
            <x-secondary-button wire:click="$toggle('createBucketModal')" wire:loading.attr="disabled">
                {{ __('backlog.cancel') }}
            </x-secondary-button>
        </x-slot>
    </x-big-modal>

    {{-- Edit Bucket Modal --}}
    <x-big-modal wire:model="editBucketModal">
        <x-slot name="title">
            {{ __('backlog.edit_bucket') }}
        </x-slot>

        <x-slot name="content">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="col-span-2 w-1/3">
                    <x-label for="name" value="{{ __('backlog.name') }}" />
                    <x-input id="name" type="text" class="mt-1 block w-full" wire:model.defer="name" />
                    <x-input-error for="name" class="mt-2" />
                </div>
                <div class="col-span-2">
                    <x-label for="description" value="{{ __('backlog.description') }}" />
                    <x-textarea id="description" class="mt-1 block w-full" wire:model.defer="description" />
                    <x-input-error for="description" class="mt-2" />
                </div>
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-primary-button wire:click="updateBucket" wire:loading.attr="disabled">
                {{ __('backlog.update') }}
            </x-primary-button>
            <x-secondary-button wire:click="$toggle('editBucketModal')" wire:loading.attr="disabled">
                {{ __('backlog.cancel') }}
            </x-secondary-button>
        </x-slot>
    </x-big-modal>
    
    {{-- Delete Bucket Modal --}}
    <x-dialog-modal wire:model="deleteBucketModal">
        <x-slot name="title">
            {{ __('backlog.dialog_delete_title') }}
        </x-slot>

        <x-slot name="content">
            {{ __('backlog.dialog_delete_text') }}
        </x-slot>

        <x-slot name="footer">
            <x-danger-button class="ml-2" wire:click="destroyBucket" wire:loading.attr="disabled">
                {{ __('backlog.delete') }}
            </x-danger-button>

            <x-secondary-button wire:click="$toggle('deleteBucketModal')" wire:loading.attr="disabled">
                {{ __('backlog.cancel') }}
            </x-secondary-button>            
        </x-slot>
    </x-dialog-modal>
    
    {{-- Create Card Modal --}}
    <x-big-modal wire:model="createCardModal">
        <x-slot name="title">
            {{ __('backlog.create_card') }}
        </x-slot>

        <x-slot name="content">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="col-span-2 w-1/3">
                    <x-label for="name" value="{{ __('backlog.name') }}" />
                    <x-input id="name" type="text" class="mt-1 block w-full" wire:model.defer="name" />
                    <x-input-error for="name" class="mt-2" />
                </div>
                <div class="col-span-2">
                    <x-label for="description" value="{{ __('backlog.description') }}" />
                    <x-textarea id="description" class="mt-1 block w-full" wire:model.defer="description" />
                    <x-input-error for="description" class="mt-2" />
                </div>
                <div class="col-span-2 w-1/2">
                    assignee(s)
                    <input type="text" class="hidden" wire:model="assignedTo" value="9c42c7e8-4e75-44e5-8496-3576968a74c0">
                </div>
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-primary-button wire:click="createCard" wire:loading.attr="disabled">
                {{ __('backlog.create') }}
            </x-primary-button>
            <x-secondary-button wire:click="$toggle('createCardModal')" wire:loading.attr="disabled">
                {{ __('backlog.cancel') }}
            </x-secondary-button>
        </x-slot>
    </x-big-modal>

    {{-- Selected Card Modal --}}
    <div class="fixed inset-0 overflow-y-auto w-full h-full z-50 bg-gray-900/60 transform transition-all"
        x-data="{ show: @entangle('selectedCardModal') }"
        x-on:close.stop="show = false"
        x-on:keydown.escape.window="show = false"
        x-show="show"
        style="display: none;"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >

        {{-- Close Button --}}
        <div class="absolute top-0 right-0 p-4">
            <button class="text-white bg-gray-800 rounded-full p-2 cursor-pointer" wire:click="$toggle('selectedCardModal')">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        {{-- Modal --}}
        <div class="w-full flex justify-center mt-12 transform transition-all"
            x-trap.inert.noscroll="show"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        >
            <div class="bg-gray-100 dark:bg-gray-800 rounded-sm w-5/6 p-4">
                {{-- Topbar (Card id, Card Name, Admin Approval, Users, Menu Button) --}}
                @if ($selectedCard)
                    <div class="grid grid-cols-5 p-2">
                        <div class="col-span-3 flex gap-x-4">
                            <div class="flex text-lg text-gray-600">
                                <h1 class="text-gray-400">#</h1>
                                <h1>{{ $selectedCard->id }}
                            </div>
                            <h1 class="text-lg text-gray-600">{{ $selectedCard->name }}</h1>
                        </div>
                        <div class="col-span-2 flex gap-x-4 justify-end">
                            <div>
                                <select class="p-0" wire:model.live="selectedCardApprovalStatus">
                                    <option value="None">None</option>
                                    <option value="Approved">Approved</option>
                                    <option value="Needs work">Needs work</option>
                                    <option value="Rejected">Rejected</option>
                                </select>
                            </div>
                            <div>
                                @if ($selectedCard && isset($selectedCard->assignees)) 
                                    <div class="flex gap-x-2">
                                        <i class="fi fi-sr-users text-gray-700 dark:text-white"></i>
                                        @foreach ($selectedCard->assignees as $assignee)
                                            <img class="w-6 h-6 rounded-full" src="{{ $assignee->user->profile_photo_url }}" alt="{{ $assignee->user->name }}">
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                            <div>
                                <i class="fi fi-sr-menu-dots-vertical"></i>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

        </div>

    </div>

    {{-- <x-pmt-modal wire:model="selectedCardModal" :selected-card="$selectedCard">
        <x-slot name="closeButton">
            <button class="text-white bg-gray-800 rounded-full p-2 cursor-pointer" wire:click="$toggle('selectedCardModal')">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </x-slot>

        <x-slot name="cardId">
            @if ($selectedCard && isset($selectedCard->id))
                #{{ $selectedCard->id }}
            @else
                #0
            @endif
        </x-slot>

        <x-slot name="cardName">
            @if ($selectedCard && isset($selectedCard->name))
                {{ $selectedCard->name }}
            @else
                {{ __('backlog.no_card_selected') }}
            @endif
        </x-slot>

        <x-slot name="adminStatus">
            @if ($selectedCard && isset($selectedCard->admin_status))
                {{-- @if ($selectedCard->admin_status == 'None')
                    <p class="text-sm text-gray-500">{{ __('backlog.approval_status') }}</p>
                @elseif ($selectedCard->admin_status == 'Approved')
                    <p class="text-sm text-green-500">{{ __('backlog.approved') }}</p>
                @elseif ($selectedCard->admin_status == 'Needs work')
                    <p class="text-sm text-yellow-500">{{ __('backlog.needs_work') }}</p>
                @elseif ($selectedCard->admin_status == 'Rejected')
                    <p class="text-sm text-red-500">{{ __('backlog.rejected') }}</p>
                @endif --} }


                <input wire:model="selectedCard.admin_status" type="text" />
                <input wire:model="moi" type="text" />

                

            @else
                <p class="text-sm text-gray-500">{{ __('backlog.approval_status') }}</p>
            @endif
        </x-slot>

        <x-slot name="status">
            {{-- Backlog cards don't have a status. --} }
        </x-slot>

        <x-slot name="users">
            @if ($selectedCard && isset($selectedCard->assignees)) 
                <div class="flex gap-x-2">
                    <i class="fi fi-sr-users text-gray-700 dark:text-white"></i>
                    @foreach ($selectedCard->assignees as $assignee)
                        <img class="w-6 h-6 rounded-full" src="{{ $assignee->user->profile_photo_url }}" alt="{{ $assignee->user->name }}">
                    @endforeach
                </div>
            @endif
        </x-slot>

        <x-slot name="menuButton">
            <i class="fi fi-sr-menu-dots-vertical"></i>
        </x-slot>
    </x-pmt-modal> --}}

</div>