@props([
    'label',
    'icon' => null,
    'removable' => false,
    'onRemove' => null,
    'active' => false,
])

<span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl text-xs sm:text-sm font-medium transition-all duration-200 shrink-0 select-none {{ $attributes->get('class') }}"
      :style="{{ $attributes->has('x-bind:style') ? $attributes->get('x-bind:style') : ($active ? "'background: var(--clay-card-bg); color: var(--accent); border: 1px solid var(--clay-card-border); box-shadow: var(--clay-outer-shadow); font-weight: 700;'" : "'background: var(--clay-input-bg); color: var(--text-primary); border: 1px solid var(--clay-card-border); box-shadow: var(--clay-input-shadow);'") }}">
    @if($icon)
        <span class="shrink-0">{!! $icon !!}</span>
    @endif
    <span>{{ $label }}</span>
    @if($removable)
        <button type="button"
                @if($onRemove) @click.stop="{{ $onRemove }}" @endif
                class="w-4 h-4 rounded-full flex items-center justify-center opacity-60 hover:opacity-100 hover:bg-red-500 hover:text-white transition-all duration-200">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    @endif
</span>
