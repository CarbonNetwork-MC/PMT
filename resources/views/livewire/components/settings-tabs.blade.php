<div class="text-sm font-medium text-center text-gray-500 border-b border-gray-200 dark:text-gray-400 dark:border-gray-700">
    <ul class="flex flex-wrap -mb-px">
        <li class="me-2">
            <a href="{{ route('projects.settings.overall.render', ['uuid' => $uuid]) }}" class="{{ request()->routeIs('projects.settings.overall.render') ? 'text-blue-400 border-blue-400' : '' }} inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300">
                Settings
            </a>
        </li>
        <li class="me-2">
            <a href="{{ route('projects.settings.members.render', ['uuid' => $uuid]) }}" class="{{ request()->routeIs('projects.settings.members.render') ? 'text-blue-400 border-blue-400' : '' }} inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300">
                Members
            </a>
        </li>
        @if (auth()->user()->uuid == $owner)
            <li class="me-2">
                <a href="{{ route('projects.settings.admin.render', ['uuid' => $uuid]) }}" class="{{ request()->routeIs('projects.settings.admin.render') ? 'text-blue-400 border-blue-400' : '' }} inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300">
                    Admin
                </a>
            </li>
        @endif
    </ul>
</div>
