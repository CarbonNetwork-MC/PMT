@props(['sprint'])

<div class="col-span-1 bg-white dark:bg-gray-800 shadow-md rounded-lg p-4" wire:key="sprint-{{ $sprint->uuid }}">
    <div class="flex justify-between items-center">
        <a href="" class="text-lg font-bold dark:text-white hover:text-blue-500">{{ $sprint->name }}</a>
        <i class="fi fi-bs-menu-dots dark:text-white cursor-pointer" data-dropdown-toggle="sprint-dropdown-{{ $sprint->uuid }}"></i>

        <div id="sprint-dropdown-{{ $sprint->uuid }}" class="hidden z-10 w-44 bg-white rounded divide-y divide-gray-100 shadow dark:bg-gray-700">
            <ul class="py-1 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownDefaultButton">
                @switch($sprint->status)
                    @case('planned')
                        <li>
                            <a href="#" class="w-full h-full flex gap-2 py-2 px-4 hover:bg-gray-100 hover:text-blue-400 dark:hover:bg-gray-600">
                                <i class="fi fi-br-play-circle"></i>
                                {{ __('sprints.buttons.start_sprint') }}
                            </a>
                        </li>
                        @break
                    @case('active')
                        <li>
                            <a href="#" class="w-full h-full flex gap-2 py-2 px-4 hover:bg-gray-100 hover:text-blue-400 dark:hover:bg-gray-600">
                                <i class="fi fi-br-stop-circle"></i>
                                {{ __('sprints.buttons.complete_sprint') }}
                            </a>
                        </li>
                        @break
                    @case('completed')
                        <li>
                            <a href="#" class="w-full h-full flex gap-2 py-2 px-4 hover:bg-gray-100 hover:text-blue-400 dark:hover:bg-gray-600">
                                <i class="fi fi-sr-box"></i>
                                {{ __('sprints.buttons.archive_sprint') }}
                            </a>
                        </li>
                        @break
                @endswitch

                <li>
                    <a href="#" class="w-full h-full flex gap-2 py-2 px-4 hover:bg-gray-100 hover:text-blue-400 dark:hover:bg-gray-600">
                        <i class="fi fi-rs-pencil"></i>
                        {{ __('sprints.buttons.edit_sprint') }}
                    </a>
                </li>
                <li>
                    <a href="#" class="w-full h-full flex gap-2 py-2 px-4 hover:bg-gray-100 hover:text-red-400 dark:hover:bg-gray-600">
                        <i class="fi fi-rs-trash"></i>
                        {{ __('sprints.buttons.delete_sprint') }}
                    </a>
                </li>
            </ul>
        </div>
    </div>
    
    <div class="mt-1">
        <div class="flex gap-2">
            <div class="flex flex-col">
                <div>
                    <p class="text-2xs font-semibold uppercase text-gray-500 dark:text-gray-300">
                        @switch($sprint->status)
                            @case('planned')
                                {{ __('sprints.labels.days_to_start') }}
                                @break
                            @case('active')
                                {{ __('sprints.labels.days_left') }}
                                @break
                            @case('completed')
                                {{ __('sprints.labels.done') }}
                                @break
                        @endswitch
                    </p>
                </div>
                <div class="flex justify-center gap-2">
                    @switch ($sprint->status)
                        @case('planned')
                            @php
                                $diffInDays = now()->startOfDay()->diffInDays($sprint->start_date->startOfDay(), false);
                                $class = $diffInDays <= 0 ? 'text-red-600 dark:text-red-400' : 'dark:text-gray-300';
                            @endphp
                            <i class="fi fi-sr-clock-five dark:text-gray-300"></i>
                            <p class="{{ $class }}">
                                {{ $diffInDays }}
                            </p>
                            @break
                        @case('active')
                            @php
                                $diffInDays = now()->startOfDay()->diffInDays($sprint->end_date->startOfDay(), false);
                                $class = $diffInDays <= 0 ? 'text-red-600 dark:text-red-400' : 'dark:text-gray-300';
                            @endphp
                            <i class="fi fi-sr-clock-five dark:text-gray-300"></i>
                            <p class="{{ $class }}">
                                {{ $diffInDays }}
                            </p>
                            @break
                        @case('completed')
                            <i class="fi fi-sr-calendar-check dark:text-gray-300"></i>
                            @break
                    @endswitch
                </div>
            </div>
            <div class="flex flex-col">
                <div>
                    <p class="text-2xs font-semibold uppercase text-gray-500 dark:text-gray-300">{{ __('sprints.labels.cards') }}</p>
                </div>
                <div class="flex gap-2">
                    <i class="fi fi-sr-list-check dark:text-gray-300"></i>
                    <p class="dark:text-gray-300">{{ count($sprint->cards) }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="my-20">
        <p class="dark:text-white text-center">
            Graph
        </p>
    </div>
</div>