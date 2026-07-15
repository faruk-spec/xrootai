@props([
    'name',
    'options' => [], // array of value => label or array of array(value, label, icon, desc)
    'selected' => null,
    'model' => null,
    'label' => null,
    'description' => null,
    'columns' => null, // e.g. 3, 4, auto
])

<div class="space-y-2.5 py-2">
    @if($label)
        <div class="flex items-center justify-between gap-2">
            <label class="block text-sm font-semibold tracking-tight" style="color: var(--text-primary);">
                {{ $label }}
            </label>
        </div>
    @endif
    @if($description)
        <p class="text-xs leading-normal" style="color: var(--text-secondary);">{{ $description }}</p>
    @endif

    <div class="grid gap-2.5 p-1.5 rounded-2xl transition-all duration-200 {{ $columns ? 'grid-cols-' . $columns : 'grid-cols-' . count($options) }}"
         style="background: var(--clay-input-bg); border: 1px solid var(--clay-card-border); box-shadow: var(--clay-input-shadow);">
        @foreach($options as $key => $opt)
            @php
                $val = is_array($opt) ? ($opt['value'] ?? $key) : $key;
                $lbl = is_array($opt) ? ($opt['label'] ?? $opt) : $opt;
                $ico = is_array($opt) ? ($opt['icon'] ?? null) : null;
                $desc = is_array($opt) ? ($opt['description'] ?? null) : null;
            @endphp
            <label class="relative flex flex-col items-center justify-center text-center py-2.5 px-3 rounded-xl cursor-pointer transition-all duration-200 select-none group focus-within:ring-2 focus-within:ring-[var(--accent)]"
                   :style="{{ $model ? ($model . " === '" . $val . "' ? 'background: var(--clay-card-bg); box-shadow: var(--clay-outer-shadow); color: var(--accent); border: 1px solid var(--clay-card-border); font-weight: 700;' : 'color: var(--text-secondary); opacity: 0.85;')") : ("'" . ($selected == $val) . "' === '1' ? 'background: var(--clay-card-bg); box-shadow: var(--clay-outer-shadow); color: var(--accent); border: 1px solid var(--clay-card-border); font-weight: 700;' : 'color: var(--text-secondary); opacity: 0.85;'") }}">
                <input type="radio"
                       name="{{ $name }}"
                       value="{{ $val }}"
                       class="sr-only"
                       @if($model) x-model="{{ $model }}" @elseif($selected == $val) checked @endif
                       @click="$el.parentElement.parentElement.querySelectorAll('label').forEach(l => { l.style.background='transparent'; l.style.boxShadow='none'; l.style.color='var(--text-secondary)'; l.style.border='none'; l.style.fontWeight='500'; }); $el.parentElement.style.background='var(--clay-card-bg)'; $el.parentElement.style.boxShadow='var(--clay-outer-shadow)'; $el.parentElement.style.color='var(--accent)'; $el.parentElement.style.border='1px solid var(--clay-card-border)'; $el.parentElement.style.fontWeight='700';"
                >
                <div class="flex items-center gap-2">
                    @if($ico)
                        <span class="shrink-0 transition-transform duration-200 group-hover:scale-110">{!! $ico !!}</span>
                    @endif
                    <span class="text-xs sm:text-sm tracking-tight">{{ $lbl }}</span>
                </div>
                @if($desc)
                    <span class="text-[11px] opacity-75 mt-0.5 leading-tight block">{{ $desc }}</span>
                @endif
            </label>
        @endforeach
    </div>
</div>
