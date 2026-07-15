@props([
    'name',
    'id' => null,
    'label' => null,
    'description' => null,
    'icon' => null,
    'options' => [], // array of value => label or array of options
    'selected' => null,
    'model' => null,
    'disabled' => false,
])

@php
    $id = $id ?? $name;
@endphp

<div class="space-y-2 py-2">
    @if($label)
        <label for="{{ $id }}" class="block text-sm font-semibold tracking-tight" style="color: var(--text-primary);">
            {{ $label }}
        </label>
    @endif
    @if($description)
        <p class="text-xs leading-normal" style="color: var(--text-secondary);">{{ $description }}</p>
    @endif

    <div class="relative rounded-2xl transition-all duration-200 focus-within:ring-2 focus-within:ring-[var(--accent)]"
         style="background: var(--clay-input-bg); border: 1px solid var(--clay-card-border); box-shadow: var(--clay-input-shadow);">
        @if($icon)
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-sm" style="color: var(--accent);">
                {!! $icon !!}
            </div>
        @endif

        <select name="{{ $name }}"
                id="{{ $id }}"
                @if($disabled) disabled @endif
                @if($model) x-model="{{ $model }}" @endif
                class="w-full appearance-none rounded-2xl py-3 pr-10 {{ $icon ? 'pl-10' : 'pl-4' }} text-sm font-medium focus:outline-none transition-colors duration-200 cursor-pointer"
                style="background: transparent; color: var(--text-primary);">
            @if(is_array($options) && !empty($options))
                @foreach($options as $key => $opt)
                    @php
                        $val = is_array($opt) ? ($opt['value'] ?? $key) : $key;
                        $lbl = is_array($opt) ? ($opt['label'] ?? $opt) : $opt;
                    @endphp
                    <option value="{{ $val }}"
                            style="background: var(--bg-main); color: var(--text-primary);"
                            @if($selected == $val) selected @endif>
                        {{ $lbl }}
                    </option>
                @endforeach
            @endif
            {{ $slot }}
        </select>

        <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none" style="color: var(--text-secondary);">
            <svg class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </div>
    </div>
</div>
