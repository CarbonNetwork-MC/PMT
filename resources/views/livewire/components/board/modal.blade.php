<div class="fixed inset-0 z-50 overflow-y-auto">

    {{-- Background overlay --}}
    <div class="fixed inset-0 bg-gray-900/75"></div>

    {{-- Close Button --}}
    <div class="relative z-50 flex justify-end mr-8 pt-4">
        <div
            wire:click="closeModal"
            class="bg-gray-800 text-white rounded-lg w-8 h-8 flex items-center justify-center cursor-pointer">
            <i class="fi fi-br-cross"></i>
        </div>
    </div>

    {{-- Modal Content --}}
    <div class="relative z-40 flex justify-center w-[85%] h-[90vh] mx-auto">
        <div class="w-full h-full flex flex-col bg-gray-100 dark:bg-gray-800 rounded-sm p-4">
            <div class="flex justify-between">
                {{-- Title --}}
                <div class="flex gap-4 mt-2">
                    <p class="text-gray-600">#{{ $card->id }}</p>
                    <p class="text-gray-400 font-bold">{{ $card->title }}</p>
                </div>

                {{-- Approval Status --}}
                @php
                    $approvalStatus = $card->approval_status;
                    $statusColors = [
                        'approved' => 'bg-green-100 text-green-800',
                        'pending' => 'bg-yellow-100 text-yellow-800',
                        'rejected' => 'bg-red-100 text-red-800',
                    ];
                    $statusColor = $statusColors[$approvalStatus] ?? 'bg-gray-100 text-gray-800';
                @endphp

                <div class="flex items-center gap-x-2">
                    <p class="text-sm font-bold text-gray-600">{{ __('board.labels.approval_status') }}:</p>
                    <div class="px-2 py-1 rounded text-xs font-semibold {{ $statusColor }}">
                        {{ __('board.status.' . $approvalStatus) }}
                    </div>
                </div>

                {{-- Users --}}


                {{-- Actions --}}
                
            </div>
        </div>
    </div>

</div>