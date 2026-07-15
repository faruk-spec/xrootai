@props([
    'percentage' => 0, // 0 to 100
    'label' => null,
    'valueLabel' => null,
    'color' => 'accent', // accent, success, warning, danger
    'height' => 'h-2.5',
])

@php
    $barGradient = match($color) {
        'success' => 'background: linear-gradient(90deg, #10b981 0%, #34d399 100%);',
        'warning' => 'background: linear-gradient(90deg, #f59e0b 0%, #fbbf24 100%);',
        'danger' => 'background: linear-gradient(90deg, #ef4444 0%, #f87171 100%);',
        default => 'background: linear-gradient(90deg, #4a88ff 0%, #7eb6ff 100%);',
    };
@endphp

<div class="space-y-1.5 w-full">
    @if($label || $valueLabel)
        <div class="flex items-center justify-between text-xs font-semibold">
            @if($label)
                <span style="color: var(--text-primary);">{{ $label }}</span>
            @endif
            @if($valueLabel)
                <span class="font-mono" style="color: var(--text-secondary);">{{ $valueLabel }}</span>
            @else
                <span class="font-mono" style="color: var(--text-secondary);">{{ $percentage }}%</span>
            @endif
        </div>
    @endif

    <div class="w-full rounded-full overflow-hidden {{ $height }} p-0.5"
         style="background: var(--clay-input-bg); box-shadow: var(--clay-input-shadow); border: 1px solid var(--clay-card-border);">
        <div class="h-full rounded-full transition-all duration-500 ease-out shadow-sm"
             style="width: {{ min(max($percentage, 0), 100) }}%; {{ $barGradient }}"></div>
    </div>
</div>
