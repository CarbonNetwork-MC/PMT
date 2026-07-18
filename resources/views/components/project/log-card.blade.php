@props(['log'])

<article
    class="w-full max-w-md rounded-lg border border-gray-200 bg-white p-5 shadow-sm
           dark:border-gray-700 dark:bg-gray-800"
>
    <div class="mb-3 flex items-start justify-between gap-4">
        <div class="flex min-w-0 items-center gap-3">
            @php
                $profilePicture = $log->user?->profile_photo_path
                    ? asset('storage/' . $log->user?->profile_photo_path)
                    : null;
            @endphp
            <img
                src="{{ $profilePicture ?? 'https://ui-avatars.com/api/?name=' . urlencode($log->user?->name ?? 'U') . '&background=16a34a&color=ffffff' }}"
                alt="{{ $log->user?->name ?? 'System' }}"
                class="size-9 rounded-full object-cover"
            >

            <div class="min-w-0">
                <p class="truncate text-sm font-semibold text-gray-900 dark:text-white">
                    {{ $log->user?->name ?? 'System' }}
                </p>

                <p class="text-xs text-gray-500 dark:text-gray-400">
                    {{ $log->created_at->diffForHumans() }}
                </p>
            </div>
        </div>

        @if ($log->event)
            <span
                class="rounded-sm bg-blue-100 px-2.5 py-0.5 text-xs font-medium
                       text-blue-800 dark:bg-blue-900 dark:text-blue-300"
            >
                {{ ucfirst($log->event) }}
            </span>
        @endif
    </div>

    <p class="text-sm leading-6 text-gray-600 dark:text-gray-300">
        {!! $log->description !!}
    </p>
</article>