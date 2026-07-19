<div>
    {{-- Page Title --}}
    @section('title', __('titles.project_dashboard') . ' | ' . $project->name)

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
            ]
        ]" />
    </x-slot>

    {{-- Information Widget --}}
    <x-containers.main padding="4">
        <h1 class="text-xl font-bold text-black dark:text-white">
            {{ $project->name }}
        </h1>
        <p class="text-gray-600 dark:text-gray-300">
            {{ $project->description }}
        </p>
        <div class="flex justify-between items-center mt-4">
            {{-- sprints count, member count, (owning) --}}
            <div class="flex gap-4">
                <x-widgets.info-card
                    title="{{ __('dashboard.labels.active_sprints') }}"
                    value="{{ $project->sprints->where('status', 'active')->count() }}"
                    icon="ss-calendar-clock"
                />
                
                <x-widgets.info-card
                    title="{{ __('dashboard.labels.archived_sprints') }}"
                    value="{{ $project->sprints->where('is_archived', true)->count() }}"
                    icon="ss-calendar-clock"
                />

                <x-widgets.info-card
                    title="{{ __('dashboard.labels.total_sprints') }}"
                    value="{{ $project->sprints->count() }}"
                    icon="ss-calendar-clock"
                />

                <x-widgets.info-card
                    title="{{ __('dashboard.labels.members') }}"
                    value="{{ $memberCount }}"
                    icon="ss-users"
                />

                @if ($project->owner->uuid === auth()->user()->uuid)
                    <x-widgets.info-card
                        title="{{ __('dashboard.labels.owning') }}"
                        value=""
                        icon="ss-user-crown"
                    />
                @endif
            </div>

            {{-- Project Users --}}
            <div class="flex bg-gray-100 dark:bg-gray-900 rounded-lg px-4 py-1.5">
                <i class="fi fi-sr-users text-gray-700 dark:text-white me-2"></i>
                <div class="flex ">
                    @foreach ($users as $user)
                        @php
                            $profilePicture = $user->profile_picture
                                ? asset('storage/' . $user->profile_picture)
                                : null;
                        @endphp
                        <img 
                            class="w-6 h-6 rounded-full border-2 border-white dark:border-gray-900 cursor-help" 
                            src="{{ $profilePicture ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name ?? 'U') . '&background=16a34a&color=ffffff' }}" 
                            alt="{{ $user->name }}"
                            data-tooltip-target="user-{{ $user->uuid }}"
                        />

                        <x-tooltip id="user-{{ $user->uuid }}" content="{{ $user->name }}" />
                    @endforeach
                </div>
            </div>
            
        </div>
    </x-containers.main>

    {{-- Show 4 active sprints which have the most recent changes --}}
    <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 mt-4">
        @forelse ($shownSprints as $sprint)
            <x-project.dashboard-card
                :sprint="$sprint"
                :projectUuid="$project->uuid"
                wire:key="sprint-{{ $sprint->uuid }}"
            />
        @empty
            <div class="col-span-2 lg:col-span-3 xl:col-span-4 bg-yellow-100 p-4 rounded-lg">
                <p class="text-center text-yellow-800">{{ __('dashboard.messages.no_active_sprints') }}</p>
            </div>
        @endforelse
    </div>

    {{-- Activity Stream --}}
    <div class="text-center text-2xl font-bold text-black dark:text-white mt-6 border-t-2 border-gray-300 dark:border-gray-700 pt-2">
        {{ __('dashboard.labels.activity_stream') }}
    </div>

    @if ($logs->isNotEmpty())
        <div class="mx-auto w-full max-w-6xl">
            <div class="space-y-0">
                @foreach ($logs as $log)
                    <div class="grid min-h-40 grid-cols-[minmax(0,1fr)_6rem_minmax(0,1fr)] items-stretch">
                        {{-- Left side --}}
                        <div class="flex items-center justify-end py-5 pr-8">
                            @if ($loop->odd)
                                <x-project.log-card :log="$log" />
                            @else
                                <time
                                    datetime="{{ $log->created_at->toIso8601String() }}"
                                    class="text-sm font-medium text-gray-500 dark:text-gray-400 border-b border-gray-300 dark:border-gray-700 pb-1"
                                >
                                    {{ $log->created_at->format('d M Y') }}
                                </time>
                            @endif
                        </div>

                        {{-- Center timeline --}}
                        <div class="relative flex items-center justify-center">
                            {{-- Vertical line --}}
                            <div
                                @class([
                                    'absolute left-1/2 w-0.5 -translate-x-1/2 bg-mist-400 dark:bg-gray-700',
                                    'top-1/2 bottom-0' => $loop->first,
                                    'top-0 bottom-1/2' => $loop->last,
                                    'inset-y-0' => ! $loop->first && ! $loop->last,
                                ])
                            ></div>

                            {{-- Time circle --}}
                            <time
                                datetime="{{ $log->created_at->toIso8601String() }}"
                                class="relative z-10 flex size-20 items-center justify-center rounded-full
                                    border-4 border-white bg-blue-600 text-sm font-semibold text-white
                                    shadow-sm ring-1 ring-gray-200
                                    dark:border-gray-900 dark:bg-blue-500 dark:ring-gray-700"
                            >
                                {{ $log->created_at->format('H:i') }}
                            </time>
                        </div>

                        {{-- Right side --}}
                        <div class="flex items-center justify-start py-5 pl-8">
                            @if ($loop->odd)
                                <time
                                    datetime="{{ $log->created_at->toIso8601String() }}"
                                    class="text-sm font-medium text-gray-500 dark:text-gray-400 border-b border-gray-300 dark:border-gray-700 pb-1"
                                >
                                    {{ $log->created_at->format('d M Y') }}
                                </time>
                            @else
                                <x-project.log-card :log="$log" />
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            @if ($hasMoreLogs)
                <div class="mt-8 flex justify-center">
                    <button
                        type="button"
                        wire:click="loadMoreLogs"
                        wire:loading.attr="disabled"
                        class="inline-flex items-center rounded-lg border border-gray-200 bg-white
                            px-5 py-2.5 text-sm font-medium text-gray-900
                            hover:bg-gray-100 focus:outline-none focus:ring-4 focus:ring-gray-100
                            disabled:cursor-not-allowed disabled:opacity-50
                            dark:border-gray-600 dark:bg-gray-800 dark:text-white
                            dark:hover:bg-gray-700 dark:focus:ring-gray-700"
                    >
                        <span wire:loading.remove wire:target="loadMoreLogs">
                            Load more
                        </span>

                        <span
                            wire:loading.flex
                            wire:target="loadMoreLogs"
                            class="items-center gap-2"
                        >
                            <svg
                                class="size-4 animate-spin"
                                aria-hidden="true"
                                viewBox="0 0 24 24"
                                fill="none"
                            >
                                <circle
                                    class="opacity-25"
                                    cx="12"
                                    cy="12"
                                    r="10"
                                    stroke="currentColor"
                                    stroke-width="4"
                                ></circle>

                                <path
                                    class="opacity-75"
                                    fill="currentColor"
                                    d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4Z"
                                ></path>
                            </svg>

                            Loading...
                        </span>
                    </button>
                </div>
            @endif
        </div>
    @else
        <p class="text-center text-gray-600 dark:text-gray-300 mt-4">{{ __('dashboard.messages.no_activity') }}</p>
    @endif
</div>
