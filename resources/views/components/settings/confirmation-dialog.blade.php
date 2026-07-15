@props([
    'id',
    'title',
    'message',
    'confirmText' => 'Confirm',
    'cancelText' => 'Cancel',
    'variant' => 'danger', // danger, warning, accent
])

<div x-data="{ open: false }"
     @confirm-{{ $id }}.window="open = true"
     @close-confirm-{{ $id }}.window="open = false"
     @keydown.escape.window="open = false"
     x-show="open"
     class="fixed inset-0 z-50 overflow-y-auto"
     aria-labelledby="confirm-title-{{ $id }}"
     role="dialog"
     aria-modal="true"
     style="display: none;">
    
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <!-- Backdrop -->
        <div x-show="open"
             x-transition:enter="ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 transition-opacity backdrop-blur-md"
             style="background: rgba(15, 18, 29, 0.7);"
             @click="open = false"
             aria-hidden="true"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <!-- Dialog Box -->
        <div x-show="open"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="inline-block align-bottom text-left overflow-hidden rounded-[28px] shadow-2xl transform transition-all sm:my-8 sm:align-middle max-w-md w-full p-6 sm:p-8 border"
             style="background: var(--clay-card-bg); border-color: var(--clay-card-border); box-shadow: var(--clay-outer-shadow);">
            
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center shrink-0"
                     :style="`{{ $variant === 'danger' ? 'background: rgba(239, 68, 68, 0.15); color: var(--danger);' : ($variant === 'warning' ? 'background: rgba(245, 158, 11, 0.15); color: var(--warning);' : 'background: rgba(74, 136, 255, 0.15); color: var(--accent);') }}`">
                    @if($variant === 'danger')
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    @elseif($variant === 'warning')
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    @else
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    @endif
                </div>
                <div>
                    <h3 class="text-lg font-bold tracking-tight" id="confirm-title-{{ $id }}" style="color: var(--text-primary);">
                        {{ $title }}
                    </h3>
                    <p class="text-sm mt-1.5 leading-relaxed" style="color: var(--text-secondary);">
                        {{ $message }}
                    </p>
                </div>
            </div>

            <div class="mt-8 pt-5 border-t flex items-center justify-end gap-3" style="border-color: var(--clay-card-border);">
                <button type="button"
                        @click="open = false"
                        class="px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200"
                        style="color: var(--text-primary); background: var(--clay-input-bg);">
                    {{ $cancelText }}
                </button>
                <div @click="open = false">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
</div>
