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
            ]
        ]" />
    </x-slot>

    <div class="flex justify-center">
        <x-containers.main class="w-2/3">
            <x-navigation.tabs 
                :active="'columns'"
                :tabs="[
                    [
                        'key' => 'general', 
                        'label' => __('settings.nav.general'), 
                        'href' => route('projects.settings.general.render', ['uuid' => $project->uuid])
                    ],
                    [
                        'key' => 'members', 
                        'label' => __('settings.nav.members'), 
                        'href' => route('projects.settings.members.render', ['uuid' => $project->uuid])
                    ],
                    [
                        'key' => 'columns',
                        'label' => __('settings.nav.columns'),
                        'href' => route('projects.settings.columns.render', ['uuid' => $project->uuid]),
                        'disabled' => !$isProjectAdmin && !$isProjectOwner
                    ],
                    [
                        'key' => 'admin',
                        'label' => __('settings.nav.admin'),
                        'href' => route('projects.settings.admin.render', ['uuid' => $project->uuid]),
                        'disabled' => !$isProjectOwner
                    ],
                ]"
            />

            <div class="mt-8 mx-4">
                <div class="flex justify-end">
                    <x-buttons.primary-button href="{{ route('projects.settings.columns.new.render', ['uuid' => $project->uuid]) }}">
                        {{ __('settings.buttons.add_column') }}
                    </x-buttons.primary-button>
                </div>

                <div class="mt-4">
                    <x-tables.table-striped>
                        <x-slot name="headers">
                            <tr>
                                <x-tables.table-header>{{ __('settings.labels.position') }}</x-tables.table-header>
                                <x-tables.table-header>{{ __('settings.labels.column_name') }}</x-tables.table-header>
                                <x-tables.table-header>{{ __('settings.labels.column_type') }}</x-tables.table-header>
                                <x-tables.table-header>{{ __('settings.labels.color') }}</x-tables.table-header>
                                <th></th>
                            </tr>
                        </x-slot>
                        <x-slot name="rows">
                            @forelse ($projectColumns as $column)
                                <x-tables.table-row>
                                    <x-tables.table-data>{{ $column->position }}</x-tables.table-data>
                                    <x-tables.table-data>{{ $column->name }}</x-tables.table-data>
                                    <x-tables.table-data>{{ __('settings.labels.' . $column->column_type) }}</x-tables.table-data>
                                    <x-tables.table-data>
                                        @if ($column->color)
                                            <div class="grid grid-cols-3">
                                                <div class="col-span-1">
                                                    <p class="text-{{ $column->color->name }}-{{ $column->color->text_color }}">
                                                        {{ ucfirst($column->color->name) }}
                                                    </p>
                                                </div>
                                                <div class="col-span-1 flex gap-x-2">
                                                    <p>{{ __('settings.labels.text_color') }}</p>
                                                    <div class="w-6 h-6 rounded bg-{{ $column->color->name }}-{{ $column->color->text_color }}"></div>
                                                </div>
                                                <div class="col-span-1 flex gap-x-2">
                                                    <p>{{ __('settings.labels.background_color') }}</p>
                                                    <div class="w-6 h-6 rounded bg-{{ $column->color->name }}-{{ $column->color->background_color }}"></div>
                                                </div>
                                            </div>
                                        @else
                                            -
                                        @endif
                                    </x-tables.table-data>
                                    <x-tables.table-actions>
                                        <x-tables.primary-action href="{{ route('projects.settings.columns.edit.render', ['uuid' => $project->uuid, 'columnId' => $column->id]) }}">
                                            {{ __('general.buttons.edit') }}
                                        </x-tables.primary-action>
                                        <x-tables.danger-action wire:click="removeColumn('{{ $column->id }}')">
                                            {{ __('general.buttons.remove') }}
                                        </x-tables.danger-action>
                                    </x-tables.table-actions>
                                    </x-tables.table-row>
                            @empty
                                <x-tables.table-row>
                                    <x-tables.empty-state :colspan="5">
                                        {{ __('settings.columns.no_columns') }}
                                    </x-tables.empty-state>
                                </x-tables.table-row>
                            @endforelse
                        </x-slot>
                    </x-tables.table-striped>
                </div>
            </div>
        </x-containers.main>
    </div>

    {{-- Remove Column Modal --}}
    <x-modals.modal wire:model="showRemoveColumnModal">
        <x-slot name="title">
            <p class="text-center">
                {{ __('settings.titles.remove_column') }}
            </p>
        </x-slot>
        <x-slot name="content">
            <p class="text-center">
                {{ __('settings.messages.remove_column_confirmation') }}
            </p>
        </x-slot>
        <x-slot name="footer">
            <x-buttons.secondary-button wire:click="$set('showRemoveColumnModal', false)">
                {{ __('general.buttons.cancel') }}
            </x-buttons.secondary-button>
            <x-buttons.danger-button wire:click="confirmRemoveColumn">
                {{ __('general.buttons.remove') }}
            </x-buttons.danger-button>
        </x-slot>
    </x-modals.modal>
</div>
