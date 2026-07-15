@props([
    'id' => 'settings-toast',
])

<div x-data="{
        toasts: [],
        add(message, type = 'success', duration = 4000) {
            const id = Date.now();
            this.toasts.push({ id, message, type });
            setTimeout(() => {
                this.remove(id);
            }, duration);
        },
        remove(id) {
            this.toasts = this.toasts.filter(t => t.id !== id);
        }
     }"
     @notify.window="add($event.detail.message, $event.detail.type || 'success', $event.detail.duration || 4000)"
     class="fixed bottom-6 right-6 z-50 flex flex-col gap-3 pointer-events-none max-w-sm w-full">
    
    <template x-for="toast in toasts" :key="toast.id">
        <div x-show="true"
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="opacity-0 translate-y-3 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-3 scale-95"
             class="pointer-events-auto rounded-2xl p-4 shadow-2xl flex items-start justify-between gap-3 border backdrop-blur-xl"
             :style="toast.type === 'error'
                 ? 'background: rgba(239, 68, 68, 0.9); border-color: rgba(255,255,255,0.2); color: #ffffff;'
                 : (toast.type === 'warning'
                     ? 'background: rgba(245, 158, 11, 0.9); border-color: rgba(255,255,255,0.2); color: #ffffff;'
                     : 'background: var(--clay-card-bg); border-color: var(--clay-card-border); color: var(--text-primary); box-shadow: var(--clay-outer-shadow);')">
            
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0"
                     :style="toast.type === 'error' || toast.type === 'warning'
                         ? 'background: rgba(255,255,255,0.2); color: #ffffff;'
                         : 'background: rgba(16, 185, 129, 0.15); color: var(--success);'">
                    <template x-if="toast.type === 'error'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </template>
                    <template x-if="toast.type !== 'error'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    </template>
                </div>
                <span class="text-sm font-semibold tracking-tight" x-text="toast.message"></span>
            </div>

            <button type="button" @click="remove(toast.id)" class="opacity-70 hover:opacity-100 transition-opacity">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </template>
</div>
