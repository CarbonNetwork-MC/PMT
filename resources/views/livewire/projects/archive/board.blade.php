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
            <div class="flex flex-col bg-gray-100 dark:bg-gray-900 rounded-lg px-4 py-1.5">
                <p class="text-black dark:text-white text-sm font-bold">{{ __('board.labels.duration') }}</p>
                <div class="flex gap-x-2">
                    <i class="fi fi-ss-calendar text-gray-800 dark:text-gray-300"></i>
                    <p class="text-black dark:text-white text-sm">
                        @if ($sprint->start_date->format('Y') === $sprint->end_date->format('Y'))
                            @if ($sprint->start_date->format('M') === $sprint->end_date->format('M'))
                                {{ $sprint->start_date->format('M d') }} - {{ $sprint->end_date->format('d, Y') }}
                            @else
                                {{ $sprint->start_date->format('M d') }} - {{ $sprint->end_date->format('M d, Y') }}
                            @endif
                        @else
                            {{ $sprint->start_date->format('M d, Y') }} - {{ $sprint->end_date->format('M d, Y') }}
                        @endif
                    </p>
                </div>
            </div>
            <div class="flex flex-col bg-gray-100 dark:bg-gray-900 rounded-lg px-4 py-1.5">
                <p class="text-black dark:text-white text-sm font-bold">{{ __('board.labels.total_cards') }}</p>
                <div class="flex gap-x-2">
                    <i class="fi fi-rr-cards-blank text-gray-800 dark:text-gray-300"></i>
                    <p class="text-black dark:text-white text-sm">
                        @if ($sprint->status === 'completed')
                            {{ $this->columns->where('column_type', 'done')->sum(fn($column) => $column->cards->where('sprint_uuid', $sprint->uuid)->count()) }}
                        @else
                            {{ $this->columns->sum(fn($column) => $column->cards->where('sprint_uuid', $sprint->uuid)->count()) }}
                        @endif
                    </p>
                </div>
            </div>
            <div class="flex flex-col bg-gray-100 dark:bg-gray-900 rounded-lg px-4 py-1.5">
                <p class="text-black dark:text-white text-sm font-bold">{{ __('board.labels.total_tasks') }}</p>
                <div class="flex gap-x-2">
                    <i class="fi fi-rr-task-checklist text-gray-800 dark:text-gray-300"></i>
                    <p class="text-black dark:text-white text-sm">
                        @if ($sprint->status === 'completed')
                            {{ $this->columns->where('column_type', 'done')->sum(fn($column) => $column->cards->where('sprint_uuid', $sprint->uuid)->sum(fn($card) => $card->tasks->count())) }}
                        @else
                            {{ $this->columns->sum(fn($column) => $column->cards->where('sprint_uuid', $sprint->uuid)->sum(fn($card) => $card->tasks->count())) }}
                        @endif
                    </p>
                </div>
            </div>
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
