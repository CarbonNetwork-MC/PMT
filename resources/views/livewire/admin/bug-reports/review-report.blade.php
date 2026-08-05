<div>
    {{-- Page Title --}}
    @section('title', __('titles.admin.bug-reports.review_report'))

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
                'url' => route('admin.bug-reports.render'),
                'label' => __('admin.titles.bug-reports.overview'),
            ],
            [
                'icon' => '',
                'url' => route('admin.bug-reports.render'),
                'label' => __('admin.titles.bug-reports.review'),
            ],
            [
                'icon' => '',
                'url' => route('admin.bug-reports.review-report.render', ['reportId' => $report->id]),
                'label' => $report->id,
            ]
        ]" />
    </x-slot>

    {{-- Bug Report Information --}}
    <x-containers.main>
        <h1 class="font-semibold text-lg dark:text-white">
            {{ __('admin.titles.bug-reports.review_report') }} - {{ $report->id }}
        </h1>

        <div class="mt-2 dark:text-white">
            {{-- Title --}}
            <div class="flex gap-2">
                <p class="font-semibold dark:text-white">
                    {{ __('admin.labels.bug-reports.title') }}:
                </p>
                <p class="text-gray-600 dark:text-gray-400">
                    {{ $report->title }}
                </p>
            </div>

            {{-- Description --}}
            <div class="flex gap-2">
                <p class="font-semibold dark:text-white">
                    {{ __('admin.labels.bug-reports.description') }}:
                </p>
                <p class="text-gray-600 dark:text-gray-400">
                    {{ $report->description }}
                </p>
            </div>

            {{-- Page --}}
            <div class="flex gap-2">
                <p class="font-semibold dark:text-white">
                    {{ __('admin.labels.bug-reports.page') }}:
                </p>
                <p class="text-gray-600 dark:text-gray-400">
                    {{ $report->page }}
                </p>
            </div>

            {{-- User --}}
            <div class="flex gap-2">
                <p class="font-semibold dark:text-white">
                    {{ __('admin.labels.bug-reports.user') }}:
                </p>
                <p class="text-gray-600 dark:text-gray-400">
                    {{ $report->user->name }}
                </p>
            </div>

            {{-- Screenshots --}}
            @if ($report->screenshots->isNotEmpty())
                <div class="space-y-2">
                    <p class="font-semibold dark:text-white">
                        {{ __('admin.labels.bug-reports.screenshots') }}:
                    </p>

                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
                        @foreach ($report->screenshots as $screenshot)
                            @php
                                $imageUrl = asset('storage/' . $screenshot->path);
                            @endphp

                            <a
                                href="{{ $imageUrl }}"
                                data-fancybox="bug-report-{{ $report->id }}"
                                data-caption="Screenshot {{ $loop->iteration }}"
                                class="group relative block overflow-hidden rounded-md
                                    border border-gray-200 bg-gray-100
                                    focus:outline-none focus:ring-2 focus:ring-blue-500
                                    dark:border-gray-700 dark:bg-gray-800"
                            >
                                <img
                                    src="{{ $imageUrl }}"
                                    alt="Bug report screenshot {{ $loop->iteration }}"
                                    loading="lazy"
                                    class="aspect-video w-full object-cover transition-transform
                                        duration-200 group-hover:scale-105"
                                >

                                <div
                                    class="absolute inset-0 flex items-center justify-center
                                        bg-black/0 transition-colors group-hover:bg-black/25"
                                >
                                    <svg
                                        class="h-7 w-7 text-white opacity-0 transition-opacity
                                            group-hover:opacity-100"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        aria-hidden="true"
                                    >
                                        <circle cx="11" cy="11" r="8" />
                                        <path d="m21 21-4.35-4.35" />
                                        <path d="M11 8v6" />
                                        <path d="M8 11h6" />
                                    </svg>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="flex gap-2">
                    <p class="font-semibold dark:text-white">
                        {{ __('admin.labels.bug-reports.screenshots') }}:
                    </p>
                    <p class="text-gray-600 dark:text-gray-400">
                        {{ __('admin.messages.bug-reports.no_screenshots') }}
                    </p>
                </div>
            @endif
        </div>
    </x-containers.main>

    {{-- Cards & Tasks --}}
    <x-containers.main class="mt-4">
        <h1 class="font-semibold text-lg dark:text-white">
            {{ __('admin.titles.bug-reports.create_card') }} - {{ $report->id }}
        </h1>

        {{-- Card --}}
        <div class="mt-2 space-y-2">
            <x-forms.text-input wire:model="cardTitle" label="{{ __('admin.labels.bug-reports.card_title') }}" width="w-1/3" />
            <x-forms.text-area wire:model="cardDescription" label="{{ __('admin.labels.bug-reports.card_description') }}" />
        </div>

        {{-- Add Task --}}
        <div class="flex gap-2 font-semibold mt-4">
            <p>
                {{ __('admin.labels.bug-reports.tasks') }}
            </p>
            -
            <p class="flex gap-2 cursor-pointer" wire:click="addTask">
                <i class="fi fi-rr-add text-green-500"></i>
                {{ __('admin.buttons.bug-reports.add_task') }}
            </p>
        </div>

        {{-- Tasks --}}
        <div class="space-y-2 mt-2">
            @foreach ($tasks as $task)
                <div class="flex gap-2">
                    <div class="flex flex-col">
                        <p>#{{ $loop->iteration }}</p>
                        <i class="fi fi-rr-cross-circle text-red-500 cursor-pointer" wire:click="removeTask('{{ $task['id'] }}')"></i>
                    </div>
                    <x-forms.text-area wrapper:class="w-full" wire:model="tasks.{{ $loop->index }}.description" label="{{ __('admin.labels.bug-reports.description') }}" />
                </div>
            @endforeach
        </div>

        <div class="flex justify-end gap-2 mt-4">
            <x-buttons.primary-button wire:click="resolveReport">
                {{ __('admin.buttons.bug-reports.resolve_report') }}
            </x-buttons.primary-button>
            <x-buttons.danger-button wire:click="removeReport">
                {{ __('admin.buttons.bug-reports.remove_report') }}
            </x-buttons.danger-button>
        </div>
    </x-containers.main>

    {{-- Remove Report Modal --}}
    <x-modals.modal wire:model="showRemoveModal">
        <x-slot name="title">
            <p class="text-center">{{ __('admin.titles.bug-reports.remove_report') }}</p>
        </x-slot>

        <x-slot name="content">
            <p class="text-center dark:text-white">
                {{ __('admin.messages.bug-reports.remove_report_confirmation', ['reportId' => $report->id]) }}
            </p>
        </x-slot>

        <x-slot name="footer">
            <div class="flex justify-end gap-2">
                <x-buttons.secondary-button wire:click="$set('showRemoveModal', false)">
                    {{ __('general.buttons.cancel') }}
                </x-buttons.secondary-button>
                <x-buttons.danger-button wire:click="confirmRemoveReport">
                    {{ __('admin.buttons.bug-reports.remove_report') }}
                </x-buttons.danger-button>
            </div>
        </x-slot>
    </x-modals.modal>
</div>
