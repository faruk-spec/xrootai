@props([
    'status' => 'success', // success, warning, danger, neutral, pulse
    'size' => 'w-2.5 h-2.5',
    'animate' => true,
])

@php
    $colorClass = match($status) {
        'success' => 'bg-[var(--success)] shadow-[0_0_8px_var(--success)]',
        'warning' => 'bg-[var(--warning)] shadow-[0_0_8px_var(--warning)]',
        'danger' => 'bg-[var(--danger)] shadow-[0_0_8px_var(--danger)]',
        'neutral' => 'bg-[var(--text-secondary)] opacity-60',
        default => 'bg-[var(--accent)] shadow-[0_0_8px_var(--accent)]',
    };
@endphp

<span class="relative inline-flex items-center justify-center shrink-0">
    @if($animate && in_array($status, ['success', 'pulse', 'warning', 'danger']))
        <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-40 {{ $colorClass }}"></span>
    @endif
    <span class="relative inline-flex rounded-full {{ $size }} {{ $colorClass }}"></span>
</span>
