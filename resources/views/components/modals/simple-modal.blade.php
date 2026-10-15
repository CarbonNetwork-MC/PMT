@props([
    'maxWidth' => 'max-w-lg',
    'closeOnClickAway' => true,
])

<div
    x-data="{
        show: @entangle($attributes->wire('model')),
        previouslyFocused: null,

        open() {
            this.previouslyFocused = document.activeElement;
            this.show = true;
        },

        close() {
            this.show = false;
            this.$nextTick(() => this.previouslyFocused?.focus());
        }
    }"
    x-init="$watch('show', value => {
        if (value) {
            document.body.classList.add('overflow-hidden');
        } else {
            document.body.classList.remove('overflow-hidden');
        }
    })"
    x-on:keydown.escape.window="close()"
    x-show="show"
    class="fixed inset-0 z-50"
    role="dialog"
    aria-modal="true"
>
    <!-- Overlay -->
    <div
        x-show="show"
        x-transition.opacity
        @if($closeOnClickAway)
            x-on:click="close()"
        @endif
        class="fixed inset-0 bg-gray-900/60"
        aria-hidden="true"
    ></div>

    <!-- Modal container -->
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div
            x-show="show"
            x-transition
            x-trap.inert.noscroll="show"
            @if($closeOnClickAway)
                x-on:click.stop
            @endif
            class="relative w-full {{ $maxWidth }} bg-white dark:bg-gray-700 rounded-lg shadow-lg"
        >
            <!-- Close button -->
            <div class="flex justify-end p-2">
                <button
                    type="button"
                    @click="close()"
                    class="text-gray-400 hover:bg-gray-200 hover:text-gray-900 dark:hover:bg-gray-600 dark:hover:text-white rounded-lg w-8 h-8 flex items-center justify-center focus:outline-none cursor-pointer"
                    aria-label="Close modal"
                >
                    <svg class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                </button>
            </div>

            <!-- Content -->
            <div
                class="px-4 pb-4 md:px-5 md:pb-5"
                x-on:show.window="$nextTick(() => $el.querySelector('[data-autofocus]')?.focus())"
            >
                {{ $slot }}
            </div>
        </div>
    </div>
</div>