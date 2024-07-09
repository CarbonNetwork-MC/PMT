<div>
    @if ($isSprint)
        Sprint board
    @else
        <div class="w-full bg-white dark:bg-gray-800 shadow-sm rounded-md p-8">
            @if ($activeSprints->count() > 0)
                <p class="text-center text-lg font-semibold text-red-500">
                    This is a project ID, how did you get here?!
                </p>
            @else
                <p class="text-center text-lg font-semibold dark:text-white">
                    There are no active sprints for this project.
                </p>
            @endif
        </div>
    @endif
</div>
