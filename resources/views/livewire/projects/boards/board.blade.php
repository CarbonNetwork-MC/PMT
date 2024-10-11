<div class="flex flex-col h-[90vh]">
    <x-slot name="title">
        {{ __('sprints.sprint') }}
    </x-slot>

    <div class="w-full bg-white dark:bg-gray-800 shadow-md rounded-lg p-4">
        @if (isset($sprint))
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
                        {{-- New Card Button --}}
                        <p class="text-gray-300">Potential place for card filtering</p>
                    </div>
                </div>
            @else
                            
            @endif
        @else
            <div class="flex justify-center items-center">
                <p class="text-sm dark:text-white">{{ __('sprints.no_sprints_found') }}</p>
            </div>
        @endif
    </div>

    {{-- Approval Status Icons: fi-sr-checkbox, fi-sr-pen-square, fi-sr-square-x --}}

    <div class="w-full flex flex-grow gap-x-4 mt-4">
        <div class="w-full grid grid-cols-5 gap-x-4 bg-white dark:bg-gray-800 shadow-md rounded-lg p-4">

            @foreach ($columns as $column)
                <div class="w-full col-span-1 bg-gray-100 dark:bg-gray-700 rounded-md p-2"
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
                            <div class="bg-white p-2 mb-2">
                                <input type="text" wire:model="name" wire:keydown.enter="storeCard('{{ $column->internal_name }}')" wire:blur="cancelCardCreation" class="w-full text-sm px-2 py-1 border-0 border-b-2 border-emerald-500 bg-transparent focus:outline-none focus:border-blue-500 text-lg text-gray-600 dark:text-gray-400" placeholder="{{ __('backlog.create_card') }}">
                            </div>
                        @endif

                        @foreach ($sprint->cards->where('status', $column->internal_name) as $card)
                            <div class="bg-white dark:bg-gray-700 p-2 mb-2 rounded-md cursor-move" data-id="{{ $card->id }}" wire:key="card-{{ $card->id }}">
                                <div class="w-full flex justify-between">
                                    <p class="flex items-center text-gray-400 text-xs">#{{ $card->id }}</p>
                                    <div class="relative" x-data="{ menuState: false, moveToState: false }">
                                        <i @click="menuState = !menuState" class="fi fi-sr-menu-dots-vertical text-xs dark:text-white cursor-pointer"></i>
                                        <div x-show="menuState" @click.outside="menuState = false" class="absolute z-10 top-8 -left-40 bg-white dark:bg-gray-800 rounded-lg shadow w-44">
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
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach


            {{-- <div class="oldCode">
                <div class="w-full col-span-1 bg-gray-100 dark:bg-gray-700 rounded-md p-2"
                    x-data
                    x-init="Sortable.create($refs.todoTasks, {
                        group: 'cards',
                        animation: 150,
                        onEnd: function (evt) {
                            @this.call('updateCardOrder', evt.item.dataset.id, evt.to.dataset.column, evt.newIndex);
                        }
                    })">
                    <div class="flex justify-between">
                        <div class="w-full flex gap-x-2">
                            <p class="flex items-center justify-center rounded-md text-sm font-bold bg-rose-500 text-white px-1.5 py-0.5">{{ $sprint->cards->where('status', 'todo')->count() }}</p>
                            <p class="text-rose-500 font-bold">{{ __('sprints.todo') }}</p>
                        </div>
                        <div>
                            <i wire:click="createCard('todo')" class="fi fi-ss-plus dark:text-white cursor-pointer"></i>
                        </div>
                    </div>
                    <div class="mt-2" x-ref="todoTasks" data-column="todo">
                        @foreach ($sprint->cards->where('status', 'todo') as $card)
                            <div class="bg-white dark:bg-gray-700 p-2 mb-2 rounded-md cursor-move" data-id="{{ $card->id }}" wire:key="card-{{ $card->id }}">
                                <div class="w-full flex justify-between">
                                    <p class="flex items-center text-gray-400 text-xs">#{{ $card->id }}</p>
                                    <div class="relative" x-data="{ menuState: false, moveToState: false }">
                                        <i @click="menuState = !menuState" class="fi fi-sr-menu-dots-vertical text-xs dark:text-white cursor-pointer"></i>
                                        <div x-show="menuState" @click.outside="menuState = false" class="absolute z-10 top-8 bg-white dark:bg-gray-800 rounded-lg shadow w-44">
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
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="w-full col-span-1 bg-gray-100 dark:bg-gray-700 rounded-md p-2"
                    x-data
                    x-init="Sortable.create($refs.doingTasks, {
                        group: 'cards',
                        animation: 150,
                        onEnd: function (evt) {
                            @this.call('updateCardOrder', evt.item.dataset.id, evt.to.dataset.column, evt.newIndex);
                        }
                    })">
                    <div class="flex justify-between">
                        <div class="w-full flex gap-x-2">
                            <p class="flex items-center justify-center rounded-md text-sm font-bold bg-amber-500 text-white px-1.5 py-0.5">{{ $sprint->cards->where('status', 'doing')->count() }}</p>
                            <p class="text-amber-500 font-bold">{{ __('sprints.doing') }}</p>
                        </div>
                        <div>
                            <i wire:click="createCard('doing')" class="fi fi-ss-plus dark:text-white cursor-pointer"></i>
                        </div>
                    </div>
                    <div class="mt-2" x-ref="doingTasks" data-column="doing">
                        @foreach ($sprint->cards->where('status', 'doing') as $card)
                            <div class="bg-white dark:bg-gray-700 p-2 mb-2 rounded-md cursor-move" data-id="{{ $card->id }}" wire:key="card-{{ $card->id }}">
                                <div class="w-full flex justify-between">
                                    <p class="flex items-center text-gray-400 text-xs">#{{ $card->id }}</p>
                                    <div class="relative" x-data="{ menuState: false, moveToState: false }">
                                        <i @click="menuState = !menuState" class="fi fi-sr-menu-dots-vertical text-xs dark:text-white cursor-pointer"></i>
                                        <div x-show="menuState" @click.outside="menuState = false" class="absolute z-10 top-8 bg-white dark:bg-gray-800 rounded-lg shadow w-44">
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
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="w-full col-span-1 bg-gray-100 dark:bg-gray-700 rounded-md p-2"
                    x-data
                    x-init="Sortable.create($refs.testingTasks, {
                        group: 'cards',
                        animation: 150,
                        onEnd: function (evt) {
                            @this.call('updateCardOrder', evt.item.dataset.id, evt.to.dataset.column, evt.newIndex);
                        }
                    })">
                    <div class="flex justify-between">
                        <div class="w-full flex gap-x-2">
                            <p class="flex items-center justify-center rounded-md text-sm font-bold bg-yellow-300 text-white px-1.5 py-0.5">{{ $sprint->cards->where('status', 'testing')->count() }}</p>
                            <p class="text-yellow-400 font-bold">{{ __('sprints.testing') }}</p>
                        </div>
                        <div>
                            <i wire:click="createCard('testing')" class="fi fi-ss-plus dark:text-white cursor-pointer"></i>
                        </div>
                    </div>
                    <div class="mt-2" x-ref="testingTasks" data-column="testing">

                    </div>
                </div>
                <div class="w-full col-span-1 bg-gray-100 dark:bg-gray-700 rounded-md p-2"
                    x-data
                    x-init="Sortable.create($refs.doneTasks, {
                        group: 'cards',
                        animation: 150,
                        onEnd: function (evt) {
                            @this.call('updateCardOrder', evt.item.dataset.id, evt.to.dataset.column, evt.newIndex);
                        }
                    })">
                    <div class="flex justify-between">
                        <div class="w-full flex gap-x-2">
                            <p class="flex items-center justify-center rounded-md text-sm font-bold bg-green-600 text-white px-1.5 py-0.5">{{ $sprint->cards->where('status', 'done')->count() }}</p>
                            <p class="text-green-500 font-bold">{{ __('sprints.done') }}</p>
                        </div>
                        <div>
                            <i wire:click="createCard('done')" class="fi fi-ss-plus dark:text-white cursor-pointer"></i>
                        </div>
                    </div>
                    <div class="mt-2" x-ref="doneTasks" data-column="done">

                    </div>
                </div>
                <div class="w-full col-span-1 bg-gray-100 dark:bg-gray-700 rounded-md p-2"
                    x-data
                    x-init="Sortable.create($refs.releasedTasks, {
                        group: 'cards',
                        animation: 150,
                        onEnd: function (evt) {
                            @this.call('updateCardOrder', evt.item.dataset.id, evt.to.dataset.column, evt.newIndex);
                        }
                    })">
                    <div class="flex justify-between">
                        <div class="w-full flex gap-x-2">
                            <p class="flex items-center justify-center rounded-md text-sm font-bold bg-sky-500 text-white px-1.5 py-0.5">{{ $sprint->cards->where('status', 'released')->count() }}</p>
                            <p class="text-sky-500 font-bold">{{ __('sprints.released') }}</p>
                        </div>
                        <div>
                            <i wire:click="createCard('released')" class="fi fi-ss-plus dark:text-white cursor-pointer"></i>
                        </div>
                    </div>
                    <div class="mt-2" x-ref="releasedTasks" data-column="released">

                    </div>
                </div>
            </div> --}}
        </div>
    </div>
</div>
