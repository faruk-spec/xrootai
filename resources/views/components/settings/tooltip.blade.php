@props([
    'text',
    'position' => 'top', // top, bottom, left, right
])

<div x-data="{ show: false }" @mouseenter="show = true" @mouseleave="show = false" class="relative inline-flex items-center">
    {{ $slot }}

    <div x-show="show"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute z-50 pointer-events-none px-2.5 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap shadow-xl"
         style="background: var(--text-primary); color: var(--bg-main);
                @if($position === 'top') bottom: 100%; left: 50%; transform: translate(-50%, -6px);
                @elseif($position === 'bottom') top: 100%; left: 50%; transform: translate(-50%, 6px);
                @elseif($position === 'left') right: 100%; top: 50%; transform: translate(-6px, -50%);
                @else left: 100%; top: 50%; transform: translate(6px, -50%); @endif
                display: none;">
        {{ $text }}
    </div>
</div>
