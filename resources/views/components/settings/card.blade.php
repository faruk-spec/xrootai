@props([
    'title' => null,
    'description' => null,
    'icon' => null,
    'badge' => null,
    'badgeColor' => 'accent',
    'headerAction' => null,
    'footer' => null,
    'class' => '',
])

<div {{ $attributes->merge(['class' => 'clay-card relative rounded-[28px] p-6 md:p-8 transition-all duration-300 ' . $class]) }}
     style="background: var(--clay-card-bg); border: 1px solid var(--clay-card-border); box-shadow: var(--clay-outer-shadow);">
    @if($title || $description || $icon || $badge || $headerAction || isset($header))
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 pb-5 border-b" style="border-color: var(--clay-card-border);">
            <div class="flex items-start gap-4">
                @if($icon)
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center shrink-0 shadow-sm transition-transform duration-300 group-hover:scale-105"
                         style="background: var(--clay-input-bg); box-shadow: var(--clay-inner-shadow); color: var(--accent);">
                        {!! $icon !!}
                    </div>
                @endif
                <div>
                    <div class="flex items-center gap-2.5 flex-wrap">
                        @if($title)
                            <h3 class="text-lg font-bold tracking-tight" style="color: var(--text-primary);">{{ $title }}</h3>
                        @endif
                        @if($badge)
                            <x-settings.badge :variant="$badgeColor">{{ $badge }}</x-settings.badge>
                        @endif
                    </div>
                    @if($description)
                        <p class="text-sm mt-1 leading-relaxed" style="color: var(--text-secondary);">{{ $description }}</p>
                    @endif
                </div>
            </div>
            @if($headerAction || isset($header))
                <div class="flex items-center gap-3 self-start sm:self-center shrink-0">
                    {{ $headerAction ?? $header }}
                </div>
            @endif
        </div>
    @endif

    <div class="space-y-6">
        {{ $slot }}
    </div>

    @if($footer || isset($cardFooter))
        <div class="mt-8 pt-5 border-t flex flex-wrap items-center justify-between gap-4" style="border-color: var(--clay-card-border);">
            {{ $footer ?? $cardFooter }}
        </div>
    @endif
</div>
