@props([
    'title' => 'An error occurred',
    'message' => null,
    'retryAction' => null,
])

<div class="rounded-2xl p-4 sm:p-5 flex items-start gap-4 border shadow-sm transition-all duration-200"
     style="background: rgba(239, 68, 68, 0.12); border-color: rgba(239, 68, 68, 0.3); color: var(--danger);">
    <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 bg-red-500/20 text-red-500">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    </div>
    <div class="flex-1 min-w-0">
        <h5 class="text-sm font-bold tracking-tight text-red-500 dark:text-red-400">{{ $title }}</h5>
        @if($message)
            <p class="text-xs sm:text-sm mt-1 leading-relaxed opacity-90 text-red-600 dark:text-red-300">{{ $message }}</p>
        @else
            <div class="text-xs sm:text-sm mt-1 leading-relaxed opacity-90 text-red-600 dark:text-red-300">{{ $slot }}</div>
        @endif
    </div>
    @if($retryAction)
        <div class="shrink-0 self-center">
            {{ $retryAction }}
        </div>
    @endif
</div>
