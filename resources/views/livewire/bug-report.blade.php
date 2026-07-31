<div>
    {{-- Page Title --}}
    @section('title', __('titles.bug_report'))

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
                'url' => route('bug-report.render'),
                'label' => __('bug-report.titles.bug_report'),
            ]
        ]" />
    </x-slot>

    @if ($errors->any())
        @foreach ($errors->all() as $error)
            {{ $error }}
        @endforeach
    @endif

    <div class="flex justify-center">
        <x-containers.main class="w-2/3">
            <h1 class="text-lg font-bold dark:text-white mb-1">
                {{ __('bug-report.titles.bug_report') }}
            </h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                {{ __('bug-report.messages.bug_report_description') }}
            </p>

            <div class="flex flex-col gap-y-4">
                <x-forms.text-input label="{{ __('bug-report.labels.title') }}" wire:model="title" required />
                <x-forms.text-area label="{{ __('bug-report.labels.description') }}" wire:model="description" required />
                <x-forms.text-input width="w-1/3" label="{{ __('bug-report.labels.page') }}" wire:model="page" required />
                <div>
                    <x-forms.file-input label="{{ __('bug-report.labels.screenshots') }}" wire:model.live="screenshots" multiple />
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                        {{ __('bug-report.messages.screenshots_helper') }}
                    </p>
                </div>
            </div>

            <div class="flex justify-end items-center gap-x-4 mt-4">
                <x-buttons.primary-button type="submit" wire:click.prevent="submitBugReport" primary>
                    {{ __('bug-report.buttons.submit') }}
                </x-buttons.primary-button>
            </div>
        </x-containers.main>
    </div>
</div>
