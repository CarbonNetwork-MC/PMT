<div class="flex flex-col h-[90vh]">
    <x-slot name="title">
        {{ __('general.overview') }}
    </x-slot>

    <div class="w-full bg-white dark:bg-gray-800 shadow-md rounded-lg p-4">
        <p class="text-lg font-bold dark:text-white">{{ $selectedProject->name }}</p>
        <div class="w-full flex justify-between mt-2">
            <div class="w-full flex gap-x-4">
                <div>
                    <p class="text-sm font-bold uppercase dark:text-white">{{ __('general.sprints') }}</p>
                    <div class="flex gap-x-2 text-sm">
                        <i class="fi fi-sr-running dark:text-white"></i>
                        <p class="dark:text-white">{{ $selectedProject->sprints->count() }}</p>
                    </div>
                </div>
                <div>
                    <p class="text-sm font-bold uppercase dark:text-white">{{ __('general.members') }}</p>
                    <div class="flex gap-x-2 text-sm">
                        <i class="fi fi-sr-users dark:text-white"></i>
                        <p class="dark:text-white">{{ $selectedProject->members->count() }}</p>
                    </div>
                </div>
                @if ($selectedProject->owner_id === auth()->user()->uuid)
                    <div>
                        <p class="text-sm font-bold uppercase dark:text-white">{{ __('general.owning') }}</p>
                        <div class="flex justify-center text-sm">
                            <i class="fi fi-sr-user-crown dark:text-white"></i>
                        </div>
                    </div>
                @endif
            </div>
            <div class="w-full flex justify-end gap-x-2">
                @foreach ($selectedProject->members as $member)
                    <div class="flex items-center gap-x-2">
                        <img class="w-6 h-6 rounded-full" src="{{ $member->user->profile_photo_url }}" alt="{{ $member->user->name }}">
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="w-full grid grid-cols-3 gap-x-2 mt-4">
        @foreach ($selectedProject->sprints->where('status', 'active') as $sprint)
            <div class="w-full bg-white dark:bg-gray-800 shadow-md rounded-lg p-4">
                <a href="{{ route('projects.board.render', ['uuid' => $sprint->uuid]) }}" class="text-lg font-bold hover:text-sky-500 dark:text-white">{{ $sprint->name }}</a>
                <div class="w-full flex justify-between mt-2">
                    <div class="w-full flex gap-x-4">
                        <div>
                            <p class="text-sm font-bold uppercase dark:text-white">{{ __('general.cards') }}</p>
                            <div class="flex gap-x-2 text-sm">
                                <i class="fi fi-ss-list-check dark:text-white"></i>
                                <p class="dark:text-white">{{ $sprint->cards->count() }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="w-full flex justify-end gap-x-2">
                        <div class="flex items-center gap-x-2">
                            <i class="fi fi-sr-calendar dark:text-white"></i>
                            <p class="dark:text-white">{{ $sprint->start_date }} - {{ $sprint->end_date }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <hr class="{{ $numberOfActiveSprints <= 3 ? 'mt-80' : '' }} {{ $numberOfActiveSprints > 3 && $numberOfActiveSprints <= 6 ? 'mt-45' : '' }} mb-4 border-gray-300 dark:border-gray-700">

    <div class="w-full flex justify-center">
        <div class="w-3/5 flex flex-col items-center bg-white dark:bg-gray-800 rounded-lg shadow-md p-4">
            <p class="font-bold text-lg dark:text-white">{{ __('general.activity') }}</p>
            @if ($logs->count() > 0)
                <div>
                    <ol class="relative w-full border-s border-gray-200 dark:bg-gray-700 mt-8">
                        @foreach ($logs as $log)
                            <li class="mb-10 ms-6">
                                <span class="absolute flex items-center justify-center w-6 h-6 bg-blue-100 rounded-full -start-3 ring-8 ring-white dark:ring-gray-900 dark:bg-blue-900">
                                    <img src="{{ $log->user->profile_photo_url }}" alt="" class="rounded-full shadow-lg">
                                </span>
                                <div class="sm:flex items-center justify-between gap-x-4 p-4 bg-gray-100 border border-gray-200 rounded-lg shadow-sm dark:bg-gray-700 dark:border-gray-600">
                                    <time class="mb-1 text-xs font-normal text-gray-700 sm:order-last sm:mb-0">
                                        @if (\Carbon\Carbon::parse($log->created_at)->diffInDays(\Carbon\Carbon::now()) < 1)
                                            {{ \Carbon\Carbon::parse($log->created_at)->diffForHumans() }}
                                        @else
                                            {{ \Carbon\Carbon::parse($log->created_at)->format('Y-m-d') }}
                                        @endif
                                    </time>
                                    <div class="text-sm font-normal text-gray-500 dark:text-gray-300">{!! $log->description !!}</div>
                                </div>
                            </li>
                        @endforeach
                    </ol>
                </div>
            @else
                <div class="flex items-center justify-center w-1/2 ">
                    <p class="text-lg font-bold dark:text-white">{{ __('general.no_activity') }}</p>
                </div>
            @endif
            {{-- Button - load more --}}
            @if ($allLogs->count() > 10)
                <div class="mb-4">
                    <button wire:click="loadMoreLogs" class="flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-blue-500 border border-transparent rounded-md shadow-sm dark:bg-blue-600 hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:hover:bg-blue-700 dark:focus:ring-blue-600">
                        <i class="fi fi-sr-arrow-down text-xl"></i>
                        <span class="pl-2">{{ __('general.load_more') }}</span>
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>
