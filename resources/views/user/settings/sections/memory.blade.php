<!-- Memory & Context Section -->
<div class="space-y-6 animate-fade-in" x-data="{
        searchQuery: '',
        newMemoryText: '',
        newMemoryCategory: 'General',
        showAddForm: false,
        isSaving: false,
        memoryEnabled: {{ !empty($settings->preferences['memory_enabled']) || !isset($settings->preferences['memory_enabled']) ? 'true' : 'false' }},
        memories: {!! json_encode($settings->preferences['memories'] ?? []) !!},
        
        get filteredMemories() {
            if (!this.searchQuery) return this.memories;
            const q = this.searchQuery.toLowerCase();
            return this.memories.filter(m => 
                (m.text && m.text.toLowerCase().includes(q)) || 
                (m.category && m.category.toLowerCase().includes(q))
            );
        },

        async updateBackend() {
            this.isSaving = true;
            try {
                const res = await fetch('{{ route('settings.update') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        preferences: { 
                            memory_enabled: this.memoryEnabled,
                            memories: this.memories 
                        }
                    })
                });
                const data = await res.json();
                if (window.showToast) window.showToast('Memory settings saved', 'success');
            } catch (e) {
                console.error('Failed to sync memory:', e);
                if (window.showToast) window.showToast('Failed to save memory settings', 'error');
            } finally {
                this.isSaving = false;
            }
        },

        toggleMemoryEnabled() {
            this.memoryEnabled = !this.memoryEnabled;
            this.updateBackend();
        },

        addMemory() {
            if (!this.newMemoryText.trim()) return;
            const newEntry = {
                id: 'mem_' + Date.now(),
                text: this.newMemoryText.trim(),
                category: this.newMemoryCategory || 'General',
                date: new Date().toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }),
                confidence: '100%'
            };
            this.memories.unshift(newEntry);
            this.newMemoryText = '';
            this.showAddForm = false;
            this.updateBackend();
        },

        deleteMemory(id) {
            this.memories = this.memories.filter(m => m.id !== id);
            this.updateBackend();
        },

        clearAllMemories() {
            if (confirm('Are you sure you want to clear all saved memory items?')) {
                this.memories = [];
                this.updateBackend();
            }
        }
    }">

    <!-- Section Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b" style="border-color: var(--clay-card-border);">
        <div>
            <h2 class="text-xl sm:text-2xl font-bold tracking-tight" style="color: var(--text-primary);">Memory & Personal Context</h2>
            <p class="text-sm mt-1" style="color: var(--text-muted);">Manage context, preferred tools, and remembered facts that your AI assistant retains across chat sessions.</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="px-3 py-1 rounded-full text-xs font-semibold" 
                  :class="memoryEnabled ? 'bg-emerald-500/15 text-emerald-600 border border-emerald-500/30' : 'bg-amber-500/15 text-amber-600 border border-amber-500/30'">
                <span x-text="memoryEnabled ? 'Memory Active' : 'Memory Disabled'"></span>
            </span>
        </div>
    </div>

    <!-- Memory Settings & Overview -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Toggle Control -->
        <div class="lg:col-span-2 p-5 rounded-2xl border flex flex-col justify-between gap-4" style="background: var(--clay-card-bg); border-color: var(--clay-card-border); box-shadow: var(--clay-outer-shadow);">
            <div>
                <div class="flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-blue-500" style="background: var(--clay-input-bg); border: 1px solid var(--clay-card-border);">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold" style="color: var(--text-primary);">Enable Context Memory</h3>
                            <p class="text-xs mt-0.5" style="color: var(--text-muted);">Allow the AI assistant to remember coding habits, preferences, and custom instructions across conversations.</p>
                        </div>
                    </div>
                    <button type="button" @click="toggleMemoryEnabled()" 
                            class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
                            :class="memoryEnabled ? 'bg-[var(--accent)]' : 'bg-gray-300 dark:bg-gray-700'">
                        <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                              :class="memoryEnabled ? 'translate-x-5' : 'translate-x-0'"></span>
                    </button>
                </div>
            </div>
            
            <div class="p-3.5 rounded-xl text-xs flex items-center gap-3" style="background: var(--clay-input-bg); border: 1px solid var(--clay-card-border); color: var(--text-muted);">
                <svg class="w-4 h-4 text-blue-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Memory items are private to your account and can be deleted at any time.</span>
            </div>
        </div>

        <!-- Memory Counter Card -->
        <div class="p-5 rounded-2xl border flex flex-col justify-between gap-4" style="background: var(--clay-card-bg); border-color: var(--clay-card-border); box-shadow: var(--clay-outer-shadow);">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider" style="color: var(--text-muted);">Stored Memories</span>
                <span class="text-xs font-bold px-2 py-0.5 rounded-md" style="background: var(--clay-input-bg); color: var(--text-primary); border: 1px solid var(--clay-card-border);" x-text="memories.length + ' Item' + (memories.length === 1 ? '' : 's')"></span>
            </div>
            <div>
                <div class="text-3xl font-extrabold" style="color: var(--text-primary);" x-text="memories.length"></div>
                <div class="text-xs mt-1" style="color: var(--text-muted);">Active memory entries</div>
            </div>
            <div class="w-full bg-gray-200 dark:bg-gray-700 h-2 rounded-full overflow-hidden">
                <div class="bg-[var(--accent)] h-full transition-all duration-300" :style="'width: ' + Math.min(100, (memories.length / 50) * 100) + '%'"></div>
            </div>
        </div>
    </div>

    <!-- Memories List Card -->
    <div class="p-6 rounded-2xl border space-y-5" style="background: var(--clay-card-bg); border-color: var(--clay-card-border); box-shadow: var(--clay-outer-shadow);">
        <!-- Actions & Search Bar -->
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 pb-4 border-b" style="border-color: var(--clay-card-border);">
            <div class="relative flex-1 max-w-md">
                <svg class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2" style="color: var(--text-muted);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text"
                       x-model="searchQuery"
                       placeholder="Search memory entries..."
                       class="w-full rounded-xl py-2.5 pl-10 pr-4 text-xs sm:text-sm font-medium transition-all focus:outline-none"
                       style="background: var(--clay-input-bg); color: var(--text-primary); border: 1px solid var(--clay-card-border);">
            </div>

            <div class="flex items-center gap-2">
                <button type="button" 
                        @click="showAddForm = !showAddForm" 
                        class="btn btn-primary text-xs py-2 px-3.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span x-text="showAddForm ? 'Cancel' : 'Add Memory'"></span>
                </button>

                <button type="button" 
                        @click="clearAllMemories()" 
                        x-show="memories.length > 0" 
                        class="btn btn-danger-outline text-xs py-2 px-3.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Clear All
                </button>
            </div>
        </div>

        <!-- Add Memory Form -->
        <div x-show="showAddForm" x-transition class="p-4 rounded-xl border space-y-3" style="background: var(--clay-input-bg); border-color: var(--clay-card-border);">
            <h4 class="text-xs font-bold uppercase tracking-wider" style="color: var(--text-primary);">Add New Memory Item</h4>
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                <div class="sm:col-span-3">
                    <input type="text" 
                           x-model="newMemoryText" 
                           placeholder="e.g., Prefers Tailwind CSS over Bootstrap, always write unit tests..." 
                           class="field-input text-xs" 
                           @keydown.enter.prevent="addMemory()">
                </div>
                <div>
                    <select x-model="newMemoryCategory" class="field-select text-xs">
                        <option value="General">General</option>
                        <option value="Preference">Preference</option>
                        <option value="Code Style">Code Style</option>
                        <option value="Workflow">Workflow</option>
                    </select>
                </div>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" @click="showAddForm = false" class="btn btn-secondary text-xs py-1.5 px-3">Cancel</button>
                <button type="button" @click="addMemory()" class="btn btn-primary text-xs py-1.5 px-4">Save Memory</button>
            </div>
        </div>

        <!-- Memories List -->
        <div class="space-y-3">
            <template x-if="filteredMemories.length === 0">
                <div class="text-center py-10 px-4 rounded-xl border border-dashed" style="border-color: var(--clay-card-border);">
                    <svg class="w-10 h-10 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                    <p class="text-sm font-semibold" style="color: var(--text-primary);">No memories found</p>
                    <p class="text-xs mt-1" style="color: var(--text-muted);">Use the "Add Memory" button above to create a context entry for your AI assistant.</p>
                </div>
            </template>

            <template x-for="mem in filteredMemories" :key="mem.id">
                <div class="p-4 rounded-xl border flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 transition-all hover:border-[var(--accent)]"
                     style="background: var(--clay-input-bg); border-color: var(--clay-card-border);">
                    <div class="flex items-start gap-3 min-w-0 flex-1">
                        <span class="px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider shrink-0 mt-0.5"
                              style="background: var(--clay-card-bg); color: var(--accent); border: 1px solid var(--clay-card-border);"
                              x-text="mem.category || 'General'"></span>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs sm:text-sm font-medium leading-relaxed" style="color: var(--text-primary);" x-text="mem.text"></p>
                            <div class="flex items-center gap-3 mt-1 text-[11px]" style="color: var(--text-muted);">
                                <span x-text="'Saved ' + (mem.date || 'Recently')"></span>
                            </div>
                        </div>
                    </div>

                    <div class="shrink-0 self-end sm:self-center">
                        <button type="button"
                                @click="deleteMemory(mem.id)"
                                class="btn btn-danger-outline text-xs py-1.5 px-3">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            Remove
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>

