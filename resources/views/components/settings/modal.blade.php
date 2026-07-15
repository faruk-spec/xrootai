@props([
    'id',
    'title' => null,
    'subtitle' => null,
    'icon' => null,
    'width' => 'max-w-xl',
])

<div x-data="{ open: false }"
     @open-modal-{{ $id }}.window="open = true"
     @close-modal-{{ $id }}.window="open = false"
     @keydown.escape.window="open = false"
     x-show="open"
     class="fixed inset-0 z-50 overflow-y-auto"
     aria-labelledby="modal-title-{{ $id }}"
     role="dialog"
     aria-modal="true"
     style="display: none;">
    
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <!-- Backdrop -->
        <div x-show="open"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 transition-opacity backdrop-blur-md"
             style="background: rgba(15, 18, 29, 0.65);"
             @click="open = false"
             aria-hidden="true"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <!-- Modal Dialog -->
        <div x-show="open"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="inline-block align-bottom text-left overflow-hidden rounded-[28px] shadow-2xl transform transition-all sm:my-8 sm:align-middle w-full {{ $width }} border"
             style="background: var(--clay-card-bg); border-color: var(--clay-card-border); box-shadow: var(--clay-outer-shadow);">
            
            @if($title || isset($header))
                <div class="px-6 py-5 border-b flex items-center justify-between gap-4" style="border-color: var(--clay-card-border);">
                    <div class="flex items-center gap-3.5">
                        @if($icon)
                            <div class="w-11 h-11 rounded-2xl flex items-center justify-center shrink-0" style="background: var(--clay-input-bg); color: var(--accent);">
                                {!! $icon !!}
                            </div>
                        @endif
                        <div>
                            <h3 class="text-lg font-bold tracking-tight" id="modal-title-{{ $id }}" style="color: var(--text-primary);">
                                {{ $title ?? $header }}
                            </h3>
                            @if($subtitle)
                                <p class="text-xs mt-0.5" style="color: var(--text-secondary);">{{ $subtitle }}</p>
                            @endif
                        </div>
                    </div>
                    <button type="button"
                            @click="open = false"
                            class="w-9 h-9 rounded-xl flex items-center justify-center transition-transform hover:scale-110"
                            style="background: var(--clay-input-bg); color: var(--text-secondary);">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            @endif

            <div class="p-6 sm:p-8 space-y-5 max-h-[75vh] overflow-y-auto">
                {{ $slot }}
            </div>

            @if(isset($footer))
                <div class="px-6 py-4 border-t flex flex-wrap items-center justify-end gap-3" style="border-color: var(--clay-card-border); background: var(--clay-input-bg);">
                    {{ $footer }}
                </div>
            @endif
        </div>
    </div>
</div>
