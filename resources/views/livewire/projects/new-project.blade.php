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
                'url' => route('projects.new.render'),
                'label' => __('projects.titles.new'),
            ]
        ]" />
    </x-slot>

    <x-containers.main>
        <x-containers.title>
            {{ __('projects.titles.new') }}
        </x-containers.title>

        <div class="grid grid-cols-4 gap-4 mt-6">
            {{-- Name --}}
            <div class="col-span-1">
                <x-forms.text-input label="{{ __('projects.labels.name') }}" wire:model="name" required />
            </div>

            <div class="col-span-3"></div>

            {{-- Description --}}
            <div class="col-span-2">
                <x-forms.text-area label="{{ __('projects.labels.description') }}" wire:model="description" rows="4" />
            </div>
        </div>

        <div class="flex justify-end items-center gap-x-4">
            <x-forms.required-fields />
            <x-buttons.primary-button wire:click="createProject">
                {{ __('general.buttons.create') }}
            </x-buttons.primary-button>
        </div>
    </x-containers.main>
</div>
