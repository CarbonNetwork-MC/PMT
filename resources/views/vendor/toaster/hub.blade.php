<div role="status" id="toaster" x-data="toasterHub(@js($toasts), @js($config))" @class([
    'fixed z-50 p-4 w-full flex flex-col pointer-events-none sm:p-6',
    'bottom-0' => $alignment->is('bottom'),
    'top-1/2 -translate-y-1/2' => $alignment->is('middle'),
    'top-0' => $alignment->is('top'),
    'items-start rtl:items-end' => $position->is('left'),
    'items-center' => $position->is('center'),
    'items-end rtl:items-start' => $position->is('right'),
 ])>
    <template x-for="toast in toasts" :key="toast.id">
        <div x-show="toast.isVisible"
             x-init="$nextTick(() => toast.show($el))"
             @if($alignment->is('bottom'))
             x-transition:enter-start="translate-y-12 opacity-0"
             x-transition:enter-end="translate-y-0 opacity-100"
             @elseif($alignment->is('top'))
             x-transition:enter-start="-translate-y-12 opacity-0"
             x-transition:enter-end="translate-y-0 opacity-100"
             @else
             x-transition:enter-start="opacity-0 scale-90"
             x-transition:enter-end="opacity-100 scale-100"
             @endif
             x-transition:leave-end="opacity-0 scale-90"
             @class(['mt-2 relative duration-300 transform transition ease-in-out max-w-xs w-full pointer-events-auto', 'text-center' => $position->is('center')])
             :class="toast.select({ error: 'text-white', info: 'text-black', success: 'text-white', warning: 'text-white' })"
        >
            <div class="flex items-center w-full max-w-xs p-4 rounded-base shadow-xs border border-default bg-gray-100 text-black">
                <!-- Icon -->
                <div
                    class="flex-shrink-0"
                    :class="toast.select({
                        success: 'text-fg-success',
                        info: 'text-blue-500',
                        error: 'text-fg-danger',
                        warning: 'text-fg-warning'
                    })"
                >
                    <template x-if="toast.type === 'success'">
                        <i class="fi fi-rr-check-circle"></i>
                    </template>

                    <template x-if="toast.type === 'info'">
                        <i class="fi fi-rr-info"></i>
                    </template>

                    <template x-if="toast.type === 'error'">
                        <i class="fi fi-rr-cross-circle"></i>
                    </template>

                    <template x-if="toast.type === 'warning'">
                        <i class="fi fi-rr-triangle-warning"></i>
                    </template>
                </div>

                <!-- Message -->
                <div class="ms-2.5 text-sm border-s border-default ps-3.5" x-text="toast.message"></div>

                <!-- Close -->
                <button @click="toast.dispose()"
                    class="ms-auto flex items-center justify-center h-8 w-8 rounded hover:bg-gray-300 focus:ring-4 focus:ring-neutral-tertiary cursor-pointer"
                    aria-label="@lang('close')"
                >
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none">
                        <path stroke="currentColor" stroke-width="2" d="M6 18 18 6M18 18 6 6"/>
                    </svg>
                </button>
            </div>
        </div>
    </template>
</div>