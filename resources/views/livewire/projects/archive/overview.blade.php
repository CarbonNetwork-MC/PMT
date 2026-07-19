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
            ]
        ]" />
    </x-slot>

    {{-- Top Bar --}}
    <x-containers.main padding="4">
        <div class="flex gap-4">
            <x-widgets.info-card
                title="{{ __('archive.labels.archived_sprints') }}"
                value="{{ $project->sprints->where('is_archived', true)->count() }}"
                icon="sr-archive"
            />
        </div>
    </x-containers.main>

    {{-- Sprints --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mt-4">
        @forelse ($sprints as $sprint)
            <x-project.archive-card
                projectUuid="{{ $project->uuid }}"
                :sprint="$sprint"
                :isProjectAdminOrOwner="$isProjectAdminOrOwner"
            />
        @empty
            <div class="col-span-2 md:col-span-3 lg:col-span-4 bg-white dark:bg-gray-800 shadow-md rounded-lg p-4">
                <p class="text-center text-gray-500 dark:text-gray-300">
                    {{ __('archive.messages.no_archived_sprints') }}
                </p>
            </div>
        @endforelse
    </div>
</div>
