<div class="w-full flex justify-center">
    <div class="w-2/3 bg-white dark:bg-gray-800 rounded-lg shadow-md p-4">
        <h2 class="text-lg font-bold dark:text-white">{{ __('bugs.report_a_bug') }}</h2>
        <p class="dark:text-white">{{ __('bugs.intro') }}</p>
        <div class="w-2/3 mt-4">
            <x-label for="description" class="dark:text-white" value="{{ __('bugs.description') }}" />
            <textarea wire:model="description" id="description" rows="10" class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" placeholder="{{ __('bugs.describe_bug')}}"></textarea>
        </div>
        <div class="w-1/3 mt-2">
            <x-label for="page" value="{{ __('bugs.page') }}" class="dark:text-white" />
            <x-input wire:model="page" id="page" class="w-full dark:bg-gray-700 dark:text-white rounded-lg p-2" type="text" placeholder="{{ __('bugs.page') }}" />
        </div>
        <div class="w-1/5 mt-8">
            <x-primary-button wire:click="submit" class="w-full">{{ __('bugs.submit') }}</x-primary-button>
        </div>
    </div>

    <script>
        window.addEventListener('DOMContentLoaded', () => {
            toastr.options.positionClass = 'toast-bottom-right';
            window.addEventListener('notify', event => {
                toastr.success(event.detail[0].message);
            })
        })
    </script>
</div>
