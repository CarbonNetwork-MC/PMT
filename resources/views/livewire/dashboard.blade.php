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
                'url' => route('dashboard.render'),
                'label' => __('dashboard.titles.dashboard'),
            ]
        ]" />
    </x-slot>

    {{-- Information Widget (Num. Projects, Num. Unique Users the user is working with) --}}
    <x-containers.main>
        <div class="flex gap-4">
            <x-widgets.info-card
                title="{{ __('dashboard.labels.projects') }}"
                value="{{ $projectsCount }}"
                icon="rs-folder"
            />
            <x-widgets.info-card
                title="{{ __('dashboard.labels.unique_users') }}"
                value="{{ $uniqueUsersCount }}"
                icon="rs-users"
            />
        </div>
    </x-containers.main>

    {{-- Projects with recent activity --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-4 gap-4 mt-4">
        @forelse ($projects as $project)
            <x-project.detailed-project-card 
                :project="$project"
                @class([
                    'hidden md:block' => $loop->index === 1,
                    'hidden lg:block' => $loop->index >= 2,
                ])
            />
        @empty
            <x-containers.main class="col-span-1 lg:col-span-2 xl:col-span-4 text-center">
                {{ __('dashboard.labels.no_projects') }}
            </x-containers.main>
        @endforelse
    </div>
</div>
