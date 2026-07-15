@props([
    'type' => 'lines', // lines, card, avatar, title
    'lines' => 3,
    'class' => '',
])

<div class="animate-pulse space-y-3 w-full {{ $class }}">
    @if($type === 'avatar')
        <div class="w-12 h-12 rounded-2xl" style="background: var(--clay-input-bg);"></div>
    @elseif($type === 'title')
        <div class="h-6 rounded-xl w-1/3" style="background: var(--clay-input-bg);"></div>
    @elseif($type === 'card')
        <div class="p-6 rounded-[24px] space-y-4 border" style="background: var(--clay-input-bg); border-color: var(--clay-card-border);">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl shrink-0" style="background: var(--clay-card-bg);"></div>
                <div class="space-y-2 flex-1">
                    <div class="h-4 rounded-lg w-1/2" style="background: var(--clay-card-bg);"></div>
                    <div class="h-3 rounded-lg w-1/3 opacity-70" style="background: var(--clay-card-bg);"></div>
                </div>
            </div>
            <div class="space-y-2 pt-2">
                <div class="h-3 rounded-lg w-full" style="background: var(--clay-card-bg);"></div>
                <div class="h-3 rounded-lg w-4/5" style="background: var(--clay-card-bg);"></div>
            </div>
        </div>
    @else
        @for($i = 0; $i < $lines; $i++)
            <div class="h-4 rounded-xl {{ $i === $lines - 1 ? 'w-2/3' : 'w-full' }}" style="background: var(--clay-input-bg);"></div>
        @endfor
    @endif
</div>
