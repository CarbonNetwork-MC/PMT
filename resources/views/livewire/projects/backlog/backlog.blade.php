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
                    <i class="fi fi-ss-membership-vip"></i>
                    <p class="dark:text-white">{{ $numOfCards }}</p>
                </div>
            </div>
        </div>
    </div>
    <div class="w-flex flex flex-grow gap-x-4 mt-4">
        <div class="w-1/4 bg-white dark:bg-gray-800 shadow-md rounded-lg p-4">
        
        </div>
        <div class="w-3/4 bg-white dark:bg-gray-800 shadow-md rounded-lg p-4">

        </div>
    </div>
</div>

{{-- $2y$12$zxdodQ1Aj3wkSElXFmBDAOv/JlLTPAekJAqGBdJq6Ws2AzqqSNktK --}}