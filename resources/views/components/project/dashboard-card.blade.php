@props(['projectUuid', 'sprint'])

<div class="col-span-1 bg-white dark:bg-gray-800 shadow-md rounded-lg p-4" wire:key="sprint-{{ $sprint->uuid }}">
    <div class="flex justify-between items-center">
        <a href="{{ route('projects.board.render', ['uuid' => $projectUuid, 'sprintUuid' => $sprint->uuid]) }}" class="text-lg font-bold dark:text-white hover:text-blue-500">{{ $sprint->name }}</a>
    </div>
    
    <div class="mt-1 flex justify-between items-center">
        {{-- Information left - Icons --}}
        <div class="flex gap-2">
            <div class="flex flex-col">
                <div>
                    <p class="text-2xs font-semibold uppercase text-gray-500 dark:text-gray-300">{{ __('archive.labels.cards') }}</p>
                </div>
                <div class="flex gap-2">
                    <i class="fi fi-sr-list-check dark:text-gray-300"></i>
                    <p class="dark:text-gray-300">{{ count($sprint->cards) }}</p>
                </div>
            </div>
        </div>

        {{-- Information right - Date --}}
        @php
            $start = $sprint->start_date;
            $end = $sprint->end_date;
        @endphp

        <div class="flex flex-col items-end">
            <p class="text-2xs font-semibold uppercase text-gray-500 dark:text-gray-300">
                {{ __('archive.labels.duration') }}
            </p>
            <p class="dark:text-gray-300 font-medium">
                @if($start->format('Y') === $end->format('Y'))
                    @if($start->format('M') === $end->format('M'))
                        {{ $start->format('M d') }} – {{ $end->format('d, Y') }}
                    @else
                        {{ $start->format('M d') }} – {{ $end->format('M d, Y') }}
                    @endif
                @else
                    {{ $start->format('M d, Y') }} – {{ $end->format('M d, Y') }}
                @endif
            </p>
        </div>
    </div>

    <div class="my-20">
        <p class="dark:text-white text-center">
            Graph
        </p>
    </div>
</div>