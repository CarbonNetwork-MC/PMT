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

        @endforelse
    </div>
</div>
