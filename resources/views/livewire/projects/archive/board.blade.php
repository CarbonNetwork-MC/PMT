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
                'url' => route('projects.archive.render', ['uuid' => $project->uuid]),
                'label' => __('archive.titles.archive_overview'),
            ],
            [
                'icon' => '',
                'url' => route('projects.archive.board.render', ['uuid' => $project->uuid, 'sprintUuid' => $sprint->uuid]),
                'label' => $sprint->name,
            ]
        ]" />
    </x-slot>

    {{-- Information Widget --}}
    <x-containers.main padding="4">
        <div class="flex gap-x-4">
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
                icon="fi fi-rr-cards-blank"
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
                icon="fi fi-rr-task-checklist"
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
    </x-containers.main>

    {{-- Selected Card Modal --}}
    @if ($selectedCard)
        <livewire:components.board.modal :project="$project" :card="$selectedCard" :users="$users" />
    @endif
</div>
