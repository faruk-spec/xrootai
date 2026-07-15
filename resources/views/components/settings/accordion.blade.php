@props([
    'title',
    'subtitle' => null,
    'icon' => null,
    'badge' => null,
    'open' => false,
])

<div x-data="{ open: {{ $open ? 'true' : 'false' }} }"
     class="rounded-2xl border transition-all duration-300 overflow-hidden"
     :class="{ 'ring-2 ring-[var(--accent)]/40': open }"
     style="background: var(--clay-input-bg); border-color: var(--clay-card-border); box-shadow: var(--clay-input-shadow);">
    
    <button type="button"
            @click="open = !open"
            class="w-full px-5 py-4 flex items-center justify-between text-left gap-4 transition-colors duration-200 hover:bg-white/5">
        <div class="flex items-center gap-3.5 min-w-0">
            @if($icon)
                <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 transition-transform duration-300"
                     :class="{ 'scale-110 rotate-3': open }"
                     style="background: var(--clay-card-bg); color: var(--accent); box-shadow: var(--clay-outer-shadow);">
                    {!! $icon !!}
                </div>
            @endif
            <div class="min-w-0">
                <div class="flex items-center gap-2">
                    <h5 class="text-sm sm:text-base font-bold tracking-tight truncate" style="color: var(--text-primary);">
                        {{ $title }}
                    </h5>
                    @if($badge)
                        <x-settings.badge size="sm">{{ $badge }}</x-settings.badge>
                    @endif
                </div>
                @if($subtitle)
                    <p class="text-xs truncate mt-0.5" style="color: var(--text-secondary);">{{ $subtitle }}</p>
                @endif
            </div>
        </div>

        <div class="shrink-0 flex items-center gap-2">
            <span class="w-8 h-8 rounded-full flex items-center justify-center transition-transform duration-300"
                  :class="{ 'rotate-180': open }"
                  style="background: var(--clay-card-bg); color: var(--text-secondary);">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                </svg>
            </span>
        </div>
    </button>

    <div x-show="open"
         x-collapse
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 max-h-0"
         x-transition:enter-end="opacity-100 max-h-screen"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 max-h-screen"
         x-transition:leave-end="opacity-0 max-h-0"
         style="display: none;">
        <div class="p-5 pt-2 border-t space-y-4" style="border-color: var(--clay-card-border); background: var(--clay-card-bg);">
            {{ $slot }}
        </div>
    </div>
</div>
