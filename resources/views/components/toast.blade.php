<div
    x-data="{
        toasts: [],
        push(type, message) {
            if (!message) return;
            const id = Date.now() + Math.random();
            this.toasts.push({ id, type, message });
            setTimeout(() => this.remove(id), 4000);
        },
        remove(id) {
            this.toasts = this.toasts.filter(t => t.id !== id);
        }
    }"
    @notify.window="push($event.detail.type ?? 'success', $event.detail.message)"
    class="fixed top-6 left-1/2 -translate-x-1/2 z-[9998] flex flex-col items-center gap-2 pointer-events-none w-full max-w-sm px-4"
>
    @if (session('message') || session('success'))
        <div x-init="push('success', @js(session('message') ?? session('success')))"></div>
    @endif
    @if (session('error'))
        <div x-init="push('error', @js(session('error')))"></div>
    @endif

    <template x-for="toast in toasts" :key="toast.id">
        <div
            x-show="true"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-2"
            :class="toast.type === 'success' ? 'bg-blue-600' : 'bg-red-500'"
            class="pointer-events-auto text-white px-5 py-3 rounded-xl shadow-2xl flex items-center gap-3 w-full"
        >
            <template x-if="toast.type === 'success'">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
            </template>
            <template x-if="toast.type === 'error'">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </template>
            <p class="text-sm font-bold" x-text="toast.message"></p>
        </div>
    </template>
</div>
