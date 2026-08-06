<div>
    {{-- Page Title --}}
    @section('title', __('titles.projects.board') . ' | ' . $project->name . ' | ' . $sprint->name)

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
                'url' => route('projects.board.render', ['uuid' => $project->uuid, 'sprintUuid' => $sprint->uuid]),
                'label' => __('board.titles.boards'),
            ],
            [
                'icon' => '',
                'url' => route('projects.board.render', ['uuid' => $project->uuid, 'sprintUuid' => $sprint->uuid]),
                'label' => $sprint->name,
            ]
        ]" />
    </x-slot>

    {{-- Information Widget --}}
    <x-containers.main padding="4">
        <div class="flex gap-x-4">
            <x-widgets.info-card
                title="{{ __('board.labels.days_left') }}"
                value="{{ $daysLeft }}"
                icon="ss-calendar-clock"
                textColor="{{ $daysLeft < 1 ? 'red-400' : 'black' }}"
                textColorDark="{{ $daysLeft < 1 ? 'red-400' : 'white' }}"
            />

            <x-widgets.info-card
                title="{{ __('board.labels.duration') }}"
                icon="ss-calendar"
            >
                <x-slot name="value">
                    @if ($sprint->start_date->format('Y') === $sprint->end_date->format('Y'))
                        @if ($sprint->start_date->format('M') === $sprint->end_date->format('M'))
                            {{ $sprint->start_date->format('M d') }} - {{ $sprint->end_date->format('d, Y') }}
                        @else
                            {{ $sprint->start_date->format('M d') }} - {{ $sprint->end_date->format('M d, Y') }}
                        @endif
                    @else
                        {{ $sprint->start_date->format('M d, Y') }} - {{ $sprint->end_date->format('M d, Y') }}
                    @endif
                </x-slot>
            </x-widgets.info-card>

            <x-widgets.info-card
                title="{{ __('board.labels.total_cards') }}"
                icon="rr-cards-blank"
            >
                <x-slot name="value">
                    @if ($sprint->status === 'completed')
                        {{ $this->columns->where('column_type', 'done')->sum(fn($column) => $column->cards->where('sprint_uuid', $sprint->uuid)->count()) }}
                    @else
                        {{ $this->columns->sum(fn($column) => $column->cards->where('sprint_uuid', $sprint->uuid)->count()) }}
                    @endif
                </x-slot>
            </x-widgets.info-card>

            <x-widgets.info-card
                title="{{ __('board.labels.total_tasks') }}"
                icon="rr-task-checklist"
            >
                <x-slot name="value">
                    @if ($sprint->status === 'completed')
                        {{ $this->columns->where('column_type', 'done')->sum(fn($column) => $column->cards->where('sprint_uuid', $sprint->uuid)->sum(fn($card) => $card->tasks->count())) }}
                    @else
                        {{ $this->columns->sum(fn($column) => $column->cards->where('sprint_uuid', $sprint->uuid)->sum(fn($card) => $card->tasks->count())) }}
                    @endif
                </x-slot>
            </x-widgets.info-card>
        </div>
    </x-containers.main>

    {{-- Board --}}
    <x-containers.main padding="4" class="mt-4">
        @if ($sprint->status === 'active')
            <div wire:sortable-group="updateCardOrder" class="grid grid-cols-3 lg:grid-cols-{{ $this->columns->count() <= 3 ? 3 : $this->columns->count() }} gap-2">
                @foreach ($this->columns as $column)
                    <livewire:components.board.column
                        :column="$column"
                        :sprint="$sprint"
                        :users="$users"
                        :refreshKey="$refreshKey"
                        wire:key="column-{{ $column->id }}-{{ $refreshKey }}"
                    />
                @endforeach
            </div>

        @elseif ($sprint->status === 'completed')
            <div class="grid grid-cols-3 lg:grid-cols-{{ $this->columns->where('column_type', 'done')->count() <= 3 ? 3 : $this->columns->where('column_type', 'done')->count() }} gap-2">
                @foreach ($this->columns->where('column_type', 'done') as $column)
                    <livewire:components.board.column
                        :column="$column"
                        :sprint="$sprint"
                        :users="$users"
                        :refreshKey="$refreshKey"
                        wire:key="column-{{ $column->id }}-{{ $column->cards->where('sprint_uuid', $sprint->uuid)->count() }}"
                    />
                @endforeach
            </div>
        @endif
    </x-containers.main>

    {{-- Delete Card Modal --}}
    <x-modals.modal wire:model="showDeleteCardModal">
        <x-slot name="title">
            <p class="text-center">
                {{ __('board.titles.delete_card') }}
            </p>
        </x-slot>
        <x-slot name="content">
            <p class="text-center">
                {!! __('board.messages.confirm_delete_card') !!}
            </p>
        </x-slot>
        <x-slot name="footer">
            <x-buttons.secondary-button wire:click="$set('showDeleteCardModal', false)">
                {{ __('general.buttons.cancel') }}
            </x-buttons.secondary-button>
            <x-buttons.danger-button wire:click="confirmDeleteCard">
                {{ __('general.buttons.delete') }}
            </x-buttons.danger-button>
        </x-slot>
    </x-modals.modal>

    {{-- Delete Task Modal --}}
    <x-modals.modal wire:model="showDeleteTaskModal">
        <x-slot name="title">
            <p class="text-center">
                {{ __('board.titles.delete_task') }}
            </p>
        </x-slot>
        <x-slot name="content">
            <p class="text-center">
                {!! __('board.messages.confirm_delete_task') !!}
            </p>
        </x-slot>
        <x-slot name="footer">
            <x-buttons.secondary-button wire:click="$set('showDeleteTaskModal', false)">
                {{ __('general.buttons.cancel') }}
            </x-buttons.secondary-button>
            <x-buttons.danger-button wire:click="confirmDeleteTask">
                {{ __('general.buttons.delete') }}
            </x-buttons.danger-button>
        </x-slot>
    </x-modals.modal>

    {{-- Selected Card Modal --}}
    @if ($selectedCard)
        <livewire:components.board.modal :project="$project" :card="$selectedCard" :users="$users" />
    @endif
</div>
