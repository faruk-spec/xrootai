<!-- Memory Section -->
<div class="space-y-6 animate-fade-in" x-data="{
        searchQuery: '',
        memories: [
            { id: 1, category: 'Technical Stack', text: 'User prefers Laravel 11.x + Alpine.js + Tailwind CSS over heavy SPA frameworks.', date: '2 days ago', confidence: '99%' },
            { id: 2, category: 'Project Context', text: 'User is building XrootAI, an enterprise AI gateway and pair-programming SaaS platform.', date: '5 days ago', confidence: '98%' },
            { id: 3, category: 'Styling Rules', text: 'User demands Claymorphism (clay cards, soft drop shadows, OLED dark mode) without Bootstrap.', date: 'Yesterday', confidence: '99%' },
            { id: 4, category: 'Coding Style', text: 'User requires clean, production-ready, modular Blade component extraction.', date: '1 week ago', confidence: '95%' },
            { id: 5, category: 'Workflow Preference', text: 'User prefers GitHub Dark code blocks with explicit intermediate CoT reasoning.', date: '3 days ago', confidence: '92%' }
        ],
        get filteredMemories() {
            if (!this.searchQuery) return this.memories;
            const q = this.searchQuery.toLowerCase();
            return this.memories.filter(m => m.text.toLowerCase().includes(q) || m.category.toLowerCase().includes(q));
        },
        deleteMemory(id) {
            this.memories = this.memories.filter(m => m.id !== id);
            $dispatch('notify', { message: 'Memory entry forgotten successfully.', type: 'warning' });
        },
        clearAllMemories() {
            if (confirm('Are you certain you want the AI assistant to permanently forget all learned facts and preferences about you?')) {
                this.memories = [];
                $dispatch('notify', { message: 'All memory slots cleared.', type: 'danger' });
            }
        }
    }">

    <!-- Section Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b" style="border-color: var(--clay-card-border);">
        <div>
            <h2 class="text-xl sm:text-2xl font-bold tracking-tight" style="color: var(--text-primary);">Autonomous Memory & Personal Knowledge Graph</h2>
            <p class="text-sm mt-1" style="color: var(--text-secondary);">Manage context, learned preferences, and long-term facts your AI remembers across all workspace sessions.</p>
        </div>
        <x-settings.badge variant="accent" size="lg" icon='<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>'>
            Tenant Isolated
        </x-settings.badge>
    </div>

    <!-- Memory Toggle & Status Graph Card -->
    <x-settings.card title="Memory Engine & Storage Quota" description="Control autonomous learning and monitor active embedding vector capacity." icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/></svg>'>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-center">
            <div class="space-y-4">
                <x-settings.toggle name="preferences[memory_enabled]"
                                   label="Enable Long-Term Autonomous Memory"
                                   description="Allow the AI to synthesize key facts, coding patterns, and workflow habits into an isolated vector memory store."
                                   :checked="!empty($settings->preferences['memory_enabled']) || !isset($settings->preferences['memory_enabled'])" />

                <div class="p-4 rounded-2xl border flex items-start gap-3 transition-all" style="background: var(--clay-input-bg); border-color: var(--clay-card-border);">
                    <svg class="w-5 h-5 shrink-0 text-blue-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    <div>
                        <h6 class="text-xs font-bold uppercase tracking-wider" style="color: var(--text-primary);">Tenant Privacy & Isolation Guarantee</h6>
                        <p class="text-[11px] leading-relaxed mt-1" style="color: var(--text-secondary);">
                            Your personal memory graph is encrypted at rest via AES-256 and stored inside an isolated vector namespace. It is strictly never shared with third parties or used to train base foundation models.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Usage Graph Card -->
            <div class="p-5 rounded-3xl border space-y-4" style="background: var(--clay-input-bg); border-color: var(--clay-card-border); box-shadow: var(--clay-input-shadow);">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-xs font-semibold block" style="color: var(--text-secondary);">Vector Memory Slots Capacity</span>
                        <h5 class="text-lg font-extrabold tracking-tight" style="color: var(--text-primary);">
                            <span x-text="memories.length">5</span> / 500 Slots Active
                        </h5>
                    </div>
                    <span class="px-2.5 py-1 rounded-xl text-xs font-mono font-bold bg-blue-500/15 text-blue-400 border border-blue-500/20">
                        1.2% Used
                    </span>
                </div>

                <x-settings.progress-bar :percentage="6" color="accent" height="h-3" />

                <div class="grid grid-cols-3 gap-2 pt-2 border-t text-[11px] font-mono" style="border-color: var(--clay-card-border);">
                    <div class="flex items-center gap-1.5 truncate">
                        <span class="w-2 h-2 rounded-full bg-blue-400"></span>
                        <span style="color: var(--text-secondary);">Stack: 40%</span>
                    </div>
                    <div class="flex items-center gap-1.5 truncate">
                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                        <span style="color: var(--text-secondary);">Context: 40%</span>
                    </div>
                    <div class="flex items-center gap-1.5 truncate">
                        <span class="w-2 h-2 rounded-full bg-purple-400"></span>
                        <span style="color: var(--text-secondary);">Style: 20%</span>
                    </div>
                </div>
            </div>
        </div>
    </x-settings.card>

    <!-- Stored Memories Explorer & Table -->
    <x-settings.card title="Learned Memories Explorer" description="Inspect, search, or forget individual facts the assistant has extracted from your prompts." icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>'>
        <div class="space-y-4">
            <!-- Search & Actions Bar -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pb-3 border-b" style="border-color: var(--clay-card-border);">
                <div class="relative w-full sm:w-80">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none opacity-60">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </span>
                    <input type="text"
                           x-model="searchQuery"
                           placeholder="Search memories by keyword or category..."
                           class="w-full rounded-2xl py-2.5 pl-10 pr-4 text-xs sm:text-sm font-medium transition-all focus:outline-none focus:ring-2 focus:ring-[var(--accent)]"
                           style="background: var(--clay-input-bg); color: var(--text-primary); border: 1px solid var(--clay-card-border); box-shadow: var(--clay-input-shadow);">
                </div>

                <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                    <button type="button" @click="clearAllMemories()" x-show="memories.length > 0" class="px-4 py-2.5 rounded-xl text-xs font-semibold transition-transform hover:scale-105 border flex items-center gap-1.5" style="background: rgba(239, 68, 68, 0.12); border-color: rgba(239, 68, 68, 0.3); color: var(--danger);">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Forget All Memories
                    </button>
                </div>
            </div>

            <!-- Memories List / Table -->
            <div class="space-y-3">
                <template x-if="filteredMemories.length === 0">
                    <x-settings.empty-state title="No matching memories found"
                                            description="The assistant has not recorded any memory entries matching your search filter."
                                            icon='<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>' />
                </template>

                <template x-for="mem in filteredMemories" :key="mem.id">
                    <div class="p-4 sm:p-5 rounded-2xl border flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 transition-all hover:border-[var(--accent)]"
                         style="background: var(--clay-input-bg); border-color: var(--clay-card-border); box-shadow: var(--clay-input-shadow);">
                        <div class="flex items-start gap-3.5 min-w-0">
                            <span class="px-2.5 py-1 rounded-lg text-[11px] font-mono font-bold uppercase shrink-0 mt-0.5"
                                  style="background: var(--clay-card-bg); color: var(--accent); border: 1px solid var(--clay-card-border);">
                                <span x-text="mem.category">Category</span>
                            </span>
                            <div class="min-w-0">
                                <p class="text-xs sm:text-sm font-semibold tracking-tight leading-relaxed" style="color: var(--text-primary);" x-text="mem.text"></p>
                                <div class="flex items-center gap-3 mt-1.5 text-[11px] font-mono opacity-65" style="color: var(--text-secondary);">
                                    <span x-text="'Learned ' + mem.date">Date</span>
                                    <span>•</span>
                                    <span x-text="'Confidence: ' + mem.confidence">Confidence</span>
                                </div>
                            </div>
                        </div>

                        <div class="shrink-0 self-end sm:self-center">
                            <button type="button"
                                    @click="deleteMemory(mem.id)"
                                    class="px-3 py-1.5 rounded-xl text-xs font-semibold transition-transform hover:scale-105 border flex items-center gap-1 opacity-80 hover:opacity-100 hover:bg-red-500 hover:text-white hover:border-red-600"
                                    style="background: var(--clay-card-bg); color: var(--danger); border-color: var(--clay-card-border);">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                Forget
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </x-settings.card>
</div>
