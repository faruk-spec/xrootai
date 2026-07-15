@props([
    'name',
    'id' => null,
    'label' => null,
    'description' => null,
    'min' => 0,
    'max' => 100,
    'step' => 1,
    'value' => 50,
    'model' => null,
    'unit' => '',
])

@php
    $id = $id ?? $name;
@endphp

<div class="space-y-2.5 py-3"
     @if(!$model) x-data="{ val: {{ $value }} }" @endif>
    <div class="flex items-center justify-between gap-4">
        <div>
            @if($label)
                <label for="{{ $id }}" class="block text-sm font-semibold tracking-tight" style="color: var(--text-primary);">
                    {{ $label }}
                </label>
            @endif
            @if($description)
                <p class="text-xs mt-0.5 leading-normal" style="color: var(--text-secondary);">{{ $description }}</p>
            @endif
        </div>
        <div class="shrink-0">
            <span class="inline-flex items-center px-3 py-1 rounded-xl text-xs sm:text-sm font-mono font-bold transition-all duration-200 shadow-inner"
                  style="background: var(--clay-input-bg); border: 1px solid var(--clay-card-border); color: var(--accent);">
                <span x-text="{{ $model ? $model : 'val' }}">{{ $value }}</span>{{ $unit }}
            </span>
        </div>
    </div>

    <div class="relative pt-1">
        <input type="range"
               name="{{ $name }}"
               id="{{ $id }}"
               min="{{ $min }}"
               max="{{ $max }}"
               step="{{ $step }}"
               value="{{ $value }}"
               @if($model) x-model.number="{{ $model }}" @else @input="val = $event.target.value" @endif
               class="w-full h-3 rounded-lg appearance-none cursor-pointer transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-[var(--accent)]"
               style="background: var(--clay-input-bg); box-shadow: var(--clay-input-shadow);"
        >
        <div class="flex justify-between text-[11px] font-medium mt-1.5 px-1 opacity-70" style="color: var(--text-secondary);">
            <span>{{ $min }}{{ $unit }}</span>
            @if(($min + $max) / 2 != $min && ($min + $max) / 2 != $max)
                <span>{{ ($min + $max) / 2 }}{{ $unit }}</span>
            @endif
            <span>{{ $max }}{{ $unit }}</span>
        </div>
    </div>
</div>
