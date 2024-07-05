<div>
    <div class="flex justify-end items-center mb-6">
        <a href="{{ route('projects.create') }}" class="flex items-center px-3 py-1.5 bg-blue-500 text-white rounded-md hover:bg-blue-600">
            <i class="fi fi-sr-plus"></i>
            <span class="ml-2">Create Project</span>
        </a>
    </div>

    <div class="grid grid-cols-4">
        @foreach ($projects as $project)
            <a href="{{ route('projects.board.render', ['uuid' => $project->uuid]) }}" class="col-span-1 block max-w-sm p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700">

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
        @endforeach
    </div>
</div>
