<div class="flex flex-col h-[90vh]">
    <x-slot name="title">
        {{ __('sprints.sprint') }}
    </x-slot>

    @if ($sprint !== null)
        <div class="w-full bg-white dark:bg-gray-800 shadow-md rounded-lg p-4">
            
            @if ($sprint->status == 'active')
                <div class="w-full flex justify-between text-xs">
                    <div class="flex gap-x-4">
                        <div>
                            @if (round(\Carbon\Carbon::parse($sprint->end_date)->diffInDays(\Carbon\Carbon::now()) * -1) > 0)
                                <p class="font-bold uppercase dark:text-white">{{ __('sprints.days_left') }}</p>
                                <div class="flex gap-x-2">
                                    <i class="fi fi-ss-calendar-clock dark:text-white"></i>
                                    <p class="dark:text-white">{{ round(\Carbon\Carbon::parse($sprint->end_date)->diffInDays(\Carbon\Carbon::now()) * -1) }}</p>
                                </div>
                            @else
                                <p class="font-bold uppercase text-rose-500">{{ __('sprints.days_overdue') }}</p>
                                <div class="flex justify-center gap-x-2">
                                    <i class="fi fi-ss-calendar-clock text-rose-500"></i>
                                    <p class="dark:text-white">{{ round(\Carbon\Carbon::parse($sprint->end_date)->diffInDays(\Carbon\Carbon::now())) }}</p>
                                </div>
                            @endif
                        </div>
                        <div>
                            <p class="font-bold uppercase dark:text-white">{{ __('sprints.dates') }}</p>
                            <div class="flex gap-x-2">
                                <i class="fi fi-ss-calendar dark:text-white"></i>
                                <p class="dark:text-white">
                                    {{ \Carbon\Carbon::parse($sprint->start_date)->format('d/m/Y') }}
                                    -
                                    {{ \Carbon\Carbon::parse($sprint->end_date)->format('d/m/Y') }}
                                </p>
                            </div>
                        </div>
                        <div>
                            <p class="font-bold uppercase dark:text-white">{{ __('sprints.cards_total') }}</p>
                            <div class="flex gap-x-2">
                                <i class="fi fi-ss-list-check dark:text-white"></i>
                                <p class="dark:text-white">{{ $sprint->cards->count() }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-x-2">
                        <p class="text-gray-300">Potential place for card filtering</p>
                    </div>
                </div>
            @else
                            
            @endif
        </div>

        <div class="w-full flex flex-grow gap-x-4 mt-4">
            <div class="w-full grid grid-cols-5 gap-x-4 bg-white dark:bg-gray-700 shadow-md rounded-lg p-4">

                @foreach ($columns as $column)
                    <div class="w-full col-span-1 bg-gray-100 dark:bg-gray-800 rounded-md p-2"
                        x-data
                        x-init="Sortable.create($refs.{{ $column->internal_name }}Tasks, {
                            group: 'cards',
                            animation: 150,
                            onEnd: function (evt) {
                                @this.call('updateCardOrder', evt.item.dataset.id, evt.to.dataset.column, evt.newIndex);
                            }
                        })">
                        <div class="flex justify-between">
                            <div class="w-full flex gap-x-2">
                                <p class="flex items-center justify-center rounded-md text-sm font-bold bg-{{ $column->bg_color }} text-white px-1.5 py-0.5">{{ $sprint->cards->where('status', $column->internal_name)->count() }}</p>
                                <p class="text-{{ $column->text_color }} font-bold">{{ __($column->name) }}</p>
                            </div>
                            <div>
                                <i wire:click="createCard('{{ $column->internal_name }}')" class="fi fi-ss-plus dark:text-white cursor-pointer"></i>
                            </div>
                        </div>
                        {{-- Cards --}}
                        <div class="mt-2" x-ref="{{ $column->internal_name }}Tasks" data-column="{{ $column->internal_name }}">
                            @if ($isCreatingCard && $createdCardColumn === $column->internal_name)
                                <div class="bg-white dark:bg-gray-700 p-2 mb-2">
                                    <input type="text" wire:model="name" wire:keydown.enter="storeCard('{{ $column->internal_name }}')" wire:blur="cancelCardCreation" class="w-full text-sm px-2 py-1 border-0 border-b-2 border-emerald-500 bg-transparent focus:outline-none focus:border-blue-500 text-lg text-gray-600 dark:text-gray-100 dark:placeholder:text-white" placeholder="{{ __('backlog.create_card') }}" autofocus>
                                </div>
                            @endif

                            @foreach ($sprint->cards->where('status', $column->internal_name) as $card)
                                <div class="bg-white dark:bg-gray-700 p-2 mb-2 rounded-md cursor-move" data-id="{{ $card->id }}" wire:key="card-{{ $card->id }}">
                                    <div class="w-full flex justify-between">
                                        <p class="flex items-center text-gray-400 text-xs">#{{ $card->id }}</p>
                                        <div class="relative" x-data="{ menuState: false, moveToState: false }">
                                            <i @click="menuState = !menuState" class="fi fi-sr-menu-dots-vertical text-xs dark:text-white cursor-pointer"></i>
                                            <div x-show="menuState" @click.outside="menuState = false" class="absolute z-10 top-8 -left-20 bg-white dark:bg-gray-800 rounded-lg shadow w-44">
                                                <ul class="py-2 text-sm text-gray-700 dark:text-gray-200">
                                                    <li>
                                                        <p class="flex justify-center text-gray-400 dark:text-gray-300">{{ __('sprints.actions') }} - {{ __('sprints.card') }} #{{ $card->id }}</p>
                                                    </li>
                                                    <hr class="h-px my-2 bg-gray-200 border-0 dark:bg-gray-600">
                                                    <li>
                                                        <p wire:click="assignCardToMe('{{ $card->id }}')" @click="menuState = false" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer">{{ __('sprints.assign_me') }}</p>
                                                    </li>
                                                    <hr class="h-px my-2 bg-gray-200 border-0 dark:bg-gray-600">
                                                    <li>
                                                        <p @click="menuState = false; moveToState = true" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer">{{ __('sprints.move_to') }}</p>
                                                    </li>
                                                    <li>
                                                        <p wire:click="copyCard('{{ $card->id }}')" @click="menuState = false" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer">{{ __('sprints.make_copy') }}</p>
                                                    </li>
                                                    <hr class="h-px my-2 bg-gray-200 border-0 dark:bg-gray-600">
                                                    <li>
                                                        <p wire:click="deleteCard('{{ $card->id }}')" @click="menuState = false" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer">{{ __('sprints.delete') }}</p>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div x-show="moveToState" @click.outside="moveToState = false" class="absolute z-10 top-8 -left-40 bg-white dark:bg-gray-800 rounded-lg shadow w-60">
                                                <ul class="py-2 text-sm text-gray-700 dark:text-gray-200">
                                                    <li>
                                                        <p class="flex justify-center text-gray-400 dark:text-gray-300">{{ __('sprints.move_to') }} - #{{ $card->id }}</p>
                                                    </li>
                                                    <hr class="h-px my-2 bg-gray-200 border-0 dark:bg-gray-600">
                                                    <li class="p-2">
                                                        <p class="dark:text-white">{{ __('sprints.select_destination') }}</p>
                                                        {{-- Projects --}}
                                                        <select wire:model.live="selectedProject" class="w-full mt-2 border-sky-500 bg-gray-100 dark:bg-gray-800 text-sm rounded-lg text-gray-600 dark:text-gray-400">
                                                            @forelse ($projects as $project)
                                                                <option value="{{ $project->uuid }}">{{ $project->name }}</option>
                                                            @empty
                                                                <option value="">{{ __('sprints.select_project') }}</option>
                                                            @endforelse
                                                        </select>
                                                        {{-- Backlog/Sprint --}}
                                                        <select wire:model.live="backlogOrSprint" class="w-full mt-2 border-sky-500 bg-gray-100 dark:bg-gray-800 text-sm rounded-lg text-gray-600 dark:text-gray-400">
                                                            <option value="backlog">{{ __('sprints.backlog') }}</option>
                                                            <option value="sprint">{{ __('sprints.sprint') }}</option>
                                                        </select>
                                                        {{-- Backlog / Sprint name --}}
                                                        <select wire:model.live="backlogOrSprintName" class="w-full mt-2 border-sky-500 bg-gray-100 dark:bg-gray-800 text-sm rounded-lg text-gray-600 dark:text-gray-400">
                                                            @if ($backlogOrSprint === 'backlog')
                                                                @forelse ($backlogs as $backlog)
                                                                    <option value="{{ $backlog->uuid }}">{{ $backlog->name }}</option>
                                                                @empty
                                                                    <option value="">{{ __('sprints.select_backlog') }}</option>
                                                                @endforelse
                                                            @elseif ($backlogOrSprint === 'sprint')
                                                                @forelse ($sprints as $sprint)
                                                                    <option value="{{ $sprint->uuid }}">{{ $sprint->name }}</option>
                                                                @empty
                                                                    <option value="">{{ __('sprints.select_sprint') }}</option>
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
                                                            <button @click="moveTo = false" wire:click="moveCard('{{ $card->id }}')" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700">
                                                                {{ __('backlog.move') }}
                                                            </button>
                                                        </div>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <p wire:click="selectCard('{{ $card->id }}')" class="text-sm text-gray-700 dark:text-gray-200 hover:text-sky-500 cursor-pointer">{{ $card->name }}</p>
                                    <div class="flex gap-x-2">
                                        @if ($card->approval_status !== 'None')
                                            <div wire:click="selectCard('{{ $card->id }}')">
                                                @switch($card->approval_status)
                                                    @case('Approved')
                                                        <div class="px-2 py-1 bg-gray-200 dark:bg-gray-600 rounded-md text-sm cursor-pointer">
                                                            <i class="fi fi-sr-checkbox text-emerald-400" data-tooltip-target="approval-status-{{ $card->id }}"></i>
                                                        </div>

                                                        <div id="approval-status-{{ $card->id }}" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-xs font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                                                            {{ __('sprints.approval_status_approved') }}
                                                            <div class="tooltip-arrow" data-popper-arrow></div>
                                                        </div>
                                                        @break
                                                    @case('Needs work')
                                                        <div class="px-2 py-1 bg-gray-200 dark:bg-gray-600 rounded-md text-sm cursor-pointer">
                                                            <i class="fi fi-sr-pen-square text-amber-500" data-tooltip-target="approval-status-{{ $card->id }}"></i>
                                                        </div>

                                                        <div id="approval-status-{{ $card->id }}" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-xs font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                                                            {{ __('sprints.approval_status_needs_work') }}
                                                            <div class="tooltip-arrow" data-popper-arrow></div>
                                                        </div>
                                                        @break
                                                    @case('Rejected')
                                                        <div class="px-2 py-1 bg-gray-200 dark:bg-gray-600 rounded-md text-sm cursor-pointer">
                                                            <i class="fi fi-sr-square-x text-rose-500" data-tooltip-target="approval-status-{{ $card->id }}"></i>
                                                        </div> 

                                                        <div id="approval-status-{{ $card->id }}" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-xs font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                                                            {{ __('sprints.approval_status_rejected') }}
                                                            <div class="tooltip-arrow" data-popper-arrow></div>
                                                        </div>
                                                        @break
                                                @endswitch
                                            </div>
                                        @endif
                                        @if ($card->tasks->count() > 0)
                                            <div wire:click="selectCard('{{ $card->id }}')" class="cursor-pointer">
                                                @if ($card->tasks->where('status', 'done')->count() === $card->tasks->count())
                                                    <div data-tooltip-target="tasks-{{ $card->id }}" class="bg-emerald-400 flex gap-x-2 py-[0.1875rem] px-2 text-xs rounded-md">
                                                        <i class="fi fi-ss-list-check text-white"></i>
                                                        <p class="text-white">{{ $card->tasks->where('status', 'done')->count() }} / {{ $card->tasks->count() }}</p>
                                                    </div>

                                                    <div id="tasks-{{ $card->id }}" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-xs font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                                                        {{ __('sprints.tooltip-tasks-done') }}
                                                        <div class="tooltip-arrow" data-popper-arrow></div>
                                                    </div>
                                                @elseif ($card->tasks->where('status', 'doing')->count() > 0)
                                                    <div data-tooltip-target="tasks-{{ $card->id }}" class="bg-blue-400 flex gap-x-2 py-[0.1875rem] px-2 text-xs rounded-md">
                                                        <i class="fi fi-ss-list-check text-white"></i>
                                                        <p class="text-white">{{ $card->tasks->where('status', 'done')->count() }} / {{ $card->tasks->count() }}</p>
                                                    </div>

                                                    <div id="tasks-{{ $card->id }}" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-xs font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                                                        {{ __('sprints.tooltip-tasks-doing') }}
                                                        <div class="tooltip-arrow" data-popper-arrow></div>
                                                    </div>
                                                @else
                                                    <div data-tooltip-target="tasks-{{ $card->id }}" class="bg-purple-400 flex gap-x-2 py-[0.1875rem] px-2 text-xs rounded-md">
                                                        <i class="fi fi-ss-list-check text-white"></i>
                                                        <p class="text-white">{{ $card->tasks->where('status', 'done')->count() }} / {{ $card->tasks->count() }}</p>
                                                    </div>

                                                    <div id="tasks-{{ $card->id }}" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-xs font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                                                        {{ __('sprints.tooltip-tasks-none') }}
                                                        <div class="tooltip-arrow" data-popper-arrow></div>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                        @if ($card->description !== null)
                                            <div wire:click="selectCard('{{ $card->id }}')" data-tooltip-target="description-{{ $card->id }}" class="px-2 py-1 bg-gray-200 dark:bg-gray-600 rounded-md text-sm cursor-pointer">
                                                <i class="fi fi-rr-poll-h dark:text-white"></i>
                                            </div>

                                            <div id="description-{{ $card->id }}" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-xs font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                                                {{ __('sprints.tooltip-description') }}
                                                <div class="tooltip-arrow" data-popper-arrow></div>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex justify-end">
                                        <div class="flex items-center gap-x-2 bg-gray-200 dark:bg-gray-700 px-2.5 py-1.5 rounded-full" x-data="{ userMenuState: false }">
                                            <div @click="userMenuState = !userMenuState" class="relative">
                                                <i class="fi fi-sr-users text-sm flex items-center text-gray-700 dark:text-white cursor-pointer"></i>
                                                <div x-show="userMenuState" @click.away="userMenuState = false" class="absolute z-10 mt-2 w-60 top-6 bg-white dark:bg-gray-800 rounded-md shadow-lg">
                                                    <ul class="py-2 text-sm text-gray-700 dark:text-gray-200">
                                                        <li class="flex justify-center items-center">
                                                            <p class="text-gray-400 dark:text-gray-300 text-sm">{{ __('sprints.users') }} - {{ __('sprints.card') }} #{{ $card->id }}</p>
                                                        </li>
                                                        <hr class="h-px my-2 bg-gray-200 border-0 dark:bg-gray-600">
                                                        @forelse ($projectMembers as $member)
                                                            <li class="px-1">
                                                                @if ($card->assignees->contains('user_id', $member->user_id))
                                                                    <div wire:click="removeCardAssignee('{{ $member->id }}')" class="w-full flex justify-between bg-gray-300 dark:bg-gray-700 cursor-pointer p-2">
                                                                        <div class="flex items-center gap-x-2">
                                                                            <img class="w-6 h-6 rounded-full" src="{{ $member->user->profile_photo_url }}" alt="{{ $member->user->name }}">
                                                                            <p class="dark:text-white">{{ $member->user->name }}</p>
                                                                        </div>
                                                                        <i class="fi fi-ss-user-check flex items-center dark:text-white"></i>
                                                                    </div>
                                                                @else
                                                                    <div wire:click="addCardAssignee('{{ $member->id }}')" class="w-full hover:bg-gray-300 dark:hover:bg-gray-700 cursor-pointer p-2">
                                                                        <div class="flex items-center gap-x-2">
                                                                            <img class="w-6 h-6 rounded-full" src="{{ $member->user->profile_photo_url }}" alt="{{ $member->user->name }}">
                                                                            <p class="dark:text-white">{{ $member->user->name }}</p>
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            </li>
                                                        @empty
                                                            <li>
                                                                <p class="px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer">{{ __('sprints.no_users_found') }}</p>
                                                            </li>
                                                        @endforelse
                                                    </ul>
                                                </div>
                                            </div>
                                            @if ($card->assignees->count() > 0)
                                                <div class="flex items-center gap-x-2">
                                                    @foreach ($card->assignees->slice(0, 3) as $assignee)
                                                        <img class="w-5 h-5 rounded-full" src="{{ $assignee->user->profile_photo_url }}" alt="{{ $assignee->user->name }}">
                                                    @endforeach
                                                    @if ($card->assignees->count() > 3)
                                                        <p class="text-xs text-gray-700 dark:text-white">+{{ $card->assignees->count() - 3 }}</p>
                                                    @endif
                                                </div>
                                            @else
                                                <p class="text-xs text-gray-700 dark:text-white">{{ __('sprints.no_users_assigned') }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-md flex justify-center items-center p-4">
            <p class="text-sm dark:text-white">{{ __('sprints.no_sprints_found') }}</p>
        </div>
    @endif

    {{-- Delete Card Modal --}}
    <x-dialog-modal wire:model="deleteCardModal">
        <x-slot name="title">
            {{ __('sprints.dialog_delete_title_card') }}
        </x-slot>

        <x-slot name="content">
            {{ __('sprints.dialog_delete_text_card') }}
        </x-slot>

        <x-slot name="footer">
            <x-danger-button class="ml-2" wire:click="destroyCard" wire:loading.attr="disabled">
                {{ __('sprints.delete') }}
            </x-danger-button>

            <x-secondary-button wire:click="$toggle('deleteCardModal')" wire:loading.attr="disabled">
                {{ __('sprints.cancel') }}
            </x-secondary-button>            
        </x-slot>
    </x-dialog-modal>

    {{-- Delete Task Modal --}}
    <x-dialog-modal wire:model="deleteTaskModal">
        <x-slot name="title">
            {{ __('sprints.dialog_delete_title_task') }}
        </x-slot>

        <x-slot name="content">
            {{ __('sprints.dialog_delete_text_task') }}
        </x-slot>

        <x-slot name="footer">
            <x-danger-button class="ml-2" wire:click="destroyTask" wire:loading.attr="disabled">
                {{ __('sprints.delete') }}
            </x-danger-button>

            <x-secondary-button wire:click="$toggle('deleteTaskModal')" wire:loading.attr="disabled">
                {{ __('sprints.cancel') }}
            </x-secondary-button>            
        </x-slot>
    </x-dialog-modal>

    {{-- Selected Card Modal --}}
    <div class="fixed inset-0 overflow-y-auto w-full h-full z-20 bg-gray-900/60 transform transition-all"
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
                    <div class="flex justify-between p-2">
                        <div class="flex gap-x-4">
                            <div class="flex items-center text-lg">
                                <h1 class="text-gray-400 dark:text-gray-500">#</h1>
                                <h1 class="text-gray-600 dark:text-gray-400">{{ $selectedCard->id }}</h1>
                            </div>
                            <div class="flex items-center">
                                @if ($isEditingCardName)
                                    <input type="text" wire:model="name" wire:blur="saveCardName" 
                                        class="border-0 px-2 py-1 border-b-2 border-gray-600 dark:border-gray-300 bg-transparent focus:outline-none focus:border-blue-500 text-lg text-gray-600 dark:text-gray-400" />
                                @else
                                    <h1 wire:click="startEditingCardName('{{ $selectedCard->id }}')" class="text-lg text-gray-600 dark:text-gray-400">{{ $selectedCard->name }}</h1>
                                @endif
                            </div>
                        </div>
                        <div class="flex gap-x-4 justify-end">
                            <div class="flex items-center relative" x-data="{ menuState: false }">
                                <button @click="menuState = !menuState" class="text-{{ $approvalStatuses[$selectedCard->approval_status]->color_dark }} border border-{{ $approvalStatuses[$selectedCard->approval_status]->color_light }} hover:bg-{{ $approvalStatuses[$selectedCard->approval_status]->color_dark }} focus:ring-2 focus:ring-{{ $approvalStatuses[$selectedCard->approval_status]->color_dark }} hover:text-black font-medium rounded-lg text-sm px-5 py-1 text-center">
                                    {{ $selectedCard->approval_status }}
                                </button>
                                <div x-show="menuState" class="absolute z-10 top-10 mt-2 w-44 bg-white dark:bg-gray-800 rounded-md shadow-lg">
                                    <ul class="py-2 text-sm text-gray-700 dark:text-gray-200">
                                        <li>
                                            <p class="flex justify-center text-gray-400 dark:text-gray-300">{{ __('sprints.change_approval_status') }}</p>
                                        </li>
                                        <hr class="h-px my-2 bg-gray-200 border-0 dark:bg-gray-600">
                                        @foreach ($approvalStatuses as $k => $v)
                                            <li class="px-1">
                                                <p wire:click="changeApprovalStatus('{{ $selectedCard->id }}', '{{ $k }}')" @click="menuState = false" class="px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer">{{ $k }}</p>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                            <div class="flex items-center">
                                @if ($selectedCard && isset($selectedCard->assignees))
                                    <div class="flex items-center gap-x-2 bg-gray-200 dark:bg-gray-700 px-2.5 py-1.5 rounded-full" x-data="{ userMenuState: false }">
                                        <div @click="userMenuState = !userMenuState" class="relative">
                                            <i class="fi fi-sr-users flex items-center text-gray-700 dark:text-white cursor-pointer"></i>
                                            <div x-show="userMenuState" @click.away="userMenuState = false" class="absolute z-10 mt-2 w-60 top-6 -left-20 bg-white dark:bg-gray-800 rounded-md shadow-lg">
                                                <ul class="py-2 text-sm text-gray-700 dark:text-gray-200">
                                                    <li class="flex justify-center items-center">
                                                        <p class="text-gray-400 dark:text-gray-300 text-sm">{{ __('sprints.users') }} - {{ __('sprints.card') }} #{{ $selectedCard->id }}</p>
                                                    </li>
                                                    <hr class="h-px my-2 bg-gray-200 border-0 dark:bg-gray-600">
                                                    @forelse ($projectMembers as $member)
                                                        <li class="px-1">
                                                            @if ($selectedCard->assignees->contains('user_id', $member->user_id))
                                                                <div wire:click="removeCardAssignee('{{ $member->id }}')" class="w-full flex justify-between bg-gray-300 dark:bg-gray-700 cursor-pointer p-2">
                                                                    <div class="flex items-center gap-x-2">
                                                                        <img class="w-6 h-6 rounded-full" src="{{ $member->user->profile_photo_url }}" alt="{{ $member->user->name }}">
                                                                        <p class="dark:text-white">{{ $member->user->name }}</p>
                                                                    </div>
                                                                    <i class="fi fi-ss-user-check flex items-center dark:text-white"></i>
                                                                </div>
                                                            @else
                                                                <div wire:click="addCardAssignee('{{ $member->id }}')" class="w-full hover:bg-gray-300 dark:hover:bg-gray-700 cursor-pointer p-2">
                                                                    <div class="flex items-center gap-x-2">
                                                                        <img class="w-6 h-6 rounded-full" src="{{ $member->user->profile_photo_url }}" alt="{{ $member->user->name }}">
                                                                        <p class="dark:text-white">{{ $member->user->name }}</p>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </li>
                                                    @empty
                                                        <li>
                                                            <p class="px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer">{{ __('sprints.no_users_found') }}</p>
                                                        </li>
                                                    @endforelse
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
                                            <p class="text-sm text-gray-700 dark:text-white">{{ __('sprints.no_users_assigned') }}</p>
                                        @endif
                                    </div>
                                @endif
                            </div>
                            <div class="relative flex items-center" x-data="{ menuState: false, moveToState: false }">
                                <i @click="menuState = !menuState" class="fi fi-sr-menu-dots-vertical dark:text-white cursor-pointer"></i>
                                <div x-show="menuState" @click.outside="menuState = false" class="absolute z-10 top-10 bg-white dark:bg-gray-800 rounded-lg shadow w-44">
                                    <ul class="py-2 text-sm text-gray-700 dark:text-gray-200">
                                        <li>
                                            <p class="flex justify-center text-gray-400 dark:text-gray-300">{{ __('sprints.actions') }} - {{ __('sprints.card') }} #{{ $selectedCard->id }}</p>
                                        </li>
                                        <hr class="h-px my-2 bg-gray-200 border-0 dark:bg-gray-600">
                                        <li>
                                            <p wire:click="assignCardToMe('{{ $selectedCard->id }}')" @click="menuState = false" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer">{{ __('sprints.assign_me') }}</p>
                                        </li>
                                        <hr class="h-px my-2 bg-gray-200 border-0 dark:bg-gray-600">
                                        <li>
                                            <p @click="menuState = false; moveToState = true" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer">{{ __('sprints.move_to') }}</p>
                                        </li>
                                        <li>
                                            <p wire:click="copyCard('{{ $selectedCard->id }}')" @click="menuState = false" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer">{{ __('sprints.make_copy') }}</p>
                                        </li>
                                        <hr class="h-px my-2 bg-gray-200 border-0 dark:bg-gray-600">
                                        <li>
                                            <p wire:click="deleteCard('{{ $selectedCard->id }}')" @click="menuState = false" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer">{{ __('sprints.delete') }}</p>
                                        </li>
                                    </ul>
                                </div>
                                <div x-show="moveToState" @click.outside="moveToState = false" class="absolute z-10 top-10 -left-40 bg-white dark:bg-gray-800 rounded-lg shadow w-60">
                                    <ul class="py-2 text-sm text-gray-700 dark:text-gray-200">
                                        <li>
                                            <p class="flex justify-center text-gray-400 dark:text-gray-300">{{ __('sprints.move_to') }} - {{ __('sprints.card') }} #{{ $selectedCard->id }}</p>
                                        </li>
                                        <hr class="h-px my-2 bg-gray-200 border-0 dark:bg-gray-600">
                                        <li class="p-2">
                                            <p class="dark:text-white">{{ __('sprints.select_destination') }}</p>
                                            {{-- Projects --}}
                                            <select wire:model.live="selectedProject" class="w-full mt-2 border-sky-500 bg-gray-100 dark:bg-gray-800 text-sm rounded-lg text-gray-600 dark:text-gray-400">
                                                @forelse ($projects as $project)
                                                    <option value="{{ $project->uuid }}">{{ $project->name }}</option>
                                                @empty
                                                    <option value="">{{ __('sprints.select_project') }}</option>
                                                @endforelse
                                            </select>
                                            {{-- Backlog/Sprint --}}
                                            <select wire:model.live="backlogOrSprint" class="w-full mt-2 border-sky-500 bg-gray-100 dark:bg-gray-800 text-sm rounded-lg text-gray-600 dark:text-gray-400">
                                                <option value="backlog">{{ __('sprints.backlog') }}</option>
                                                <option value="sprint">{{ __('sprints.sprint') }}</option>
                                            </select>
                                            {{-- Backlog / Sprint name --}}
                                            <select wire:model.live="backlogOrSprintName" class="w-full mt-2 border-sky-500 bg-gray-100 dark:bg-gray-800 text-sm rounded-lg text-gray-600 dark:text-gray-400">
                                                @if ($backlogOrSprint === 'backlog')
                                                    @forelse ($backlogs as $backlog)
                                                        <option value="{{ $backlog->uuid }}">{{ $backlog->name }}</option>
                                                    @empty
                                                        <option value="">{{ __('sprints.select_backlog') }}</option>
                                                    @endforelse
                                                @elseif ($backlogOrSprint === 'sprint')
                                                    @forelse ($sprints as $sprint)
                                                        <option value="{{ $sprint->uuid }}">{{ $sprint->name }}</option>
                                                    @empty
                                                        <option value="">{{ __('sprints.select_sprint') }}</option>
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
                                                <button @click="moveTo = false" wire:click="moveCard('{{ $card->id }}')" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700">
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
                                @if ($selectedCard->description !== null)
                                    <p class="text-sm text-gray-700 dark:text-gray-200 cursor-pointer">{{ $selectedCard->description }}</p>
                                @else
                                    <p class="text-sm text-gray-400 dark:text-gray-200 cursor-pointer">{{ __('sprints.no_description') }}</p>
                                @endif
                                <i x-show="hover" wire:click="startEditingCardDescription('{{ $selectedCard->id }}')" class="fi fi-bs-pencil text-sm hover:text-sky-500 dark:text-white cursor-pointer"></i>
                            </div>
                            
                        @endif
                        <hr class="h-px my-8 bg-gray-200 border-0 dark:bg-gray-800">
                        <div class="h-full grid grid-cols-3 gap-x-4">
                            {{-- Tasks - Todo --}}
                            <div class="h-full col-span-1 bg-gray-100 dark:bg-gray-800"
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
                                        <h1 class="text-sm font-bold dark:text-white">{{ __('sprints.tasks') }} - {{ __('backlog.todo') }}</h1>
                                    </div>
                                    <div wire:click="createTask('todo')" class="flex items-center mr-1 cursor-pointer">
                                        <i class="fi fi-sr-plus flex items-center text-sm text-black dark:text-white"></i>
                                    </div>
                                </div>
                                <div class="p-2" x-ref="todoTasks" data-column="todo">
                                    @if ($isCreatingTask && $createdTaskColumn === 'todo')
                                        <div class="bg-white dark:bg-gray-700 p-2 mb-2">
                                            <input type="text" wire:model="taskDescription" wire:keydown.enter="storeTask('todo')" wire:blur="cancelTaskCreation" class="w-full text-sm px-2 py-1 border-0 border-b-2 border-emerald-500 bg-transparent focus:outline-none focus:border-blue-500 text-lg text-gray-600 dark:text-white" placeholder="{{ __('backlog.create_task') }}">
                                        </div>
                                    @endif

                                    @foreach ($selectedCard->tasks->sortBy('task_index') as $task)
                                        @if ($task->status === 'todo')
                                            <div class="bg-white dark:bg-gray-700 p-2 mb-2 rounded-md cursor-move" data-id="{{ $task->id }}" wire:key="task-{{ $task->id }}">
                                                <div class="flex justify-between">
                                                    <p class="flex items-center text-gray-400 text-xs">#{{ $task->id }}</p>
                                                    <div class="relative" x-data="{ menuState: false, moveToState: false }">
                                                        <i @click="menuState = !menuState" class="fi fi-sr-menu-dots-vertical text-xs dark:text-white cursor-pointer"></i>
                                                        <div x-show="menuState" @click.outside="menuState = false" class="absolute z-10 top-8 bg-white dark:bg-gray-800 rounded-lg shadow w-44">
                                                            <ul class="py-2 text-sm text-gray-700 dark:text-gray-200">
                                                                <li>
                                                                    <p class="flex justify-center text-gray-400 dark:text-gray-300">{{ __('sprints.actions') }} - {{ __('sprints.task') }} #{{ $task->id }}</p>
                                                                </li>
                                                                <hr class="h-px my-2 bg-gray-200 border-0 dark:bg-gray-600">
                                                                <li>
                                                                    <p wire:click="assignTaskToMe('{{ $task->id }}')" @click="menuState = false" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 dark:hover:text-white cursor-pointer">{{ __('sprints.assign_me') }}</p>
                                                                </li>
                                                                <hr class="h-px my-2 bg-gray-200 border-0 dark:bg-gray-600">
                                                                <li>
                                                                    <p @click="menuState = false; moveToState = true" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 dark:hover:text-white cursor-pointer">{{ __('sprints.move_to') }}</p>
                                                                </li>
                                                                <li>
                                                                    <p wire:click="copyTask('{{ $task->id }}')" @click="menuState = false" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 dark:hover:text-white cursor-pointer">{{ __('sprints.make_copy') }}</p>
                                                                </li>
                                                                <li>
                                                                    <p @click="open = false" wire:click="convertToCard('{{ $task->id }}')" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer">{{ __('sprints.convert_to_card') }}</p>
                                                                </li>
                                                                <hr class="h-px my-2 bg-gray-200 border-0 dark:bg-gray-600">
                                                                <li>
                                                                    <p @click="open = false" wire:click="deleteTask('{{ $task->id }}')" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer">{{ __('sprints.delete') }}</p>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                        <div x-show="moveToState" @click.outside="moveToState = false" class="absolute z-10 top-8 bg-white dark:bg-gray-800 rounded-lg shadow w-60">
                                                            <ul class="py-2 text-sm text-gray-700 dark:text-gray-200">
                                                                <li>
                                                                    <p class="flex justify-center text-gray-400 dark:text-gray-300">{{ __('sprints.move_to_column') }} - {{ __('sprints.task') }} #{{ $task->id }}</p>
                                                                </li>
                                                                <hr class="h-px my-2 bg-gray-200 border-0 dark:bg-gray-600">
                                                                <li>
                                                                    <p @click="open = false" wire:click="moveTask('{{ $task->id }}', 'todo')" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer">{{ __('sprints.todo') }}</p>
                                                                </li>
                                                                <li>
                                                                    <p @click="open = false" wire:click="moveTask('{{ $task->id }}', 'doing')" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer">{{ __('sprints.doing') }}</p>
                                                                </li>
                                                                <li>
                                                                    <p @click="open = false" wire:click="moveTask('{{ $task->id }}', 'done')" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer">{{ __('sprints.done') }}</p>
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
                                                    <div @click="menuState = !menuState" class="relative flex gapx--2 bg-gray-200 dark:bg-gray-600 mt-2 px-2.5 py-1.5 rounded-full" x-data="{ menuState: false }">
                                                        <i class="fi fi-sr-users text-gray-700 dark:text-white cursor-pointer"></i>
                                                        <div x-show="menuState" @click.outside="menuState = false" class="absolute z-10 top-10 mt-2 w-60 bg-white dark:bg-gray-700 rounded-md shadow">
                                                            <ul class="py-2 text-sm text-gray-700 dark:text-gray-200">
                                                                <li class="flex justify-center items-center">
                                                                    <p class="text-gray-400 dark:text-gray-300 text-sm">{{ __('sprints.users') }} - {{ __('sprints.task') }} #{{ $task->id }}</p>
                                                                </li>
                                                                <hr class="h-px my-2 bg-gray-200 border-0 dark:bg-gray-600">
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
                                                                            <div wire:click="addTaskAssignee({{ $member->id }})" class="w-full hover:bg-gray-300 dark:hover:bg-gray-700 cursor-pointer p-2">
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
                                                            <p class="text-sm text-gray-700 dark:text-white">{{ __('sprints.no_users_assigned') }}</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>

                            {{-- Tasks - Doing --}}
                            <div class="h-full col-span-1 bg-gray-100 dark:bg-gray-800"
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
                                        <h1 class="text-sm font-bold dark:text-white">{{ __('sprints.tasks') }} - {{ __('backlog.doing') }}</h1>
                                    </div>
                                    <div wire:click="createTask('doing')" class="flex items-center mr-1 cursor-pointer">
                                        <i class="fi fi-sr-plus flex items-center text-sm text-black dark:text-white"></i>
                                    </div>
                                </div>
                                <div class="p-2" x-ref="doingTasks" data-column="doing">
                                    @if ($isCreatingTask && $createdTaskColumn === 'doing')
                                        <div class="bg-white dark:bg-gray-700 p-2 mb-2">
                                            <input type="text" wire:model="taskDescription" wire:keydown.enter="storeTask('doing')" wire:blur="cancelTaskCreation" class="w-full text-sm px-2 py-1 border-0 border-b-2 border-emerald-500 bg-transparent focus:outline-none focus:border-blue-500 text-lg text-gray-600 dark:text-white" placeholder="{{ __('backlog.create_task') }}">
                                        </div>
                                    @endif

                                    @foreach ($selectedCard->tasks->sortBy('task_index') as $task)
                                        @if ($task->status === 'doing')
                                            <div class="bg-white dark:bg-gray-700 p-2 mb-2 rounded-md cursor-move" data-id="{{ $task->id }}" wire:key="task-{{ $task->id }}">
                                                <div class="flex justify-between">
                                                    <p class="flex items-center text-gray-400 text-xs">#{{ $task->id }}</p>
                                                    <div class="relative" x-data="{ menuState: false, moveToState: false }">
                                                        <i @click="menuState = !menuState" class="fi fi-sr-menu-dots-vertical text-xs dark:text-white cursor-pointer"></i>
                                                        <div x-show="menuState" @click.outside="menuState = false" class="absolute z-10 top-8 bg-white dark:bg-gray-800 rounded-lg shadow w-44">
                                                            <ul class="py-2 text-sm text-gray-700 dark:text-gray-200">
                                                                <li>
                                                                    <p class="flex justify-center text-gray-400 dark:text-gray-300">{{ __('sprints.actions') }} - {{ __('sprints.task') }} #{{ $task->id }}</p>
                                                                </li>
                                                                <hr class="h-px my-2 bg-gray-200 border-0 dark:bg-gray-600">
                                                                <li>
                                                                    <p wire:click="assignTaskToMe('{{ $task->id }}')" @click="menuState = false" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 dark:hover:text-white cursor-pointer">{{ __('sprints.assign_me') }}</p>
                                                                </li>
                                                                <hr class="h-px my-2 bg-gray-200 border-0 dark:bg-gray-600">
                                                                <li>
                                                                    <p @click="menuState = false; moveToState = true" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 dark:hover:text-white cursor-pointer">{{ __('sprints.move_to') }}</p>
                                                                </li>
                                                                <li>
                                                                    <p wire:click="copyTask('{{ $task->id }}')" @click="menuState = false" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 dark:hover:text-white cursor-pointer">{{ __('sprints.make_copy') }}</p>
                                                                </li>
                                                                <li>
                                                                    <p @click="open = false" wire:click="convertToCard('{{ $task->id }}')" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer">{{ __('sprints.convert_to_card') }}</p>
                                                                </li>
                                                                <hr class="h-px my-2 bg-gray-200 border-0 dark:bg-gray-600">
                                                                <li>
                                                                    <p @click="open = false" wire:click="deleteTask('{{ $task->id }}')" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer">{{ __('sprints.delete') }}</p>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                        <div x-show="moveToState" @click.outside="moveToState = false" class="absolute z-10 top-8 bg-white dark:bg-gray-800 rounded-lg shadow w-60">
                                                            <ul class="py-2 text-sm text-gray-700 dark:text-gray-200">
                                                                <li>
                                                                    <p class="flex justify-center text-gray-400 dark:text-gray-300">{{ __('sprints.move_to_column') }} - {{ __('sprints.task') }} #{{ $task->id }}</p>
                                                                </li>
                                                                <hr class="h-px my-2 bg-gray-200 border-0 dark:bg-gray-600">
                                                                <li>
                                                                    <p @click="open = false" wire:click="moveTask('{{ $task->id }}', 'todo')" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer">{{ __('sprints.todo') }}</p>
                                                                </li>
                                                                <li>
                                                                    <p @click="open = false" wire:click="moveTask('{{ $task->id }}', 'doing')" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer">{{ __('sprints.todo') }}</p>
                                                                </li>
                                                                <li>
                                                                    <p @click="open = false" wire:click="moveTask('{{ $task->id }}', 'done')" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer">{{ __('sprints.todo') }}</p>
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
                                                    <div @click="menuState = !menuState" class="relative flex gap-x-2 bg-gray-200 dark:bg-gray-600 mt-2 px-2.5 py-1.5 rounded-full" x-data="{ menuState: false }">
                                                        <i class="fi fi-sr-users text-gray-700 dark:text-white cursor-pointer"></i>
                                                        <div x-show="menuState" @click.outside="menuState = false" class="absolute z-10 top-10 mt-2 w-60 bg-white dark:bg-gray-700 rounded-md shadow">
                                                            <ul class="py-2 text-sm text-gray-700 dark:text-gray-200">
                                                                <li class="flex justify-center items-center">
                                                                    <p class="text-gray-400 dark:text-gray-300 text-sm">{{ __('sprints.users') }} - {{ __('sprints.task') }} #{{ $task->id }}</p>
                                                                </li>
                                                                <hr class="h-px my-2 bg-gray-200 border-0 dark:bg-gray-600">
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
                                                                            <div wire:click="addTaskAssignee({{ $member->id }})" class="w-full hover:bg-gray-300 dark:hover:bg-gray-700 cursor-pointer p-2">
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
                                                            <p class="text-sm text-gray-700 dark:text-white">{{ __('sprints.no_users_assigned') }}</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>

                            {{-- Tasks - Done --}}
                            <div class="h-full col-span-1 bg-gray-100 dark:bg-gray-800"
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
                                        <h1 class="text-sm font-bold dark:text-white">{{ __('sprints.tasks') }} - {{ __('backlog.done') }}</h1>
                                    </div>
                                    <div wire:click="createTask('done')" class="flex items-center mr-1 cursor-pointer">
                                        <i class="fi fi-sr-plus flex items-center text-sm text-black dark:text-white"></i>
                                    </div>
                                </div>
                                <div class="p-2" x-ref="doneTasks" data-column="done">
                                    @if ($isCreatingTask && $createdTaskColumn === 'done')
                                        <div class="bg-white dark:bg-gray-700 p-2 mb-2">
                                            <input type="text" wire:model="taskDescription" wire:keydown.enter="storeTask('done')" wire:blur="cancelTaskCreation" class="w-full text-sm px-2 py-1 border-0 border-b-2 border-emerald-500 bg-transparent focus:outline-none focus:border-blue-500 text-lg text-gray-600 dark:text-white" placeholder="{{ __('backlog.create_task') }}">
                                        </div>
                                    @endif

                                    @foreach ($selectedCard->tasks->sortBy('task_index') as $task)
                                        @if ($task->status === 'done')
                                            <div class="bg-white dark:bg-gray-700 p-2 mb-2 rounded-md cursor-move" data-id="{{ $task->id }}" wire:key="task-{{ $task->id }}">
                                                <div class="flex justify-between">
                                                    <p class="flex items-center text-gray-400 text-xs">#{{ $task->id }}</p>
                                                    <div class="relative" x-data="{ menuState: false, moveToState: false }">
                                                        <i @click="menuState = !menuState" class="fi fi-sr-menu-dots-vertical text-xs dark:text-white cursor-pointer"></i>
                                                        <div x-show="menuState" @click.outside="menuState = false" class="absolute z-10 top-8 bg-white dark:bg-gray-800 rounded-lg shadow w-44">
                                                            <ul class="py-2 text-sm text-gray-700 dark:text-gray-200">
                                                                <li>
                                                                    <p class="flex justify-center text-gray-400 dark:text-gray-300">{{ __('sprints.actions') }} - {{ __('sprints.task') }} #{{ $task->id }}</p>
                                                                </li>
                                                                <hr class="h-px my-2 bg-gray-200 border-0 dark:bg-gray-600">
                                                                <li>
                                                                    <p wire:click="assignTaskToMe('{{ $task->id }}')" @click="menuState = false" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 dark:hover:text-white cursor-pointer">{{ __('sprints.assign_me') }}</p>
                                                                </li>
                                                                <hr class="h-px my-2 bg-gray-200 border-0 dark:bg-gray-600">
                                                                <li>
                                                                    <p @click="menuState = false; moveToState = true" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 dark:hover:text-white cursor-pointer">{{ __('sprints.move_to') }}</p>
                                                                </li>
                                                                <li>
                                                                    <p wire:click="copyTask('{{ $task->id }}')" @click="menuState = false" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 dark:hover:text-white cursor-pointer">{{ __('sprints.make_copy') }}</p>
                                                                </li>
                                                                <li>
                                                                    <p @click="open = false" wire:click="convertToCard('{{ $task->id }}')" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer">{{ __('sprints.convert_to_card') }}</p>
                                                                </li>
                                                                <hr class="h-px my-2 bg-gray-200 border-0 dark:bg-gray-600">
                                                                <li>
                                                                    <p @click="open = false" wire:click="deleteTask('{{ $task->id }}')" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer">{{ __('sprints.delete') }}</p>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                        <div x-show="moveToState" @click.outside="moveToState = false" class="absolute z-10 top-8 bg-white dark:bg-gray-800 rounded-lg shadow w-60">
                                                            <ul class="py-2 text-sm text-gray-700 dark:text-gray-200">
                                                                <li>
                                                                    <p class="flex justify-center text-gray-400 dark:text-gray-300">{{ __('sprints.move_to_column') }} - {{ __('sprints.task') }} #{{ $task->id }}</p>
                                                                </li>
                                                                <hr class="h-px my-2 bg-gray-200 border-0 dark:bg-gray-600">
                                                                <li>
                                                                    <p @click="open = false" wire:click="moveTask('{{ $task->id }}', 'todo')" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer">{{ __('sprints.todo') }}</p>
                                                                </li>
                                                                <li>
                                                                    <p @click="open = false" wire:click="moveTask('{{ $task->id }}', 'doing')" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer">{{ __('sprints.todo') }}</p>
                                                                </li>
                                                                <li>
                                                                    <p @click="open = false" wire:click="moveTask('{{ $task->id }}', 'done')" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer">{{ __('sprints.todo') }}</p>
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
                                                    <div @click="menuState = !menuState" class="relative flex gap-x-2 bg-gray-200 dark:bg-gray-600 mt-2 px-2.5 py-1.5 rounded-full" x-data="{ menuState: false }">
                                                        <i class="fi fi-sr-users text-gray-700 dark:text-white cursor-pointer"></i>
                                                        <div x-show="menuState" @click.outside="menuState = false" class="absolute z-10 top-10 mt-2 w-60 bg-white dark:bg-gray-700 rounded-md shadow">
                                                            <ul class="py-2 text-sm text-gray-700 dark:text-gray-200">
                                                                <li class="flex justify-center items-center">
                                                                    <p class="text-gray-400 dark:text-gray-300 text-sm">{{ __('sprints.users') }} - {{ __('sprints.task') }} #{{ $task->id }}</p>
                                                                </li>
                                                                <hr class="h-px my-2 bg-gray-200 border-0 dark:bg-gray-600">
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
                                                                            <div wire:click="addTaskAssignee({{ $member->id }})" class="w-full hover:bg-gray-300 dark:hover:bg-gray-700 cursor-pointer p-2">
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
                                                            <p class="text-sm text-gray-700 dark:text-white">{{ __('sprints.no_users_assigned') }}</p>
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
</div>
