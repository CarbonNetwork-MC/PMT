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
                'icon' => 'fi fi-sr-department-structure',
                'url' => route('projects.render'),
                'label' => __('sidebar.projects.title'),
            ]
        ]" />
    </x-slot>

    <div class="grid grid-cols-3 lg:grid-cols-4 3xl:grid-cols-5 gap-4">
        @forelse ($projects as $project)
            <x-project.card :project="$project" />
        @empty
            <div class="col-span-full">
                <div class="block p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700">
                    <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-200">No Projects Found</h2>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">You haven't created or joined any projects yet. Start by creating a new project or joining an existing one.</p>
                </div>
            </div>
        @endforelse
    </div>
</div>
