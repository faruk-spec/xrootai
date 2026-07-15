@props([
    'title',
    'description' => null,
    'icon' => null,
    'action' => null,
])

<div class="text-center py-12 px-6 rounded-[24px] border flex flex-col items-center justify-center space-y-4"
     style="background: var(--clay-input-bg); border-color: var(--clay-card-border); box-shadow: var(--clay-input-shadow);">
    @if($icon)
        <div class="w-16 h-16 rounded-3xl flex items-center justify-center shrink-0 shadow-inner"
             style="background: var(--clay-card-bg); color: var(--accent); box-shadow: var(--clay-outer-shadow);">
            {!! $icon !!}
        </div>
    @endif
    
    <div class="max-w-sm space-y-1">
        <h4 class="text-base font-bold tracking-tight" style="color: var(--text-primary);">{{ $title }}</h4>
        @if($description)
            <p class="text-xs sm:text-sm leading-relaxed" style="color: var(--text-secondary);">{{ $description }}</p>
        @endif
    </div>

    @if($action || isset($slot))
        <div class="pt-2">
            {{ $action ?? $slot }}
        </div>
    @endif
</div>
