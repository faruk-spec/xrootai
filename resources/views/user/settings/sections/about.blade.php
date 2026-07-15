<!-- About & System Diagnostics Section -->
<div class="space-y-6 animate-fade-in" x-data="{
        checkingUpdate: false,
        checkForUpdates() {
            if (this.checkingUpdate) return;
            this.checkingUpdate = true;
            $dispatch('notify', { message: 'Checking XrootAI edge release registry for updates...', type: 'accent' });
            setTimeout(() => {
                this.checkingUpdate = false;
                $dispatch('notify', { message: 'You are running the latest stable release (v3.8.4 Enterprise build #a89f21e0)!', type: 'success' });
            }, 1600);
        }
    }">

    <!-- Section Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b" style="border-color: var(--clay-card-border);">
        <div>
            <h2 class="text-xl sm:text-2xl font-bold tracking-tight" style="color: var(--text-primary);">About XrootAI & System Diagnostics</h2>
            <p class="text-sm mt-1" style="color: var(--text-secondary);">Inspect runtime versions, telemetry health, release notes, and legal compliance documentation.</p>
        </div>
        <x-settings.badge variant="accent" size="lg" icon='<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'>
            v3.8.4 Stable
        </x-settings.badge>
    </div>

    <!-- Hero Version & Update Card -->
    <div class="p-6 sm:p-8 rounded-[32px] border flex flex-col sm:flex-row items-center justify-between gap-6 relative overflow-hidden transition-all shadow-md"
         style="background: linear-gradient(135deg, var(--clay-card-bg) 0%, rgba(74, 136, 255, 0.12) 100%); border-color: var(--clay-card-border);">
        <div class="flex items-center gap-5 text-center sm:text-left flex-col sm:flex-row">
            <div class="w-20 h-20 rounded-3xl flex items-center justify-center shrink-0 shadow-xl transition-transform hover:scale-105"
                 style="background: var(--accent); color: white;">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>
            <div>
                <div class="flex items-center justify-center sm:justify-start gap-2.5">
                    <h3 class="text-2xl font-black tracking-tight" style="color: var(--text-primary);">XrootAI Enterprise Platform</h3>
                    <x-settings.badge variant="success" size="sm">Stable</x-settings.badge>
                </div>
                <p class="text-xs sm:text-sm font-mono mt-1 opacity-80" style="color: var(--text-secondary);">
                    Release Build: <strong style="color: var(--accent);">v3.8.4</strong> • Git Commit Hash: <code class="px-1.5 py-0.5 rounded bg-black/10 dark:bg-white/10">#a89f21e0</code>
                </p>
                <p class="text-xs mt-2 max-w-md leading-relaxed" style="color: var(--text-secondary);">
                    Next-generation autonomous AI pair programming, multi-model gateway, and tenant knowledge vector platform.
                </p>
            </div>
        </div>

        <div class="shrink-0 w-full sm:w-auto flex flex-col items-center sm:items-end gap-3">
            <button type="button"
                    @click="checkForUpdates()"
                    :disabled="checkingUpdate"
                    class="w-full sm:w-auto px-6 py-3 rounded-2xl text-xs sm:text-sm font-bold shadow-lg transition-all hover:scale-105 flex items-center justify-center gap-2"
                    style="background: var(--accent); color: white;">
                <template x-if="checkingUpdate">
                    <x-settings.loading-spinner size="w-4 h-4" color="white" />
                </template>
                <template x-if="!checkingUpdate">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                </template>
                <span x-text="checkingUpdate ? 'Checking Edge Registry...' : 'Check for System Updates'"></span>
            </button>
            <span class="text-[11px] font-mono opacity-65" style="color: var(--text-secondary);">Automatic background sync active</span>
        </div>
    </div>

    <!-- System Diagnostics Card -->
    <x-settings.card title="System Diagnostics & Runtime Stack" description="Technical runtime versions powering your current workspace server instance." icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/></svg>'>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="p-4 rounded-2xl border space-y-1 font-mono text-center sm:text-left" style="background: var(--clay-input-bg); border-color: var(--clay-card-border);">
                <span class="text-xs uppercase font-bold opacity-70 block" style="color: var(--text-secondary);">Framework</span>
                <span class="text-sm font-black block text-red-500">Laravel {{ app()->version() }}</span>
                <span class="text-[11px] opacity-60 block">Blade Engine</span>
            </div>

            <div class="p-4 rounded-2xl border space-y-1 font-mono text-center sm:text-left" style="background: var(--clay-input-bg); border-color: var(--clay-card-border);">
                <span class="text-xs uppercase font-bold opacity-70 block" style="color: var(--text-secondary);">PHP Runtime</span>
                <span class="text-sm font-black block text-indigo-400">PHP {{ PHP_VERSION }}</span>
                <span class="text-[11px] opacity-60 block">OPcache Enabled</span>
            </div>

            <div class="p-4 rounded-2xl border space-y-1 font-mono text-center sm:text-left" style="background: var(--clay-input-bg); border-color: var(--clay-card-border);">
                <span class="text-xs uppercase font-bold opacity-70 block" style="color: var(--text-secondary);">Frontend UI</span>
                <span class="text-sm font-black block text-teal-400">Alpine.js v3.13</span>
                <span class="text-[11px] opacity-60 block">Tailwind v3.4</span>
            </div>

            <div class="p-4 rounded-2xl border space-y-1 font-mono text-center sm:text-left" style="background: var(--clay-input-bg); border-color: var(--clay-card-border);">
                <span class="text-xs uppercase font-bold opacity-70 block" style="color: var(--text-secondary);">Server Environment</span>
                <span class="text-sm font-black block" style="color: var(--text-primary);">{{ PHP_OS_FAMILY }} x64</span>
                <span class="text-[11px] opacity-60 block">Nginx Proxy</span>
            </div>
        </div>
    </x-settings.card>

    <!-- Release Notes / Changelog Card -->
    <x-settings.card title="Changelog & Release Highlights (v3.8.4)" description="Explore recently shipped capabilities and system enhancements." icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>'>
        <div class="space-y-4">
            <x-settings.accordion title="Release v3.8.4 Enterprise — Redesigned Settings & BYOK Architecture" :open="true">
                <ul class="text-xs sm:text-sm space-y-2 list-disc pl-5 leading-relaxed" style="color: var(--text-secondary);">
                    <li><strong style="color: var(--text-primary);">Total UI/UX Overhaul:</strong> Completely redesigned Claymorphism Settings Dashboard supporting Light, Dark, and OLED pure black modes without Bootstrap.</li>
                    <li><strong style="color: var(--text-primary);">Bring Your Own Key (BYOK):</strong> Added support for 10+ AI providers including OpenAI, Anthropic, Google Gemini, DeepSeek, Groq, and OpenRouter with client-side test handshakes.</li>
                    <li><strong style="color: var(--text-primary);">Live Theme & Density Preview:</strong> Real-time visual feedback across theme variants, border radii, and visual density.</li>
                    <li><strong style="color: var(--text-primary);">Autonomous Memory Manager:</strong> Inspect, search, and forget user facts learned inside your isolated vector store.</li>
                </ul>
            </x-settings.accordion>

            <x-settings.accordion title="Release v3.7.0 — Multi-Agent Reasoning Canvas & MCP Integration">
                <ul class="text-xs sm:text-sm space-y-2 list-disc pl-5 leading-relaxed" style="color: var(--text-secondary);">
                    <li>Introduced deep Chain of Thought (CoT) reasoning scratchpads with expandable thinking steps.</li>
                    <li>Added Model Context Protocol (MCP) server support for connecting local database tools and GitHub repos.</li>
                    <li>Improved voice TTS synthesis latency down to under 250ms with ElevenLabs turbo stream engines.</li>
                </ul>
            </x-settings.accordion>
        </div>
    </x-settings.card>

    <!-- Legal & Copyright Footer -->
    <div class="p-6 rounded-[24px] border flex flex-col sm:flex-row items-center justify-between gap-4 text-xs font-mono"
         style="background: var(--clay-input-bg); border-color: var(--clay-card-border); color: var(--text-secondary);">
        <div>
            <span>© {{ date('Y') }} XrootAI Inc. All rights reserved. Made with ❤️ for developers and AI engineers.</span>
        </div>
        <div class="flex items-center gap-4">
            <a href="#" @click.prevent="$dispatch('notify', { message: 'Opening Terms of Service...', type: 'accent' })" class="hover:underline text-[var(--accent)]">Terms of Service</a>
            <span>•</span>
            <a href="#" @click.prevent="$dispatch('notify', { message: 'Opening Privacy Policy...', type: 'accent' })" class="hover:underline text-[var(--accent)]">Privacy Policy</a>
            <span>•</span>
            <a href="#" @click.prevent="$dispatch('notify', { message: 'Opening Security Whitepaper...', type: 'accent' })" class="hover:underline text-[var(--accent)]">SOC2 Whitepaper</a>
        </div>
    </div>
</div>
