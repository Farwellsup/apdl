@props(['position' => 'top-right'])

<div x-data="toastManager()" @toast.window="addToast($event.detail)" class="fixed z-50 pointer-events-none
            @if($position === 'top-right') top-4 right-4
            @elseif($position === 'top-left') top-4 left-4
            @elseif($position === 'bottom-right') bottom-4 right-4
            @elseif($position === 'bottom-left') bottom-4 left-4
            @elseif($position === 'top-center') top-4 left-1/2 -translate-x-1/2
            @elseif($position === 'bottom-center') bottom-4 left-1/2 -translate-x-1/2
            @endif">
    <div class="flex flex-col gap-2 w-80">
        <template x-for="toast in toasts" :key="toast.id">
            <div x-show="toast.show" x-transition:enter="transform transition ease-out duration-300"
                x-transition:enter-start="translate-x-full opacity-0" x-transition:enter-end="translate-x-0 opacity-100"
                x-transition:leave="transform transition ease-in duration-200"
                x-transition:leave-start="translate-x-0 opacity-100" x-transition:leave-end="translate-x-full opacity-0"
                class="pointer-events-auto shadow-lg overflow-hidden bg-white border border-gray-200">
                <div class="p-4 flex items-start gap-3">
                    <!-- Icon -->
                    <div class="flex-shrink-0">
                        <template x-if="toast.type === 'success'">
                            <svg class="w-5 h-5" style="color: #1F3B67;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </template>
                        <template x-if="toast.type === 'error'">
                            <svg class="w-5 h-5" style="color: #FFB901;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </template>
                        <template x-if="toast.type === 'warning'">
                            <svg class="w-5 h-5" style="color: #FFB901;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                </path>
                            </svg>
                        </template>
                        <template x-if="toast.type === 'info'">
                            <svg class="w-5 h-5" style="color: #1F3B67;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </template>
                    </div>

                    <!-- Content -->
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold" :class="{
                               'text-[#1F3B67]': toast.type === 'info',
                               'text-[#1F3B67]': toast.type === 'success',
                               'text-[#1F3B67]': toast.type === 'error',
                               'text-[#1F3B67]': toast.type === 'warning'
                           }" x-text="toast.title"></p>
                        <p x-show="toast.message" class="text-sm mt-1 text-[#1F3B67]" x-text="toast.message"></p>
                    </div>

                    <!-- Close Button -->
                    <button @click="removeToast(toast.id)" class="flex-shrink-0 rounded-lg p-1 transition-colors hover:bg-gray-200 text-#1F3B67]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Progress Bar -->
                <div x-show="toast.duration > 0" class="h-1 bg-gray-200">
                    <div class="h-full transition-all ease-linear" :class="{
                             'bg-blue-500': toast.type === 'info',
                             'bg-green-500': toast.type === 'success',
                             'bg-red-500': toast.type === 'error',
                             'bg-yellow-500': toast.type === 'warning'
                         }" :style="`width: ${toast.progress}%; transition-duration: ${toast.duration}ms`"></div>
                </div>
            </div>
        </template>
    </div>
</div>

<script>
    function toastManager() {
        return {
            toasts: [],

            addToast(toast) {
                const id = Date.now();
                const duration = toast.duration || 5000;

                const newToast = {
                    id,
                    type: toast.type || 'info',
                    title: toast.title || '',
                    message: toast.message || '',
                    duration,
                    progress: 100,
                    show: false
                };

                this.toasts.push(newToast);

                // Trigger animation
                setTimeout(() => {
                    const index = this.toasts.findIndex(t => t.id === id);
                    if (index !== -1) {
                        this.toasts[index].show = true;
                    }
                }, 100);

                // Start progress bar
                if (duration > 0) {
                    setTimeout(() => {
                        const index = this.toasts.findIndex(t => t.id === id);
                        if (index !== -1) {
                            this.toasts[index].progress = 0;
                        }
                    }, 100);

                    // Auto remove
                    setTimeout(() => {
                        this.removeToast(id);
                    }, duration + 100);
                }
            },

            removeToast(id) {
                const index = this.toasts.findIndex(t => t.id === id);
                if (index !== -1) {
                    this.toasts[index].show = false;
                    setTimeout(() => {
                        this.toasts = this.toasts.filter(t => t.id !== id);
                    }, 300);
                }
            }
        }
    }
</script>