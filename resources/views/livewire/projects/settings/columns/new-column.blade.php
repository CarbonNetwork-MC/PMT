<div>
    {{-- Page Title --}}
    @section('title', __('titles.project_settings_columns_new') . ' | ' . $project->name)

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
                'url' => route('projects.settings.general.render', ['uuid' => $project->uuid]),
                'label' => __('settings.titles.settings'),
            ],
            [
                'icon' => '',
                'url' => route('projects.settings.columns.render', ['uuid' => $project->uuid]),
                'label' => __('settings.titles.columns'),
            ],
            [
                'icon' => '',
                'url' => route('projects.settings.columns.new.render', ['uuid' => $project->uuid]),
                'label' => __('settings.titles.new_column'),
            ]
        ]" />
    </x-slot>

    <div class="flex justify-center">
        <div class="w-2/3">
            <x-containers.main>
                <x-containers.title>
                    {{ __('settings.titles.new_column') }}
                </x-containers.title>

                <div class="grid grid-cols-4 gap-4 mt-6">
                    {{-- Name --}}
                    <div class="col-span-1">
                        <x-forms.text-input label="{{ __('settings.labels.name') }}" wire:model="name" required />
                    </div>

                    {{-- Color --}}
                    <div class="col-span-1">
                        <x-forms.select
                            label="{{ __('settings.labels.color') }}"
                            wire:model="colorId"
                            :options="$colors->map(function($color) {
                                return [
                                    'value' => $color->id,
                                    'label' => ucfirst($color->name),
                                    'class' => 'text-' . $color->name . '-' . $color->text_color,
                                ];
                            })"
                            required
                        />
                    </div>

                    {{-- Position --}}
                    <div class="col-span-1">
                        <x-forms.number-input label="{{ __('settings.labels.position') }}" wire:model="position" min="{{ $minColumns }}" max="{{ $maxColumns }}" required />
                    </div>

                    <div class="col-span-1"></div>

                    {{-- Column Type --}}
                    <div class="col-span-1">
                        <x-forms.select
                            label="{{ __('settings.labels.column_type') }}"
                            wire:model="columnType"
                            :options="[
                                ['value' => 'todo', 'label' => __('settings.labels.todo')],
                                ['value' => 'doing', 'label' => __('settings.labels.doing')],
                                ['value' => 'done', 'label' => __('settings.labels.done')],
                            ]"
                            required
                        />
                    </div>
                </div>

                <div class="mt-8">
                    <div class="flex gap-x-4">
                        <div class="flex flex-col">
                            <p class="text-black dark:text-white mb-2">
                                {{ __('settings.labels.text_color') }}
                            </p>
                            <div class="h-6 w-30 rounded text-center {{ $colorId ? 'bg-' . $color->name . '-' . $color->text_color : 'bg-white' }}">{{ $color ? '' : 'None' }}</div>
                        </div>
                        <div class="flex flex-col">
                            <p class="text-black dark:text-white mb-2">
                                {{ __('settings.labels.background_color') }}
                            </p>
                            <div class="h-6 w-30 rounded text-center {{ $colorId ? 'bg-' . $color->name . '-' . $color->background_color : 'bg-white' }}">{{ $color ? '' : 'None' }}</div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end items-center gap-4 mt-6">
                    <x-forms.required-fields />
                    <x-buttons.primary-button wire:click="save">
                        {{ __('general.buttons.save') }}
                    </x-buttons.primary-button>
                </div>
            </x-containers.main>
        </div>
    </div>
</div>
