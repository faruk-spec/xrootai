@props([
    'id',
    'title' => null,
    'subtitle' => null,
    'position' => 'right', // left, right
    'width' => 'max-w-md',
])

<div x-data="{ open: false }"
     @open-drawer-{{ $id }}.window="open = true"
     @close-drawer-{{ $id }}.window="open = false"
     @keydown.escape.window="open = false"
     x-show="open"
     class="fixed inset-0 z-50 overflow-hidden"
     aria-labelledby="drawer-title-{{ $id }}"
     role="dialog"
     aria-modal="true"
     style="display: none;">
    
    <div class="absolute inset-0 overflow-hidden">
        <div x-show="open"
             x-transition:enter="ease-in-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in-out duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="absolute inset-0 transition-opacity backdrop-blur-sm"
             style="background: rgba(15, 18, 29, 0.6);"
             @click="open = false"
             aria-hidden="true"></div>

        <div class="fixed inset-y-0 {{ $position === 'left' ? 'left-0 pr-10' : 'right-0 pl-10' }} max-w-full flex">
            <div x-show="open"
                 x-transition:enter="transform transition ease-in-out duration-300"
                 x-transition:enter-start="{{ $position === 'left' ? '-translate-x-full' : 'translate-x-full' }}"
                 x-transition:enter-end="translate-x-0"
                 x-transition:leave="transform transition ease-in-out duration-300"
                 x-transition:leave-start="translate-x-0"
                 x-transition:leave-end="{{ $position === 'left' ? '-translate-x-full' : 'translate-x-full' }}"
                 class="w-screen {{ $width }}">
                <div class="h-full flex flex-col py-6 shadow-2xl overflow-y-scroll border-l"
                     style="background: var(--clay-card-bg); border-color: var(--clay-card-border); backdrop-filter: blur(24px);">
                    
                    <div class="px-6 pb-4 border-b flex items-center justify-between" style="border-color: var(--clay-card-border);">
                        <div>
                            @if($title)
                                <h3 class="text-lg font-bold tracking-tight" id="drawer-title-{{ $id }}" style="color: var(--text-primary);">{{ $title }}</h3>
                            @endif
                            @if($subtitle)
                                <p class="text-xs mt-0.5" style="color: var(--text-secondary);">{{ $subtitle }}</p>
                            @endif
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

                    <div class="mt-6 relative flex-1 px-6 space-y-6">
                        {{ $slot }}
                    </div>

                    @if(isset($footer))
                        <div class="mt-6 pt-4 px-6 border-t flex items-center justify-end gap-3" style="border-color: var(--clay-card-border);">
                            {{ $footer }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
