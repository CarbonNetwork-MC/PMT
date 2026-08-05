<div>
    {{-- Page Title --}}
    @section('title', __('titles.admin.bug-reports.overview'))

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
            ]
        ]" />
    </x-slot>

    <x-containers.main>
        <div class="flex justify-between">
            <h1 class="font-bold text-lg dark:text-white">
                {{ __('admin.titles.bug-reports.unreviewed_bugs') }}
            </h1>

            <x-forms.text-input wire:model.live="unreviewedSearch" placeholder="{{ __('general.placeholders.search') }}" />
        </div>

        <x-tables.table-striped class="mt-4">
            <x-slot name="headers">
                <x-tables.table-header>#</x-tables.table-header>
                <x-tables.table-header>{{ __('admin.labels.bug-reports.user') }}</x-tables.table-header>
                <x-tables.table-header>{{ __('admin.labels.bug-reports.page') }}</x-tables.table-header>
                <x-tables.table-header>{{ __('admin.labels.bug-reports.title') }}</x-tables.table-header>
                <x-tables.table-header>{{ __('admin.labels.bug-reports.description') }}</x-tables.table-header>
                <x-tables.table-header></x-tables.table-header>
            </x-slot>
            <x-slot name="rows">
                @forelse ($unreviewedBugReports as $report)
                    <x-tables.table-row>
                        <x-tables.table-data>{{ $report->id }}</x-tables.table-data>
                        <x-tables.table-data>{{ $report->user->name }}</x-tables.table-data>
                        <x-tables.table-data>{{ $report->page }}</x-tables.table-data>
                        <x-tables.table-data>{{ $report->title }}</x-tables.table-data>
                        <x-tables.table-data>{{ $report->description }}</x-tables.table-data>
                        <x-tables.table-actions>
                            <x-tables.primary-action href="{{ route('admin.bug-reports.review-report.render', ['reportId' => $report->id]) }}">
                                {{ __('admin.buttons.bug-reports.review_bug') }}
                            </x-tables.primary-action>
                        </x-tables.table-actions>
                    </x-tables.table-row>
                @empty
                    <x-tables.table-row>
                        <x-tables.empty-state colspan="6">
                            {{ __('admin.messages.bug-reports.no_unreviewed_bugs') }}
                        </x-tables.empty-state>
                    </x-tables.table-row>
                @endforelse
            </x-slot>
            <x-slot name="pagination">
                @if ($unreviewedBugReports->hasPages())
                    <div class="w-full flex items-center gap-4 mt-4">
                        {{ $unreviewedBugReports->links() }}
                        <x-tables.per-page-select wire:model.live="unreviewedPerPage">
                            <option value="5">5</option>
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </x-tables.per-page-select>
                    </div>
                @endif
            </x-slot>
        </x-tables.table-striped>
    </x-containers.main>

    <x-containers.main class="mt-4">
        <div class="flex justify-between">
            <h1 class="font-bold text-lg dark:text-white">
                {{ __('admin.titles.bug-reports.reviewed_bugs') }}
            </h1>

            <x-forms.text-input wire:model.live="reviewedSearch" placeholder="{{ __('general.placeholders.search') }}" />
        </div>

        <x-tables.table-striped class="mt-4">
            <x-slot name="headers">
                <x-tables.table-header>#</x-tables.table-header>
                <x-tables.table-header>{{ __('admin.labels.bug-reports.user') }}</x-tables.table-header>
                <x-tables.table-header>{{ __('admin.labels.bug-reports.page') }}</x-tables.table-header>
                <x-tables.table-header>{{ __('admin.labels.bug-reports.title') }}</x-tables.table-header>
                <x-tables.table-header>{{ __('admin.labels.bug-reports.description') }}</x-tables.table-header>
                <x-tables.table-header></x-tables.table-header>
            </x-slot>
            <x-slot name="rows">
                @forelse ($reviewedBugReports as $report)
                    <x-tables.table-row>
                        <x-tables.table-data>{{ $report->id }}</x-tables.table-data>
                        <x-tables.table-data>{{ $report->user->name }}</x-tables.table-data>
                        <x-tables.table-data>{{ $report->page }}</x-tables.table-data>
                        <x-tables.table-data>{{ $report->title }}</x-tables.table-data>
                        <x-tables.table-data>{{ $report->description }}</x-tables.table-data>
                        <x-tables.table-actions>
                            <x-tables.danger-action wire:click="undoReviewBug('{{ $report->id }}')">
                                {{ __('admin.buttons.bug-reports.undo') }}
                            </x-tables.danger-action>
                        </x-tables.table-actions>
                    </x-tables.table-row>
                @empty
                    <x-tables.table-row>
                        <x-tables.empty-state colspan="6">
                            {{ __('admin.messages.bug-reports.no_reviewed_bugs') }}
                        </x-tables.empty-state>
                    </x-tables.table-row>
                @endforelse
            </x-slot>
            <x-slot name="pagination">
                @if ($reviewedBugReports->hasPages())
                    <div class="w-full flex items-center gap-4 mt-4">
                        {{ $reviewedBugReports->links() }}
                        <x-tables.per-page-select wire:model.live="reviewedPerPage">
                            <option value="5">5</option>
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </x-tables.per-page-select>
                    </div>
                @endif
            </x-slot>
        </x-tables.table-striped>
    </x-containers.main>
</div>
