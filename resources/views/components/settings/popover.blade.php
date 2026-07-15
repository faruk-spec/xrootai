@props([
    'title' => null,
    'align' => 'right',
    'width' => 'w-72',
])

@php
    $alignmentClasses = match ($align) {
        'left' => 'origin-top-left left-0',
        'top' => 'origin-bottom bottom-full mb-2',
        default => 'origin-top-right right-0',
    };
@endphp

<div class="relative inline-block" x-data="{ open: false }" @click.outside="open = false" @close.stop="open = false">
    <div @click="open = !open">
        {{ $trigger }}
    </div>

    <div x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95 translate-y-1"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 translate-y-1"
         class="absolute z-50 mt-2 rounded-2xl shadow-2xl {{ $width }} {{ $alignmentClasses }} p-4 border backdrop-blur-xl"
         style="background: var(--clay-card-bg); border-color: var(--clay-card-border); box-shadow: var(--clay-outer-shadow); display: none;">
        
        @if($title)
            <div class="flex items-center justify-between pb-2 mb-3 border-b" style="border-color: var(--clay-card-border);">
                <h6 class="text-xs font-bold uppercase tracking-wider" style="color: var(--text-primary);">{{ $title }}</h6>
                <button type="button" @click="open = false" class="opacity-60 hover:opacity-100 transition-opacity">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        @endif

        <div class="space-y-3">
            {{ $content ?? $slot }}
        </div>
    </div>
</div>
