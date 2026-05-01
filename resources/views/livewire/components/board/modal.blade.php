<div class="fixed inset-0 z-50 overflow-y-auto">

    {{-- Background overlay --}}
    <div class="fixed inset-0 bg-gray-900 opacity-75"></div>

    {{-- Close Button --}}
    <div class="relative z-50 flex justify-end mr-8 pt-4">
        <div
            wire:click="closeModal"
            class="bg-gray-800 text-white rounded-lg w-8 h-8 flex items-center justify-center cursor-pointer">
            <i class="fi fi-br-cross"></i>
        </div>
    </div>

    {{-- Modal Content --}}
    <div class="relative z-40 flex justify-center pt-16 px-4 w-[80%] mx-auto">
        <div class="w-full bg-white rounded-lg shadow-lg">
            <p class="text-red-500 text-9xl">
                {{ $card->name }}
            </p>
        </div>
    </div>

</div>