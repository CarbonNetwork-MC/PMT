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
            <div class="flex flex-col bg-gray-100 dark:bg-gray-900 rounded-lg px-4 py-1.5">
                <p class="text-sm font-bold uppercase dark:text-white">
                    {{ __('archive.labels.archived_sprints') }}
                </p>
                <div class="flex justify-center gap-2">
                    <i class="fi fi-sr-archive text-gray-800 dark:text-gray-300"></i>
                    <p class="text-black dark:text-white text-sm">
                        {{ $project->sprints->where('is_archived', true)->count() }}
                    </p>
                </div>
            </div>
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
