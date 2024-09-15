<div class="flex flex-col" style="height: 90vh">
    <div class="w-full flex justify-between bg-white dark:bg-gray-800 shadow-md rounded-lg p-4">
        <div class="flex gap-x-4">
            <div>
                <p class="text-sm font-bold uppercase dark:text-white">Buckets</p>
                <div class="flex gap-x-2">
                    <i class="fi fi-sr-bucket dark:text-white"></i>
                    <p class="dark:text-white">{{ count($buckets) }}</p>
                </div>
            </div>
            <div>
                <p class="text-sm font-bold uppercase dark:text-white">Cards Total</p>
                <div class="flex gap-x-2">
                    <i class="fi fi-ss-membership-vip dark:text-white"></i>
                    <p class="dark:text-white">{{ $numOfCards }}</p>
                </div>
            </div>
        </div>
    </div>
    <div class="w-flex flex flex-grow gap-x-4 mt-4">
        <div class="w-1/4 bg-white dark:bg-gray-800 shadow-md rounded-lg p-4">
            <div class="flex justify-between">
                <p class="text-lg font-bold dark:text-white">Buckets</p>
                <button wire:click="$toggle('createBucketModal')" class="text-3xl">
                    <i class="fi fi-sr-plus-small dark:text-white"></i>
                </button>
            </div>

            {{-- Buckets --}}
            <div class="flex">
                <ul class="w-full">
                    @foreach ($buckets as $bucket)
                        <li class="flex justify-between items-center p-2 bg-gray-100 dark:bg-gray-700 rounded-lg mt-2 cursor-pointer" wire:click="selectBucket({{$bucket->id}})">
                            <div class="flex items-center gap-x-2">
                                <p class="dark:text-white">{{ $bucket->name }}</p>
                            </div>
                            <div class="flex gap-x-2 items-center">
                                <button wire:click="editBucket({{ $bucket->id }})">
                                    <i class="fi fi-br-edit dark:text-white items-center"></i>
                                </button>
                                <button wire:click="deleteBucket({{ $bucket->id }})">
                                    <i class="fi fi-br-trash dark:text-white items-center"></i>
                                </button>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
        <div class="w-3/4 bg-white dark:bg-gray-800 shadow-md rounded-lg p-4">
            @if ($selectedBucket)
                @foreach ($selectedBucket->cards as $card)
                    <div class="bg-gray-100 dark:bg-gray-700 rounded-lg p-2 mt-2 cursor-pointer" wire:click="selectCard({{$card->id}})">
                        <div class="flex justify-between items-center">
                            <div class="flex items-center gap-x-2">
                                <p class="dark:text-white">{{ $card->name }}</p>
                            </div>
                            <div class="flex gap-x-2 items-center">
                                <button wire:click="editCard({{ $card->id }})">
                                    <i class="fi fi-br-edit dark:text-white items-center"></i>
                                </button>
                                <button wire:click="deleteCard({{ $card->id }})">
                                    <i class="fi fi-br-trash dark:text-white items-center"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>

    {{-- Create Bucket Modal --}}
    <x-pmt-modal wire:model="createBucketModal">
        <x-slot name="title">
            {{ __('backlog.create_bucket') }}
        </x-slot>

        <x-slot name="content">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="col-span-2 w-50">
                    <x-label for="name" value="{{ __('backlog.name') }}" />
                    <x-input id="name" type="text" class="mt-1 block w-full" wire:model.defer="bucketName" />
                    <x-input-error for="bucketName" class="mt-2" />
                </div>
                <div class="col-span-2">
                    <x-label for="description" value="{{ __('backlog.description') }}" />
                    <x-textarea id="description" class="mt-1 block w-full" wire:model.defer="bucketDescription" />
                    <x-input-error for="bucketDescription" class="mt-2" />
                </div>
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-primary-button wire:click="createBucket" wire:loading.attr="disabled">
                {{ __('backlog.create') }}
            </x-primary-button>
            <x-secondary-button wire:click="$toggle('createBucketModal')" wire:loading.attr="disabled">
                {{ __('backlog.cancel') }}
            </x-secondary-button>
        </x-slot>
    </x-pmt-modal>

</div>