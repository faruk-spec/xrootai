@props([
    'title' => 'Success',
    'message' => null,
])

<div class="rounded-2xl p-4 sm:p-5 flex items-start gap-4 border shadow-sm transition-all duration-200"
     style="background: rgba(16, 185, 129, 0.12); border-color: rgba(16, 185, 129, 0.3); color: var(--success);">
    <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 bg-emerald-500/20 text-emerald-500">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
    </div>
    <div class="flex-1 min-w-0">
        <h5 class="text-sm font-bold tracking-tight text-emerald-600 dark:text-emerald-400">{{ $title }}</h5>
        @if($message)
            <p class="text-xs sm:text-sm mt-1 leading-relaxed opacity-90 text-emerald-700 dark:text-emerald-300">{{ $message }}</p>
        @else
            <div class="text-xs sm:text-sm mt-1 leading-relaxed opacity-90 text-emerald-700 dark:text-emerald-300">{{ $slot }}</div>
        @endif
    </div>
</div>
