<div>
    {{-- Page Title --}}
    @section('title', __('titles.projects.sprints.new') . ' | ' . $project->name)

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
            ],
            [
                'icon' => '',
                'url' => route('projects.sprints.new.render', ['uuid' => $project->uuid]),
                'label' => __('sprints.titles.new-sprint'),
            ]
        ]" />
    </x-slot>

    <x-containers.main>
        <x-containers.title>
            {{ __('sprints.titles.new-sprint') }}
        </x-containers.title>

        <div class="grid grid-cols-4 gap-4 mt-6">
            {{-- Name --}}
            <div class="col-span-1">
                <x-forms.text-input label="{{ __('sprints.labels.name') }}" wire:model="name" required />
            </div>

            <div class="col-span-3"></div>

            {{-- Start Date --}}
            <div class="col-span-1">
                <x-forms.date-input label="{{ __('sprints.labels.start_date') }}" wire:model="start_date" required />
            </div>

            {{-- End Date --}}
            <div class="col-span-1">
                <x-forms.date-input label="{{ __('sprints.labels.end_date') }}" wire:model="end_date" required />
            </div>
        </div>

        <div class="flex justify-end items-center gap-4">
            <x-forms.required-fields />
            <x-buttons.primary-button wire:click="createSprint">
                {{ __('general.buttons.create') }}
            </x-buttons.primary-button>
        </div>
    </x-containers.main>
</div>
