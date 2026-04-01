@props(['headers' => [], 'rows' => [], 'pagination' => null])

<div>
    <div class="relative overflow-x-auto bg-neutral-primary-soft shadow-xs rounded-base border border-default">
        <table class="w-full text-sm text-left rtl:text-right text-body">
            <thead class="bg-gray-200 dark:bg-gray-700 border-b border-default">
                {{ $headers ?? '' }}
            </thead>
            <tbody>
                {{ $rows ?? '' }}
            </tbody>
        </table>
    </div>

    @if ($pagination)
        <div class="w-full mt-4 px-2">
            {{ $pagination }}
        </div>
    @endif
</div>