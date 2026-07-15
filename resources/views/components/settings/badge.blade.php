@props([
    'variant' => 'accent', // accent, success, warning, danger, neutral, outline
    'size' => 'md', // sm, md, lg
    'icon' => null,
])

@php
    $sizeClasses = match($size) {
        'sm' => 'px-2 py-0.5 text-[11px]',
        'lg' => 'px-3.5 py-1.5 text-sm font-semibold',
        default => 'px-2.5 py-1 text-xs font-medium',
    };

    $variantStyles = match($variant) {
        'success' => 'background: rgba(16, 185, 129, 0.15); color: var(--success); border: 1px solid rgba(16, 185, 129, 0.3);',
        'warning' => 'background: rgba(245, 158, 11, 0.15); color: var(--warning); border: 1px solid rgba(245, 158, 11, 0.3);',
        'danger' => 'background: rgba(239, 68, 68, 0.15); color: var(--danger); border: 1px solid rgba(239, 68, 68, 0.3);',
        'neutral' => 'background: var(--clay-input-bg); color: var(--text-secondary); border: 1px solid var(--clay-card-border);',
        'outline' => 'background: transparent; color: var(--text-primary); border: 1px dashed var(--clay-card-border);',
        default => 'background: rgba(74, 136, 255, 0.15); color: var(--accent); border: 1px solid rgba(74, 136, 255, 0.3);',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 rounded-full tracking-wide transition-transform duration-200 hover:scale-105 shrink-0 " . $sizeClasses]) }}
      style="{{ $variantStyles }}">
    @if($icon)
        <span class="shrink-0">{!! $icon !!}</span>
    @endif
    {{ $slot }}
</span>
