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
                'url' => route('projects.dashboard.render', ['uuid' => $project->uuid]),
                'label' => $project->name,
            ],
            [
                'icon' => '',
                'url' => route('projects.sprints.render', ['uuid' => $project->uuid]),
                'label' => __('sprints.titles.sprint-overview'),
            ]
        ]" />
    </x-slot>

    <x-containers.main>
        <div class="flex justify-between">
            <div class="flex gap-6">
                <div>
                    <p class="text-sm font-bold uppercase dark:text-white">{{ __('sprints.labels.sprints') }}</p>
                    <div class="flex justify-center gap-2">
                        <i class="fi fi-sr-running dark:text-white"></i>
                        <p class="dark:text-white">{{ $sprintCount }}</p>
                    </div>
                </div>
                <div>
                    <p class="text-sm font-bold uppercase dark:text-white">{{ __('sprints.labels.active-sprints') }}</p>
                    <div class="flex justify-center gap-2">
                        <i class="fi fi-sr-running dark:text-white"></i>
                        <p class="dark:text-white">{{ $activeSprints }}</p>
                    </div>
                </div>
                <div>
                    <p class="text-sm font-bold uppercase dark:text-white">{{ __('sprints.labels.completed-sprints') }}</p>
                    <div class="flex justify-center gap-2">
                        <i class="fi fi-sr-check dark:text-white"></i>
                        <p class="dark:text-white">{{ $completedSprints }}</p>
                    </div>
                </div>
                <div>
                    <p class="text-sm font-bold uppercase dark:text-white">{{ __('sprints.labels.archived-sprints') }}</p>
                    <div class="flex justify-center gap-2">
                        <i class="fi fi-sr-archive dark:text-white"></i>
                        <p class="dark:text-white">{{ $archivedSprints }}</p>
                    </div>
                </div>
            </div>
            
            <x-buttons.primary-button href="{{ route('projects.sprints.new.render', ['uuid' => $project->uuid]) }}">
                {{ __('sprints.buttons.new_sprint') }}
            </x-buttons.primary-button>
        </div>
    </x-containers.main>

    <div class="grid grid-cols-3 lg:grid-cols-4 3xl:grid-cols-5 gap-4 mt-4">
        @forelse ($sprints as $sprint)
            {{-- TODO: link to board for $sprint --}}
            {{-- TODO: burndown chart for $sprint --}}
            <x-project.sprint-card :sprint="$sprint" />
        @empty

        @endforelse
    </div>
</div>
