<div>
    {{-- Page Title --}}
    @section('title', __('titles.admin.settings'))

    {{-- Breadcrumbs --}}
    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[
            [
                'icon' => 'fi fi-rs-house-chimney',
                'url' => route('admin.dashboard.render'),
                'label' => '',
            ],
            [
                'icon' => '',
                'url' => route('admin.settings.render'),
                'label' => __('admin.titles.settings.overview'),
            ]
        ]" />
    </x-slot>
    
    <div class="mx-20">
        <x-profile.profile-card>
            <x-slot name="title">
                {{ __('admin.titles.settings.sprint_id') }}
            </x-slot>
            <x-slot name="description">
                {!! __('admin.descriptions.settings.sprint_id') !!}
            </x-slot>
            <x-slot name="form">
                <div class="w-1/2">
                    <x-forms.text-input label="{{ __('admin.labels.settings.sprint_id') }}" wire:model="sprintId" required />
                </div>

                <div class="flex justify-end items-center gap-x-4">
                    <x-buttons.primary-button type="submit" wire:click.prevent="saveSprintId" primary>
                        {{ __('general.buttons.save') }}
                    </x-buttons.primary-button>
                </div>
            </x-slot>
        </x-profile.profile-card>
    </div>
</div>
