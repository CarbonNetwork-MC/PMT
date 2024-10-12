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

    <div class="w-full grid grid-cols-3 mt-4">
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

    <hr class="{{ $numberOfActiveSprints <= 3 ? 'mt-80' : '' }} {{ $numberOfActiveSprints > 3 && $numberOfActiveSprints <= 6 ? 'mt-45' : '' }} mb-4 border-gray-200 dark:border-gray-700">

    <div class="w-full">
        <div class="flex justify-center">
            <p class="font-bold text-lg dark:text-white">{{ __('general.activity') }}</p>
        </div>
    </div>
</div>
