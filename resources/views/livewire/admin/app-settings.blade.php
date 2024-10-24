<div class="flex flex-col h-[90vh]">
    <x-slot name="title">
        {{ __('admin.app_settings') }}
    </x-slot>

    <div class="w-full bg-white dark:bg-gray-800 shadow-md rounded-lg p-4">
        <p class="text-lg font-bold dark:text-white">{{ __('admin.app_settings') }}</p>
        <div class="w-full mt-8">
            @foreach ($settings as $setting)
                <div class="grid grid-cols-3">
                    <div class="col-span-1 flex items-center">
                        <p class="text-sm font-bold uppercase dark:text-white">{{ $setting->key }}</p>
                    </div>
                    @switch($setting->type)
                        @case('string')
                            <div class="col-span-1">
                                <input type="text" 
                                    wire:model.lazy="settingsValues.{{ $setting->id }}"
                                    wire:keydown.enter="saveSetting('{{ $setting->id }}')"
                                    class="w-full p-2 border border-gray-300 rounded-lg dark:bg-gray-700 dark:text-white">
                            </div>
                            @break
                        @case('text')
                            <div class="col-span-2">
                                <textarea wire:model.lazy="settingsValues.{{ $setting->id }}"
                                    wire:keydown.enter="saveSetting('{{ $setting->id }}')"
                                    class="w-full p-2 border border-gray-300 rounded-lg dark:bg-gray-700 dark:text-white"></textarea>
                            </div>
                            @break
                    @endswitch
                </div>
            @endforeach
        </div>
    </div>
</div>
