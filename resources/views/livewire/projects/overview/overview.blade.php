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

    {{-- <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th class="px-6 py-3">User ID</th>
                <th class="px-6 py-3">Project ID</th>
                <th class="px-6 py-3">Sprint ID</th>
                <th class="px-6 py-3">Backlog ID</th>
                <th class="px-6 py-3">Card ID</th>
                <th class="px-6 py-3">Task ID</th>
                <th class="px-6 py-3">Action</th>
                <th class="px-6 py-3">Table</th>
                <th class="px-6 py-3">Data</th>
                <th class="px-6 py-3">Description</th>
                <th class="px-6 py-3">Environment</th>
                <th class="px-6 py-3">Created At</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $log)
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                    <td class="px-6 py-4">{{ $log->user_id }}</td>
                    <td class="px-6 py-4">{{ $log->project_id }}</td>
                    <td class="px-6 py-4">{{ $log->sprint_id }}</td>
                    <td class="px-6 py-4">{{ $log->backlog_id }}</td>
                    <td class="px-6 py-4">{{ $log->card_id }}</td>
                    <td class="px-6 py-4">{{ $log->task_id }}</td>
                    <td class="px-6 py-4">{{ $log->action }}</td>
                    <td class="px-6 py-4">{{ $log->table }}</td>
                    <td class="px-6 py-4">
                        <button data-popover-target="popover-{{$log->id}}" type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Data</button>

                        <div data-popover id="popover-{{$log->id}}" class="absolute z-10 invisible inline-block w-64 text-sm text-gray-500 transition-opacity duration-300 bg-white border border-gray-200 rounded-lg shadow-sm opacity-0 dark:text-gray-400 dark:border-gray-600 dark:bg-gray-800">
                            <div class="p-4">
                                {{ $log->data }}
                            </div>
                            <div data-popper-arrow></div>
                        </div>
                    </td>
                    <td class="px-6 py-4">{!! $log->description !!}</td>
                    <td class="px-6 py-4">{{ $log->environment }}</td>
                    <td class="px-6 py-4">{{ $log->created_at }}</td>
                </tr>
            @empty
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                    <td class="px-6 py-4 text-center" colspan="12">No logs found.</td>
                </tr>
            @endforelse
        </tbody>
    </table> --}}
</div>
