@props([
    'name',
    'id' => null,
    'label' => null,
    'description' => null,
    'checked' => false,
    'disabled' => false,
    'model' => null,
    'onChange' => null,
])

@php
    $id = $id ?? $name;
@endphp

<div class="flex items-start justify-between gap-4 py-3 group">
    <div class="flex-1 min-w-0 pr-2 cursor-pointer" @if(!$disabled) onclick="document.getElementById('{{ $id }}').click()" @endif>
        @if($label)
            <label for="{{ $id }}" class="block text-sm font-semibold tracking-tight cursor-pointer select-none transition-colors duration-200 group-hover:text-[var(--accent)]" style="color: var(--text-primary);">
                {{ $label }}
            </label>
        @endif
        @if($description)
            <p class="text-xs mt-1 leading-normal select-none" style="color: var(--text-secondary);">
                {{ $description }}
            </p>
        @endif
        {{ $slot }}
    </div>

    <div class="shrink-0 pt-0.5">
        <label class="relative inline-flex items-center cursor-pointer select-none {{ $disabled ? 'opacity-50 cursor-not-allowed' : '' }}">
            <input type="checkbox"
                   name="{{ $name }}"
                   id="{{ $id }}"
                   value="1"
                   class="sr-only peer"
                   @if($checked) checked @endif
                   @if($disabled) disabled @endif
                   @if($model) x-model="{{ $model }}" @endif
                   @if($onChange) @change="{{ $onChange }}" @endif
                   @if(!$model && !$disabled) @change="if ($event.target.checked) { $el.parentElement.classList.add('is-checked') } else { $el.parentElement.classList.remove('is-checked') }" @endif
            >
            <div class="w-13 h-7 rounded-full transition-all duration-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[var(--accent)]/30 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[3px] after:left-[3px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all after:duration-300 after:shadow-md"
                 :style="{{ $model ? $model . ' ? \'background: var(--accent); box-shadow: 0 0 12px var(--accent-hover);\' : \'background: var(--clay-input-bg); border: 1px solid var(--clay-card-border); box-shadow: var(--clay-input-shadow);\'' : '($el.previousElementSibling.checked ? \'background: var(--accent); box-shadow: 0 0 12px var(--accent-hover);\' : \'background: var(--clay-input-bg); border: 1px solid var(--clay-card-border); box-shadow: var(--clay-input-shadow);\')' }}">
            </div>
        </label>
    </div>
</div>
