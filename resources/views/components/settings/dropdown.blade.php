@props([
    'align' => 'right',
    'width' => 'w-56',
])

@php
    $alignmentClasses = match ($align) {
        'left' => 'origin-top-left left-0',
        'top' => 'origin-bottom bottom-full mb-2',
        default => 'origin-top-right right-0',
    };
@endphp

<div class="relative inline-block text-left" x-data="{ open: false }" @click.outside="open = false" @close.stop="open = false">
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
         class="absolute z-50 mt-2 rounded-2xl shadow-2xl {{ $width }} {{ $alignmentClasses }} py-2 overflow-hidden border backdrop-blur-xl"
         style="background: var(--clay-card-bg); border-color: var(--clay-card-border); box-shadow: var(--clay-outer-shadow); display: none;">
        <div class="py-1 px-1.5 space-y-1" @click="open = false">
            {{ $content ?? $slot }}
        </div>
    </div>
</div>
