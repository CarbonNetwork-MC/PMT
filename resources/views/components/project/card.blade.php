@props(['project'])

<a 
    href="{{ route('projects.dashboard.render', ['uuid' => $project->uuid]) }}" 
    class="col-span-1 block max-w-sm p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700"
>
    
    <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">{{ $project->name }}</h5>
    <div class="flex gap-x-4">
        <div class="flex">
            <i class="fi fi-sr-running text-xl dark:text-white"></i>
            <p class="ml-2 text-gray-800 dark:text-white">{{ $project->sprints->count() }}</p>
        </div>
        <div class="flex">
            <i class="fi fi-sr-users-alt dark:text-white"></i>
            <p class="ml-2 text-gray-800 dark:text-white">{{ $project->members->count() }}</p>
        </div>
    </div>
</a>
