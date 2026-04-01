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
            ]
        ]" />
    </x-slot>

    <x-containers.main>
        <div class="flex justify-between">
            <div class="">
                <h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-200">{{ __('sidebar.projects.title') }}</h1>
                <p class="mt-2 text-gray-800 dark:text-gray-300">{{ __('projects.messages.projects') }}</p>
                <p class="mt-2 text-gray-800 dark:text-gray-300">{!! __('projects.messages.project-count', ['count' => $projectCount]) !!}{{ $projectCount > 1 ? 's' : '' }}.</p>
            </div>
            <div class="flex items-end">
                <x-buttons.primary-button href="{{ route('projects.new.render') }}">
                    {{ __('projects.titles.new') }}
                </x-buttons.primary-button>
            </div>
        </div>
    </x-containers.main>

    <div class="grid grid-cols-3 lg:grid-cols-4 3xl:grid-cols-5 gap-4 mt-4">
        @forelse ($projects as $project)
            @php
                $memberCount = $project->members 
                    ? count($project->members)
                    : 0;
                $memberCount = $project->owner ? $memberCount + 1 : $memberCount;
            @endphp
            <x-project.project-card :project="$project" :memberCount="$memberCount" />
        @empty
            <div class="col-span-full">
                <div class="block p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700">
                    <h2 class="text-centertext-2xl font-semibold text-gray-800 dark:text-gray-200">{{ __('projects.messages.no-projects') }}</h2>
                    <p class="mt-2 text-centertext-gray-600 dark:text-gray-400">{{ __('projects.messages.no-projects-message') }}</p>
                </div>
            </div>
        @endforelse
    </div>
</div>
