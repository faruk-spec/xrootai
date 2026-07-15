@props([
    'tabs' => [], // array of key => ['label' => '...', 'icon' => '...']
    'activeTab' => null,
    'model' => null,
])

<div class="flex items-center gap-1.5 p-1.5 rounded-2xl overflow-x-auto no-scrollbar border"
     style="background: var(--clay-input-bg); border-color: var(--clay-card-border); box-shadow: var(--clay-input-shadow);">
    @foreach($tabs as $key => $tab)
        @php
            $label = is_array($tab) ? ($tab['label'] ?? $key) : $tab;
            $icon = is_array($tab) ? ($tab['icon'] ?? null) : null;
            $badge = is_array($tab) ? ($tab['badge'] ?? null) : null;
        @endphp
        <button type="button"
                @if($model)
                    @click="{{ $model }} = '{{ $key }}'"
                @endif
                class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-semibold whitespace-nowrap transition-all duration-200 shrink-0"
                :style="{{ $model ? ($model . " === '" . $key . "' ? 'background: var(--clay-card-bg); color: var(--accent); box-shadow: var(--clay-outer-shadow); border: 1px solid var(--clay-card-border);' : 'color: var(--text-secondary); background: transparent;'") : ("'" . ($activeTab == $key) . "' === '1' ? 'background: var(--clay-card-bg); color: var(--accent); box-shadow: var(--clay-outer-shadow); border: 1px solid var(--clay-card-border);' : 'color: var(--text-secondary); background: transparent;'") }}">
            @if($icon)
                <span class="shrink-0">{!! $icon !!}</span>
            @endif
            <span>{{ $label }}</span>
            @if($badge)
                <span class="px-1.5 py-0.5 rounded-md text-[10px] font-bold"
                      style="background: rgba(74, 136, 255, 0.2); color: var(--accent);">
                    {{ $badge }}
                </span>
            @endif
        </button>
    @endforeach
</div>
