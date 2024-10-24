<div class="flex flex-col" style="height: 90vh">
    <x-slot name="title">
        {{ __('backlog.backlog') }}
    </x-slot>

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
            </div> 
        </div>
    </div>
    <div class="w-full flex flex-grow gap-x-4 mt-4">
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
                            <div class="w-full flex gap-x-2 group" wire:click="selectCard({{ $card->id }})">
                                <p class="text-gray-400">#{{ $card->id }}</p>
                                <p class="dark:text-white group-hover:text-blue-500">{{ $card->name }}</p>
                            </div>
                            <div class="flex gap-x-6 ml-10">
                                <div class="text-{{ $approvalStatusOptions[$card->approval_status] }}-500 border border-{{ $approvalStatusOptions[$card->approval_status] }}-500 hover:bg-{{ $approvalStatusOptions[$card->approval_status] }}-500 focus:ring-2 focus:ring-{{ $approvalStatusOptions[$card->approval_status] }}-500 hover:text-black font-medium rounded-lg text-sm px-5 py-1 text-center">
                                    {{ $card->approval_status }}
                                </div>
                                <div class="flex items-center gap-x-2">
                                    <i class="fi fi-sr-list-check flex items-center dark:text-white"></i>
                                    <p class="dark:text-white">{{ $card->tasks->count() }}</p>
                                </div>
                                <div class="relative flex items-center" x-data="{ open: false, moveTo: false }">
                                    <i @click="open = !open" wire:click="selectCard('{{ $card->id }}', false)" class="fi fi-sr-menu-dots-vertical flex items-center dark:text-white"></i>
        
                                    <div x-show="open" @click.outside="open = false" class="absolute z-10 top-10 -left-40 bg-white rounded-lg shadow w-44 dark:bg-gray-800">
                                        <ul class="py-2 text-sm text-gray-700 dark:text-gray-200">
                                            <li>
                                                <p class="flex justify-center text-gray-400 dark:text-gray-300">{{ __('backlog.actions') }} - #{{ $card->id }}</p>
                                            </li>
                                            <hr class="h-px my-2 bg-gray-200 border-0 dark:bg-gray-600">
                                            <li>
                                                <p wire:click="assignCardToMe" @click="open = false" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer">{{ __('backlog.assign_me') }}</p>
                                            </li>
                                            <hr class="h-px my-2 bg-gray-200 border-0 dark:bg-gray-600">
                                            <li>
                                                <p @click="open = false; moveTo = true" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer">{{ __('backlog.move_to') }}</p>
                                            </li>
                                            <li>
                                                <p @click="open = false" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer">{{ __('backlog.make_copy') }}</p>
                                            </li>
                                            <hr class="h-px my-2 bg-gray-200 border-0 dark:bg-gray-600">
                                            <li>
                                                <p wire:click="deleteCard({{ $card->id }})" @click="open = false" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer">{{ __('backlog.delete') }}</p>
                                            </li>
                                        </ul>
                                    </div>
                                    <div x-show="moveTo" @click.outside="moveTo = false" class="absolute z-10 top-10 -left-56 bg-white dark:bg-gray-800 rounded-lg shadow w-60">
                                        <ul class="py-2 text-sm text-gray-700 dark:text-gray-200">
                                            <li>
                                                <p class="flex justify-center text-gray-400 dark:text-gray-300">{{ __('backlog.move_to') }} - #{{ $card->id }}</p>
                                            </li>
                                            <hr class="h-px my-2 bg-gray-200 border-0 dark:bg-gray-600">
                                            <li class="p-2">
                                                <p class="dark:text-white">{{ __('backlog.select_destination') }}</p>
                                                {{-- Projects --}}
                                                <select wire:model="selectedProject" class="w-full mt-2 border-sky-500 bg-gray-100 dark:bg-gray-800 text-sm rounded-lg text-gray-600 dark:text-gray-400">
                                                    @forelse ($projects as $project)
                                                        <option value="{{ $project->uuid }}">{{ $project->name }}</option>
                                                    @empty
                                                        <option value="">{{ __('backlog.select_project') }}</option>
                                                    @endforelse
                                                </select>
                                                {{-- Backlog/Sprint --}}
                                                <select wire:model.live="backlogOrSprint" class="w-full mt-2 border-sky-500 bg-gray-100 dark:bg-gray-800 text-sm rounded-lg text-gray-600 dark:text-gray-400">
                                                    <option value="backlog">{{ __('backlog.backlog') }}</option>
                                                    <option value="sprint">{{ __('backlog.sprint') }}</option>
                                                </select>
                                                {{-- Backlog / Sprint name --}}
                                                <select wire:model="backlogOrSprintName" class="w-full mt-2 border-sky-500 bg-gray-100 dark:bg-gray-800 text-sm rounded-lg text-gray-600 dark:text-gray-400">
                                                    @if ($backlogOrSprint === 'backlog')
                                                        @forelse ($buckets as $bucket)
                                                            <option value="{{ $bucket->uuid }}">{{ $bucket->name }}</option>
                                                        @empty
                                                            <option value="">{{ __('backlog.select_bucket') }}</option>
                                                        @endforelse
                                                    @elseif ($backlogOrSprint === 'sprint')
                                                        @forelse ($sprints as $sprint)
                                                            <option value="{{ $sprint->uuid }}">{{ $sprint->name }}</option>
                                                        @empty
                                                            <option value="">{{ __('backlog.select_sprint') }}</option>
                                                        @endforelse
                                                    @endif
                                                </select>
                                                {{-- Sprint Column --}}
                                                @if ($backlogOrSprint === 'sprint') 
                                                    <select wire:model="sprintColumn" class="w-full mt-2 border-sky-500 bg-gray-100 dark:bg-gray-800 text-sm rounded-lg text-gray-600 dark:text-gray-400">
                                                        <option value="todo">{{ __('backlog.todo') }}</option>
                                                        <option value="doing">{{ __('backlog.doing') }}</option>
                                                        <option value="testing">{{ __('backlog.testing') }}</option>
                                                        <option value="done">{{ __('backlog.done') }}</option>
                                                        <option value="released">{{ __('backlog.released') }}</option>
                                                    </select>
                                                @endif
                                                {{-- Position: Top/Bottom --}}
                                                <select wire:model="position" class="w-full mt-2 border-sky-500 bg-gray-100 dark:bg-gray-800 text-sm rounded-lg text-gray-600 dark:text-gray-400">
                                                    <option value="top">{{ __('backlog.top') }}</option>
                                                    <option value="bottom">{{ __('backlog.bottom') }}</option>
                                                </select>
                                                {{-- Submit --}}
                                                <div class="flex justify-center mt-4">
                                                    <button @click="moveTo = false" wire:click="moveCard" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700">
                                                        {{ __('backlog.move') }}
                                                    </button>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
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
            {{ __('backlog.dialog_delete_title_bucket') }}
        </x-slot>

        <x-slot name="content">
            {{ __('backlog.dialog_delete_text_bucket') }}
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

    {{-- Delete Card Modal --}}
    <x-dialog-modal wire:model="deleteCardModal">
        <x-slot name="title">
            {{ __('backlog.dialog_delete_title_card') }}
        </x-slot>

        <x-slot name="content">
            {{ __('backlog.dialog_delete_text_card') }}
        </x-slot>

        <x-slot name="footer">
            <x-danger-button class="ml-2" wire:click="destroyCard" wire:loading.attr="disabled">
                {{ __('backlog.delete') }}
            </x-danger-button>

            <x-secondary-button wire:click="$toggle('deleteCardModal')" wire:loading.attr="disabled">
                {{ __('backlog.cancel') }}
            </x-secondary-button>            
        </x-slot>
    </x-dialog-modal>

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
        x-transition:leave-end="opacity-0">

        {{-- Close Button --}}
        <div class="absolute top-0 right-0 p-4">
            <button class="text-white bg-gray-800 rounded-full p-2 cursor-pointer" wire:click="$toggle('selectedCardModal')">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        {{-- Modal --}}
        <div class="w-full h-[90vh] flex justify-center mt-12 transform transition-all"
            x-trap.inert.noscroll="show"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
            <div class="bg-gray-100 h-full flex flex-col dark:bg-gray-800 rounded-sm w-5/6 p-4">
                @if ($selectedCard)
                    <div class="grid grid-cols-5 p-2">
                        <div class="col-span-3 flex gap-x-4">
                            <div class="flex items-center text-lg">
                                <h1 class="text-gray-400 dark:text-gray-500">#</h1>
                                <h1 class="text-gray-600 dark:text-gray-400">{{ $selectedCard->id }}
                            </div>
                            <div class="flex items-center">
                                @if ($isEditingCardName)
                                    <input id="cardNameInput" type="text" wire:model="name" wire:blur="saveCardName" 
                                        class="border-0 px-2 py-1 border-b-2 border-gray-600 dark:border-gray-300 bg-transparent focus:outline-none focus:border-blue-500 text-lg text-gray-600 dark:text-gray-400" />
                                @else
                                    <h1 wire:click="startEditingCardName" class="text-lg text-gray-600 dark:text-gray-400">{{ $selectedCard->name }}</h1>
                                @endif
                            </div>
                        </div>
                        <div class="col-span-2 flex gap-x-4 justify-end">
                            <div x-data="{ open: false }" class="flex items-center">
                                <button @click="open = !open" class="text-{{ $selectedCardColor }}-500 border border-{{ $selectedCardColor }}-400 hover:bg-{{ $selectedCardColor }}-500 focus:ring-2 focus:ring-{{ $selectedCardColor }}-500 hover:text-black font-medium rounded-lg text-sm px-5 py-1 text-center">
                                    {{ $selectedCard->approval_status }}
                                </button>
                                <div x-show="open" @click.away="open = false" class="z-10 absolute top-16 mt-2 w-44 bg-white dark:bg-gray-800 rounded-md shadow-lg divide-y divide-gray-100">
                                    <div class="py-2 flex justify-center text-sm text-gray-300">
                                        Change Approval Status
                                    </div>
                                    <ul class="py-2 text-sm text-gray-700 dark:text-gray-200">
                                        @foreach ($approvalStatusOptions as $option => $color)
                                            <li class="px-1 cursor-pointer">
                                                <p @click="open = false" wire:click="changeStatus('{{ $option }}')" class="px-4 py-2 dark:text-white hover:bg-gray-300">{{ $option }}</p>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                            <div class="flex items-center">
                                @if ($selectedCard && isset($selectedCard->assignees))
                                    <div class="flex items-center gap-x-2 bg-gray-200 dark:bg-gray-700 px-2.5 py-1.5 rounded-full" x-data="{ open: false }">
                                        <div @click="open = !open" class="relative">
                                            <i class="fi fi-sr-users flex items-center text-gray-700 dark:text-white cursor-pointer"></i>
                                            <div x-show="open" @click.away="open = false" class="absolute mt-2 w-60 bg-white dark:bg-gray-800 rounded-md shadow-lg z-10">
                                                <ul class="py-2 text-sm text-gray-700 dark:text-gray-200">
                                                    <li class="flex justify-center items-center">
                                                        <p class="text-gray-400 dark:text-gray-300 text-sm">Users - #{{ $selectedCard->id }}</p>
                                                    </li>
                                                    <hr class="h-px my-2 bg-gray-200 border-0 dark:bg-gray-600">
                                                    @foreach ($projectMembers as $member)
                                                        <li class="px-1">
                                                            @if ($selectedCard->assignees->contains('user_id', $member->user_id))
                                                                <div wire:click="removeAssignee({{ $member->id }})" class="w-full flex justify-between bg-gray-300 dark:bg-gray-700 cursor-pointer p-2">
                                                                    <div class="flex items-center gap-x-2">
                                                                        <img class="w-6 h-6 rounded-full" src="{{ $member->user->profile_photo_url }}" alt="{{ $member->user->name }}">
                                                                        <p class="dark:text-white">{{ $member->user->name }}</p>
                                                                    </div>
                                                                    <i class="fi fi-ss-user-check flex items-center dark:text-white"></i>
                                                                </div>
                                                            @else
                                                                <div wire:click="addAssignee({{ $member->id }})" class="w-full hover:bg-gray-300 dark:hover:bg-gray-700 cursor-pointer p-2">
                                                                    <div class="flex items-center gap-x-2">
                                                                        <img class="w-6 h-6 rounded-full" src="{{ $member->user->profile_photo_url }}" alt="{{ $member->user->name }}">
                                                                        <p class="dark:text-white">{{ $member->user->name }}</p>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                        @if ($selectedCard->assignees->count() > 0)
                                            <div class="flex items-center gap-x-2 rounded-full">
                                                @foreach ($selectedCard->assignees->slice(0, 3) as $assignee)
                                                    <img class="w-6 h-6 rounded-full" src="{{ $assignee->user->profile_photo_url }}" alt="{{ $assignee->user->name }}">
                                                @endforeach
                                                @if ($selectedCard->assignees->count() > 3)
                                                    <p class="text-sm text-gray-700 dark:text-white">+{{ $selectedCard->assignees->count() - 3 }}</p>
                                                @endif
                                            </div>
                                        @else
                                            <p class="text-sm text-gray-700 dark:text-white">No users assigned</p>
                                        @endif
                                    </div>
                                @endif
                            </div>
                            <div class="relative flex items-center" x-data="{ open: false, moveTo: false }">
                                <i @click="open = !open" class="fi fi-sr-menu-dots-vertical dark:text-white cursor-pointer"></i>
                                <div x-show="open" @click.outside="open = false" class="absolute z-10 top-10 bg-white rounded-lg shadow w-44 dark:bg-gray-800">
                                    <ul class="py-2 text-sm text-gray-700 dark:text-gray-200">
                                        <li>
                                            <p class="flex justify-center text-gray-400 dark:text-gray-300">{{ __('backlog.actions') }} - #{{ $selectedCard->id }}</p>
                                        </li>
                                        <hr class="h-px my-2 bg-gray-200 border-0 dark:bg-gray-600">
                                        <li>
                                            <p wire:click="assignCardToMe" @click="open = false" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer">{{ __('backlog.assign_me') }}</p>
                                        </li>
                                        <hr class="h-px my-2 bg-gray-200 border-0 dark:bg-gray-600">
                                        <li>
                                            <p @click="open = false; moveTo = true" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer">{{ __('backlog.move_to') }}</p>
                                        </li>
                                        <li>
                                            <p @click="open = false" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer">{{ __('backlog.make_copy') }}</p>
                                        </li>
                                        <hr class="h-px my-2 bg-gray-200 border-0 dark:bg-gray-600">
                                        <li>
                                            <p wire:click="deleteCard({{ $card->id }})" @click="open = false" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer">{{ __('backlog.delete') }}</p>
                                        </li>
                                    </ul>
                                </div>
                                <div x-show="moveTo" @click.outside="moveTo = false" class="absolute z-10 top-10 -left-40 bg-white dark:bg-gray-800 rounded-lg shadow w-60">
                                    <ul class="py-2 text-sm text-gray-700 dark:text-gray-200">
                                        <li>
                                            <p class="flex justify-center text-gray-400 dark:text-gray-300">{{ __('backlog.move_to') }} - #{{ $card->id }}</p>
                                        </li>
                                        <hr class="h-px my-2 bg-gray-200 border-0 dark:bg-gray-600">
                                        <li class="p-2">
                                            <p class="dark:text-white">{{ __('backlog.select_destination') }}</p>
                                            {{-- Projects --}}
                                            <select wire:model="selectedProject" class="w-full mt-2 border-sky-500 bg-gray-100 dark:bg-gray-800 text-sm rounded-lg text-gray-600 dark:text-gray-400">
                                                @forelse ($projects as $project)
                                                    <option value="{{ $project->uuid }}">{{ $project->name }}</option>
                                                @empty
                                                    <option value="">{{ __('backlog.select_project') }}</option>
                                                @endforelse
                                            </select>
                                            {{-- Backlog/Sprint --}}
                                            <select wire:model.live="backlogOrSprint" class="w-full mt-2 border-sky-500 bg-gray-100 dark:bg-gray-800 text-sm rounded-lg text-gray-600 dark:text-gray-400">
                                                <option value="backlog">{{ __('backlog.backlog') }}</option>
                                                <option value="sprint">{{ __('backlog.sprint') }}</option>
                                            </select>
                                            {{-- Backlog / Sprint name --}}
                                            <select wire:model="backlogOrSprintName" class="w-full mt-2 border-sky-500 bg-gray-100 dark:bg-gray-800 text-sm rounded-lg text-gray-600 dark:text-gray-400">
                                                @if ($backlogOrSprint === 'backlog')
                                                    @forelse ($buckets as $bucket)
                                                        <option value="{{ $bucket->uuid }}">{{ $bucket->name }}</option>
                                                    @empty
                                                        <option value="">{{ __('backlog.select_bucket') }}</option>
                                                    @endforelse
                                                @elseif ($backlogOrSprint === 'sprint')
                                                    @forelse ($sprints as $sprint)
                                                        <option value="{{ $sprint->uuid }}">{{ $sprint->name }}</option>
                                                    @empty
                                                        <option value="">{{ __('backlog.select_sprint') }}</option>
                                                    @endforelse
                                                @endif
                                            </select>
                                            {{-- Sprint Column --}}
                                            @if ($backlogOrSprint === 'sprint') 
                                                <select wire:model="sprintColumn" class="w-full mt-2 border-sky-500 bg-gray-100 dark:bg-gray-800 text-sm rounded-lg text-gray-600 dark:text-gray-400">
                                                    <option value="todo">{{ __('backlog.todo') }}</option>
                                                    <option value="doing">{{ __('backlog.doing') }}</option>
                                                    <option value="testing">{{ __('backlog.testing') }}</option>
                                                    <option value="done">{{ __('backlog.done') }}</option>
                                                    <option value="released">{{ __('backlog.released') }}</option>
                                                </select>
                                            @endif
                                            {{-- Position: Top/Bottom --}}
                                            <select wire:model="position" class="w-full mt-2 border-sky-500 bg-gray-100 dark:bg-gray-800 text-sm rounded-lg text-gray-600 dark:text-gray-400">
                                                <option value="top">{{ __('backlog.top') }}</option>
                                                <option value="bottom">{{ __('backlog.bottom') }}</option>
                                            </select>
                                            {{-- Submit --}}
                                            <div class="flex justify-center mt-4">
                                                <button @click="moveTo = false" wire:click="moveCard" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700">
                                                    {{ __('backlog.move') }}
                                                </button>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Description & Tasks --}}
                    <div class="h-full flex flex-col bg-white dark:bg-gray-700 p-4 mt-2">
                        @if ($isEditingCardDescription)
                            <textarea wire:model="description" wire:blur="saveCardDescription" 
                                class="border-0 px-2 py-1 border-b-2 border-gray-600 dark:border-gray-300 bg-transparent focus:outline-none focus:border-blue-500 text-lg text-gray-600 dark:text-gray-400">
                            </textarea>
                        @else
                            <div class="flex gap-x-2" x-on:mouseover="hover = true" x-on:mouseout="hover = false" x-data="{ hover: false }">
                                <p class="text-sm text-gray-700 dark:text-gray-200 cursor-pointer">{{ $selectedCard->description }}</p>
                                <i x-show="hover" wire:click="startEditingCardDescription('{{ $selectedCard->id }}')" class="fi fi-bs-pencil text-sm hover:text-sky-500 dark:text-white cursor-pointer"></i>
                            </div>
                        @endif
                        <hr class="h-px my-8 bg-gray-200 border-0 dark:bg-gray-800">
                        <div class="h-full grid grid-cols-3 gap-x-4">
                            {{-- Tasks - To Do --}}
                            <div class="h-full col-span-1 bg-gray-100 dark:bg-gray-800 rounded-md"
                                x-data
                                x-init="Sortable.create($refs.todoTasks, {
                                    group: 'tasks',
                                    animation: 150,
                                    onEnd: function (evt) {
                                        @this.call('updateTaskOrder', evt.item.dataset.id, evt.to.dataset.column, evt.newIndex);
                                    }
                                })">
                                <div class="flex justify-between p-2">
                                    <div class="flex items-center gap-x-2 text-purple-600">
                                        <p class="flex items-center justify-center rounded-md text-sm font-bold bg-purple-600 text-white px-1.5 py-0.5">
                                            {{ $selectedCard->tasks->where('status', 'todo')->count() }}
                                        </p>
                                        <h1 class="text-sm font-bold dark:text-white">{{ __('backlog.tasks') }} - {{ __('backlog.todo') }}</h1>
                                    </div>
                                    <div wire:click="createTask('todo')" class="flex items-center mr-1 cursor-pointer">
                                        <i class="fi fi-sr-plus flex items-center text-sm text-black dark:text-white"></i>
                                    </div>
                                </div>
                                <div class="p-2" x-ref="todoTasks" data-column="todo">
                                    @if ($isCreatingTask && $createdTaskColumn == 'todo')
                                        <div class="bg-white p-2 mb-2">
                                            <input type="text" wire:model="taskDescription" wire:keydown.enter="storeTask('todo')" wire:blur="cancelTaskCreation" class="w-full text-sm px-2 py-1 border-0 border-b-2 border-emerald-500 bg-transparent focus:outline-none focus:border-blue-500 text-lg text-gray-600 dark:text-gray-400" placeholder="{{ __('backlog.create_task') }}">
                                        </div>
                                    @endif

                                    @foreach ($selectedCard->tasks->sortBy('task_index') as $task)
                                        @if ($task->status == 'todo')
                                            <div class="bg-white dark:bg-gray-700 p-2 mb-2 rounded-md cursor-move" data-id="{{ $task->id }}" wire:key="task-{{ $task->id }}">
                                                <div class="flex justify-between">
                                                    <p class="flex items-center text-gray-400 text-xs">#{{ $task->id }}</p>
                                                    <div class="relative" x-data="{ open: false, moveTo: false }">
                                                        <i wire:click="selectTask('{{ $task->id }}')" @click="open = !open" class="fi fi-sr-menu-dots-vertical text-xs dark:text-white cursor-pointer"></i>
                                                        <div x-show="open" @click.outside="open = false" class="absolute z-10 top-8 bg-white rounded-lg shadow w-44 dark:bg-gray-800">
                                                            <ul class="py-2 text-sm text-gray-700 dark:text-gray-200">
                                                                <li>
                                                                    <p class="flex justify-center text-gray-400 dark:text-gray-300">{{ __('backlog.actions') }} - {{ __('backlog.task') }} #{{ $task->id }}</p>
                                                                </li>
                                                                <hr class="h-px my-2 bg-gray-200 border-0 dark:bg-gray-600">
                                                                <li>
                                                                    <p @click="open = false" wire:click="assignTaskToMe" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer">{{ __('backlog.assign_me') }}</p>
                                                                </li>
                                                                <hr class="h-px my-2 bg-gray-200 border-0 dark:bg-gray-600">
                                                                <li>
                                                                    <p @click="open = false; moveTo = true" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer">{{ __('backlog.move_to') }}</p>
                                                                </li>
                                                                <li>
                                                                    <p @click="open = false" wire:click="copyTask('{{ $task->id }}')" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer">{{ __('backlog.make_copy') }}</p>
                                                                </li>
                                                                <li>
                                                                    <p @click="open = false" wire:click="convertToCard('{{ $task->id }}')" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer">{{ __('backlog.convert_to_card') }}</p>
                                                                </li>
                                                                <hr class="h-px my-2 bg-gray-200 border-0 dark:bg-gray-600">
                                                                <li>
                                                                    <p @click="open = false" wire:click="deleteTask('{{ $task->id }}')" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer">{{ __('backlog.delete') }}</p>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                        <div x-show="moveTo" @click.outside="moveTo = false" class="absolute z-10 top-8 bg-white dark:bg-gray-800 rounded-lg shadow w-60">
                                                            <ul class="py-2 text-sm text-gray-700 dark:text-gray-200">
                                                                <li>
                                                                    <p class="flex justify-center text-gray-400 dark:text-gray-300">{{ __('backlog.move_to_column') }} - {{ __('backlog.task') }} #{{ $task->id }}</p>
                                                                </li>
                                                                <hr class="h-px my-2 bg-gray-200 border-0 dark:bg-gray-600">
                                                                <li>
                                                                    <p @click="open = false" wire:click="moveTask('{{ $task->id }}', 'todo')" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer">{{ __('backlog.todo') }}</p>
                                                                </li>
                                                                <li>
                                                                    <p @click="open = false" wire:click="moveTask('{{ $task->id }}', 'doing')" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer">{{ __('backlog.doing') }}</p>
                                                                </li>
                                                                <li>
                                                                    <p @click="open = false" wire:click="moveTask('{{ $task->id }}', 'done')" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer">{{ __('backlog.done') }}</p>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                                @if ($isEditingTaskDescription && (int) $editingTaskId === $task->id)
                                                    <textarea wire:model="taskDescription" wire:blur="saveTaskDescription('{{ $task->id }}')" 
                                                        class="w-full border-0 mt-1 px-2 py-1 border-b-2 border-gray-600 dark:border-gray-300 bg-transparent focus:outline-none focus:border-blue-500 text-lg text-gray-600 dark:text-gray-400">
                                                        {{ $task->description }}
                                                    </textarea>
                                                @else
                                                    <div class="flex justify-between mt-1 cursor-pointer group" x-on:mouseover="hover = true" x-on:mouseout="hover = false" x-data="{ hover: false }">
                                                        <p class="w-full text-sm group-hover:text-sky-500 dark:text-white">{{ $task->description }}</p>
                                                        <i x-show="hover" wire:click="startEditingTaskDescription('{{ $task->id }}')" class="fi fi-ss-pencil text-xs hover:text-sky-500 dark:text-white"></i>
                                                    </div>
                                                @endif
                                                <div class="flex justify-end">
                                                    <div @click="open = !open" class="relative flex gap-x-2 bg-gray-200 dark:bg-gray-600 px-2.5 py-1.5 rounded-full" x-data="{ open: false }">
                                                        <i wire:click="selectTask('{{ $task->id }}')" class="fi fi-sr-users flex items-center text-gray-700 dark:text-white cursor-pointer"></i>
                                                        <div x-show="open" @click.away="open = false" class="absolute top-10 mt-2 w-60 bg-white dark:bg-gray-700 rounded-md shadow-lg z-10">
                                                            <ul class="py-2 text-sm text-gray-700 dark:text-gray-200">
                                                                <li class="flex justify-center items-center">
                                                                    <p class="text-gray-400 dark:text-gray-300 text-sm">Users - {{ __('backlog.task') }} #{{ $task->id }}</p>
                                                                </li>
                                                                <hr class="h-px my-2 bg-gray-200 border-0 dark:bg-gray-800">
                                                                @foreach ($projectMembers as $member)
                                                                    <li class="px-1">
                                                                        @if ($task->assignees->contains('user_id', $member->user_id))
                                                                            <div wire:click="removeTaskAssignee('{{ $member->id }}')" class="w-full flex justify-between bg-gray-300 dark:bg-gray-700 cursor-pointer p-2">
                                                                                <div class="flex items-center gap-x-2">
                                                                                    <img class="w-6 h-6 rounded-full" src="{{ $member->user->profile_photo_url }}" alt="{{ $member->user->name }}">
                                                                                    <p class="dark:text-white">{{ $member->user->name }}</p>
                                                                                </div>
                                                                                <i class="fi fi-ss-user-check flex items-center dark:text-white"></i>
                                                                            </div>
                                                                        @else
                                                                            <div wire:click="addTaskAssignee('{{ $member->id }}')" class="w-full hover:bg-gray-300 dark:hover:bg-gray-700 cursor-pointer p-2">
                                                                                <div class="flex items-center gap-x-2">
                                                                                    <img class="w-6 h-6 rounded-full" src="{{ $member->user->profile_photo_url }}" alt="{{ $member->user->name }}">
                                                                                    <p class="dark:text-white">{{ $member->user->name }}</p>
                                                                                </div>
                                                                            </div>
                                                                        @endif
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                        @if ($task->assignees->count() > 0)
                                                            <div class="flex items-center gap-x-2 rounded-full">
                                                                @foreach ($task->assignees->slice(0, 2) as $assignee)
                                                                    <img class="w-6 h-6 rounded-full" src="{{ $assignee->user->profile_photo_url }}" alt="{{ $assignee->user->name }}">
                                                                @endforeach
                                                                @if ($task->assignees->count() > 2)
                                                                    <p class="text-sm text-gray-700 dark:text-white">+{{ $task->assignees->count() - 2 }}</p>
                                                                @endif
                                                            </div>
                                                        @else
                                                            <p class="text-sm text-gray-700 dark:text-white">No users assigned</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endif 
                                    @endforeach
                                </div>
                            </div>

                            {{-- Tasks - Doing --}}
                            <div class="h-full col-span-1 bg-gray-100 dark:bg-gray-800 rounded-md"
                                x-data
                                x-init="Sortable.create($refs.doingTasks, {
                                    group: 'tasks',
                                    animation: 150,
                                    onEnd: function (evt) {
                                        @this.call('updateTaskOrder', evt.item.dataset.id, evt.to.dataset.column, evt.newIndex);
                                    }
                                })">
                                <div class="flex justify-between p-2">
                                    <div class="flex items-center gap-x-2 text-sky-500">
                                        <p class="flex items-center justify-center rounded-md text-sm font-bold bg-sky-500 text-white px-1.5 py-0.5">
                                            {{ $selectedCard->tasks->where('status', 'doing')->count() }}
                                        </p>
                                        <h1 class="text-sm font-bold dark:text-white">{{ __('backlog.tasks') }} - {{ __('backlog.doing') }}</h1>
                                    </div>
                                    <div wire:click="createTask('doing')" class="flex items-center mr-1 cursor-pointer">
                                        <i class="fi fi-sr-plus flex items-center text-sm text-black dark:text-white"></i>
                                    </div>
                                </div>
                                <div class="p-2" x-ref="doingTasks" data-column="doing">
                                    @if ($isCreatingTask && $createdTaskColumn == 'doing')
                                        <div class="bg-white p-2 mb-2">
                                            <input type="text" wire:model="taskDescription" wire:keydown.enter="storeTask('doing')" wire:blur="cancelTaskCreation" class="w-full text-sm px-2 py-1 border-0 border-b-2 border-emerald-500 bg-transparent focus:outline-none focus:border-blue-500 text-lg text-gray-600 dark:text-gray-400" placeholder="{{ __('backlog.create_task') }}">
                                        </div>
                                    @endif

                                    @foreach ($selectedCard->tasks->sortBy('task_index') as $task)
                                        @if ($task->status == 'doing')
                                            <div class="bg-white dark:bg-gray-700 p-2 mb-2 rounded-md cursor-move" data-id="{{ $task->id }}" wire:key="task-{{ $task->id }}">
                                                <div class="flex justify-between">
                                                    <p class="flex items-center text-gray-400 text-xs">#{{ $task->id }}</p>
                                                    <div class="relative" x-data="{ open: false, moveTo: false }">
                                                        <i wire:click="selectTask('{{ $task->id }}')" @click="open = !open" class="fi fi-sr-menu-dots-vertical text-xs dark:text-white cursor-pointer"></i>
                                                        <div x-show="open" @click.outside="open = false" class="absolute z-10 top-8 bg-white rounded-lg shadow w-44 dark:bg-gray-800">
                                                            <ul class="py-2 text-sm text-gray-700 dark:text-gray-200">
                                                                <li>
                                                                    <p class="flex justify-center text-gray-400 dark:text-gray-300">{{ __('backlog.actions') }} - {{ __('backlog.task') }} #{{ $task->id }}</p>
                                                                </li>
                                                                <hr class="h-px my-2 bg-gray-200 border-0 dark:bg-gray-600">
                                                                <li>
                                                                    <p @click="open = false" wire:click="assignTaskToMe" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer">{{ __('backlog.assign_me') }}</p>
                                                                </li>
                                                                <hr class="h-px my-2 bg-gray-200 border-0 dark:bg-gray-600">
                                                                <li>
                                                                    <p @click="open = false; moveTo = true" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer">{{ __('backlog.move_to') }}</p>
                                                                </li>
                                                                <li>
                                                                    <p @click="open = false" wire:click="copyTask('{{ $task->id }}')" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer">{{ __('backlog.make_copy') }}</p>
                                                                </li>
                                                                <li>
                                                                    <p @click="open = false" wire:click="convertToCard('{{ $task->id }}')" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer">{{ __('backlog.convert_to_card') }}</p>
                                                                </li>
                                                                <hr class="h-px my-2 bg-gray-200 border-0 dark:bg-gray-600">
                                                                <li>
                                                                    <p @click="open = false" wire:click="deleteTask('{{ $task->id }}')" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer">{{ __('backlog.delete') }}</p>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                        <div x-show="moveTo" @click.outside="moveTo = false" class="absolute z-10 top-8 bg-white dark:bg-gray-800 rounded-lg shadow w-60">
                                                            <ul class="py-2 text-sm text-gray-700 dark:text-gray-200">
                                                                <li>
                                                                    <p class="flex justify-center text-gray-400 dark:text-gray-300">{{ __('backlog.move_to_column') }} - {{ __('backlog.task') }} #{{ $task->id }}</p>
                                                                </li>
                                                                <hr class="h-px my-2 bg-gray-200 border-0 dark:bg-gray-600">
                                                                <li>
                                                                    <p @click="open = false" wire:click="moveTask('{{ $task->id }}', 'todo')" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer">{{ __('backlog.todo') }}</p>
                                                                </li>
                                                                <li>
                                                                    <p @click="open = false" wire:click="moveTask('{{ $task->id }}', 'doing')" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer">{{ __('backlog.doing') }}</p>
                                                                </li>
                                                                <li>
                                                                    <p @click="open = false" wire:click="moveTask('{{ $task->id }}', 'done')" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer">{{ __('backlog.done') }}</p>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                                @if ($isEditingTaskDescription && (int) $editingTaskId === $task->id)
                                                    <textarea wire:model="taskDescription" wire:blur="saveTaskDescription('{{ $task->id }}')" 
                                                        class="w-full border-0 mt-1 px-2 py-1 border-b-2 border-gray-600 dark:border-gray-300 bg-transparent focus:outline-none focus:border-blue-500 text-lg text-gray-600 dark:text-gray-400">
                                                        {{ $task->description }}h
                                                    </textarea>
                                                @else
                                                    <div class="flex justify-between mt-1 cursor-pointer group" x-on:mouseover="hover = true" x-on:mouseout="hover = false" x-data="{ hover: false }">
                                                        <p class="w-full text-sm group-hover:text-sky-500 dark:text-white">{{ $task->description }}</p>
                                                        <i x-show="hover" wire:click="startEditingTaskDescription('{{ $task->id }}')" class="fi fi-bs-pencil text-sm hover:text-sky-500 dark:text-white"></i>
                                                    </div>
                                                @endif
                                                <div class="flex justify-end">
                                                    <div @click="open = !open" class="relative flex gap-x-2 bg-gray-200 dark:bg-gray-600 px-2.5 py-1.5 rounded-full" x-data="{ open: false }">
                                                        <i wire:click="selectTask('{{ $task->id }}')" class="fi fi-sr-users flex items-center text-gray-700 dark:text-white cursor-pointer"></i>
                                                        <div x-show="open" @click.away="open = false" class="absolute top-10 mt-2 w-60 bg-white dark:bg-gray-700 rounded-md shadow-lg z-10">
                                                            <ul class="py-2 text-sm text-gray-700 dark:text-gray-200">
                                                                <li class="flex justify-center items-center">
                                                                    <p class="text-gray-400 dark:text-gray-300 text-sm">Users - {{ __('backlog.task') }} #{{ $task->id }}</p>
                                                                </li>
                                                                <hr class="h-px my-2 bg-gray-200 border-0 dark:bg-gray-800">
                                                                @foreach ($projectMembers as $member)
                                                                    <li class="px-1">
                                                                        @if ($task->assignees->contains('user_id', $member->user_id))
                                                                            <div wire:click="removeTaskAssignee('{{ $member->id }}')" class="w-full flex justify-between bg-gray-300 dark:bg-gray-700 cursor-pointer p-2">
                                                                                <div class="flex items-center gap-x-2">
                                                                                    <img class="w-6 h-6 rounded-full" src="{{ $member->user->profile_photo_url }}" alt="{{ $member->user->name }}">
                                                                                    <p class="dark:text-white">{{ $member->user->name }}</p>
                                                                                </div>
                                                                                <i class="fi fi-ss-user-check flex items-center dark:text-white"></i>
                                                                            </div>
                                                                        @else
                                                                            <div wire:click="addTaskAssignee('{{ $member->id }}')" class="w-full hover:bg-gray-300 dark:hover:bg-gray-700 cursor-pointer p-2">
                                                                                <div class="flex items-center gap-x-2">
                                                                                    <img class="w-6 h-6 rounded-full" src="{{ $member->user->profile_photo_url }}" alt="{{ $member->user->name }}">
                                                                                    <p class="dark:text-white">{{ $member->user->name }}</p>
                                                                                </div>
                                                                            </div>
                                                                        @endif
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                        @if ($task->assignees->count() > 0)
                                                            <div class="flex items-center gap-x-2 rounded-full">
                                                                @foreach ($task->assignees->slice(0, 2) as $assignee)
                                                                    <img class="w-6 h-6 rounded-full" src="{{ $assignee->user->profile_photo_url }}" alt="{{ $assignee->user->name }}">
                                                                @endforeach
                                                                @if ($task->assignees->count() > 2)
                                                                    <p class="text-sm text-gray-700 dark:text-white">+{{ $task->assignees->count() - 2 }}</p>
                                                                @endif
                                                            </div>
                                                        @else
                                                            <p class="text-sm text-gray-700 dark:text-white">No users assigned</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endif 
                                    @endforeach
                                </div>
                            </div>

                            {{-- Tasks - Done --}}
                            <div class="h-full col-span-1 bg-gray-100 dark:bg-gray-800 rounded-md"
                                x-data
                                x-init="Sortable.create($refs.doneTasks, {
                                    group: 'tasks',
                                    animation: 150,
                                    onEnd: function (evt) {
                                        @this.call('updateTaskOrder', evt.item.dataset.id, evt.to.dataset.column, evt.newIndex);
                                    }
                                })">
                                <div class="flex justify-between p-2">
                                    <div class="flex items-center gap-x-2 text-green-500">
                                        <p class="flex items-center justify-center rounded-md text-sm font-bold bg-green-500 text-white px-1.5 py-0.5">
                                            {{ $selectedCard->tasks->where('status', 'done')->count() }}
                                        </p>
                                        <h1 class="text-sm font-bold dark:text-white">{{ __('backlog.tasks') }} - {{ __('backlog.done') }}</h1>
                                    </div>
                                    <div wire:click="createTask('done')" class="flex items-center mr-1 cursor-pointer">
                                        <i class="fi fi-sr-plus flex items-center text-sm text-black dark:text-white"></i>
                                    </div>
                                </div>
                                <div class="p-2" x-ref="doneTasks" data-column="done">
                                    @if ($isCreatingTask && $createdTaskColumn == 'done')
                                        <div class="bg-white p-2 mb-2">
                                            <input type="text" wire:model="taskDescription" wire:keydown.enter="storeTask('done')" wire:blur="cancelTaskCreation" class="w-full text-sm px-2 py-1 border-0 border-b-2 border-emerald-500 bg-transparent focus:outline-none focus:border-blue-500 text-lg text-gray-600 dark:text-gray-400" placeholder="{{ __('backlog.create_task') }}">
                                        </div>
                                    @endif

                                    @foreach ($selectedCard->tasks->sortBy('task_index') as $task)
                                        @if ($task->status == 'done')
                                            <div class="bg-white dark:bg-gray-700 p-2 mb-2 rounded-md cursor-move" data-id="{{ $task->id }}" wire:key="task-{{ $task->id }}">
                                                <div class="flex justify-between">
                                                    <p class="flex items-center text-gray-400 text-xs">#{{ $task->id }}</p>
                                                    <div class="relative" x-data="{ open: false, moveTo: false }">
                                                        <i wire:click="selectTask('{{ $task->id }}')" @click="open = !open" class="fi fi-sr-menu-dots-vertical text-xs dark:text-white cursor-pointer"></i>
                                                        <div x-show="open" @click.outside="open = false" class="absolute z-10 top-8 bg-white rounded-lg shadow w-44 dark:bg-gray-800">
                                                            <ul class="py-2 text-sm text-gray-700 dark:text-gray-200">
                                                                <li>
                                                                    <p class="flex justify-center text-gray-400 dark:text-gray-300">{{ __('backlog.actions') }} - {{ __('backlog.task') }} #{{ $task->id }}</p>
                                                                </li>
                                                                <hr class="h-px my-2 bg-gray-200 border-0 dark:bg-gray-600">
                                                                <li>
                                                                    <p @click="open = false" wire:click="assignTaskToMe" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer">{{ __('backlog.assign_me') }}</p>
                                                                </li>
                                                                <hr class="h-px my-2 bg-gray-200 border-0 dark:bg-gray-600">
                                                                <li>
                                                                    <p @click="open = false; moveTo = true" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer">{{ __('backlog.move_to') }}</p>
                                                                </li>
                                                                <li>
                                                                    <p @click="open = false" wire:click="copyTask('{{ $task->id }}')" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer">{{ __('backlog.make_copy') }}</p>
                                                                </li>
                                                                <li>
                                                                    <p @click="open = false" wire:click="convertToCard('{{ $task->id }}')" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer">{{ __('backlog.convert_to_card') }}</p>
                                                                </li>
                                                                <hr class="h-px my-2 bg-gray-200 border-0 dark:bg-gray-600">
                                                                <li>
                                                                    <p @click="open = false" wire:click="deleteTask('{{ $task->id }}')" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer">{{ __('backlog.delete') }}</p>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                        <div x-show="moveTo" @click.outside="moveTo = false" class="absolute z-10 top-8 -left-10 bg-white dark:bg-gray-800 rounded-lg shadow w-60">
                                                            <ul class="py-2 text-sm text-gray-700 dark:text-gray-200">
                                                                <li>
                                                                    <p class="flex justify-center text-gray-400 dark:text-gray-300">{{ __('backlog.move_to_column') }} - {{ __('backlog.task') }} #{{ $task->id }}</p>
                                                                </li>
                                                                <hr class="h-px my-2 bg-gray-200 border-0 dark:bg-gray-600">
                                                                <li>
                                                                    <p @click="open = false" wire:click="moveTask('{{ $task->id }}', 'todo')" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer">{{ __('backlog.todo') }}</p>
                                                                </li>
                                                                <li>
                                                                    <p @click="open = false" wire:click="moveTask('{{ $task->id }}', 'doing')" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer">{{ __('backlog.doing') }}</p>
                                                                </li>
                                                                <li>
                                                                    <p @click="open = false" wire:click="moveTask('{{ $task->id }}', 'done')" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer">{{ __('backlog.done') }}</p>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                                @if ($isEditingTaskDescription && (int) $editingTaskId === $task->id)
                                                    <textarea wire:model="taskDescription" wire:blur="saveTaskDescription('{{ $task->id }}')" 
                                                        class="w-full border-0 mt-1 px-2 py-1 border-b-2 border-gray-600 dark:border-gray-300 bg-transparent focus:outline-none focus:border-blue-500 text-lg text-gray-600 dark:text-gray-400">
                                                        {{ $task->description }}
                                                    </textarea>
                                                @else
                                                    <div class="flex justify-between mt-1 cursor-pointer group" x-on:mouseover="hover = true" x-on:mouseout="hover = false" x-data="{ hover: false }">
                                                        <p class="w-full text-sm group-hover:text-sky-500 dark:text-white">{{ $task->description }}</p>
                                                        <i x-show="hover" wire:click="startEditingTaskDescription('{{ $task->id }}')" class="fi fi-ss-pencil text-xs hover:text-sky-500 dark:text-white"></i>
                                                    </div>
                                                @endif
                                                <div class="flex justify-end">
                                                    <div @click="open = !open" class="relative flex gap-x-2 bg-gray-200 dark:bg-gray-600 px-2.5 py-1.5 rounded-full" x-data="{ open: false }">
                                                        <i wire:click="selectTask('{{ $task->id }}')" class="fi fi-sr-users flex items-center text-gray-700 dark:text-white cursor-pointer"></i>
                                                        <div x-show="open" @click.away="open = false" class="absolute top-10 mt-2 w-60 bg-white dark:bg-gray-700 rounded-md shadow-lg z-10">
                                                            <ul class="py-2 text-sm text-gray-700 dark:text-gray-200">
                                                                <li class="flex justify-center items-center">
                                                                    <p class="text-gray-400 dark:text-gray-300 text-sm">Users - {{ __('backlog.task') }} #{{ $task->id }}</p>
                                                                </li>
                                                                <hr class="h-px my-2 bg-gray-200 border-0 dark:bg-gray-800">
                                                                @foreach ($projectMembers as $member)
                                                                    <li class="px-1">
                                                                        @if ($task->assignees->contains('user_id', $member->user_id))
                                                                            <div wire:click="removeTaskAssignee('{{ $member->id }}')" class="w-full flex justify-between bg-gray-300 dark:bg-gray-700 cursor-pointer p-2">
                                                                                <div class="flex items-center gap-x-2">
                                                                                    <img class="w-6 h-6 rounded-full" src="{{ $member->user->profile_photo_url }}" alt="{{ $member->user->name }}">
                                                                                    <p class="dark:text-white">{{ $member->user->name }}</p>
                                                                                </div>
                                                                                <i class="fi fi-ss-user-check flex items-center dark:text-white"></i>
                                                                            </div>
                                                                        @else
                                                                            <div wire:click="addTaskAssignee('{{ $member->id }}')" class="w-full hover:bg-gray-300 dark:hover:bg-gray-700 cursor-pointer p-2">
                                                                                <div class="flex items-center gap-x-2">
                                                                                    <img class="w-6 h-6 rounded-full" src="{{ $member->user->profile_photo_url }}" alt="{{ $member->user->name }}">
                                                                                    <p class="dark:text-white">{{ $member->user->name }}</p>
                                                                                </div>
                                                                            </div>
                                                                        @endif
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                        @if ($task->assignees->count() > 0)
                                                            <div class="flex items-center gap-x-2 rounded-full">
                                                                @foreach ($task->assignees->slice(0, 2) as $assignee)
                                                                    <img class="w-6 h-6 rounded-full" src="{{ $assignee->user->profile_photo_url }}" alt="{{ $assignee->user->name }}">
                                                                @endforeach
                                                                @if ($task->assignees->count() > 2)
                                                                    <p class="text-sm text-gray-700 dark:text-white">+{{ $task->assignees->count() - 2 }}</p>
                                                                @endif
                                                            </div>
                                                        @else
                                                            <p class="text-sm text-gray-700 dark:text-white">No users assigned</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endif 
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Delete Task Modal --}}
    <x-dialog-modal wire:model="deleteTaskModal">
        <x-slot name="title">
            {{ __('backlog.dialog_delete_title_task') }}
        </x-slot>

        <x-slot name="content">
            {{ __('backlog.dialog_delete_text_task') }}
        </x-slot>

        <x-slot name="footer">
            <x-danger-button class="ml-2" wire:click="destroyTask" wire:loading.attr="disabled">
                {{ __('backlog.delete') }}
            </x-danger-button>

            <x-secondary-button wire:click="$toggle('deleteTaskModal')" wire:loading.attr="disabled">
                {{ __('backlog.cancel') }}
            </x-secondary-button>            
        </x-slot>
    </x-dialog-modal>
</div>