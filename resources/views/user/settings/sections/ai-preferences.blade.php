<!-- AI Preferences Section -->
<div class="space-y-6 animate-fade-in" x-data="{
        systemPrompt: `{{ addslashes($settings->system_prompt ?? '') }}`,
        charCount: 0,
        updateCharCount() {
            this.charCount = this.systemPrompt.length;
        },
        importPrompt() {
            const input = document.createElement('input');
            input.type = 'file';
            input.accept = '.txt,.md,.json';
            input.onchange = e => {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = event => {
                        this.systemPrompt = event.target.result;
                        this.updateCharCount();
                        $dispatch('notify', { message: 'System prompt imported successfully!', type: 'success' });
                    };
                    reader.readAsText(file);
                }
            };
            input.click();
        },
        exportPrompt() {
            const blob = new Blob([this.systemPrompt], { type: 'text/plain;charset=utf-8' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'custom_system_prompt.txt';
            a.click();
            $dispatch('notify', { message: 'System prompt exported to file.', type: 'success' });
        },
        resetPrompt() {
            if (confirm('Are you sure you want to reset your custom system instructions to default?')) {
                this.systemPrompt = '';
                this.updateCharCount();
                $dispatch('notify', { message: 'System prompt reset.', type: 'warning' });
            }
        },
        useTemplate(templateText) {
            this.systemPrompt = templateText;
            this.updateCharCount();
            $dispatch('notify', { message: 'Prompt template applied!', type: 'success' });
        }
    }" x-init="updateCharCount()">

    <!-- Section Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b" style="border-color: var(--clay-card-border);">
        <div>
            <h2 class="text-xl sm:text-2xl font-bold tracking-tight" style="color: var(--text-primary);">AI Assistant & Generation Preferences</h2>
            <p class="text-sm mt-1" style="color: var(--text-secondary);">Fine-tune model intelligence, reasoning depth, token parameters, and global system instructions.</p>
        </div>
        <x-settings.badge variant="accent" size="lg" icon='<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>'>
            Model Engine v3.1
        </x-settings.badge>
    </div>

    <!-- Core Model Selection & Favorites -->
    <x-settings.card title="Model Engine & Favorite Shortcuts" description="Select your default intelligence model and pin favorite models for instant switcher switching." icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>'>
        <div class="space-y-6">
            <x-settings.select name="default_model"
                               label="Default Startup AI Model"
                               description="This model is automatically assigned whenever a new conversation is started."
                               icon='<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>'
                               :options="[
                                   'mock' => '✨ Default Model (Intelligent Auto-Router)',
                                   'gemini-3.1-pro-high' => '🧠 Gemini 3.1 Pro (High Reasoning & 1M Context)',
                                   'claude-3-5-sonnet' => '🔥 Claude 3.5 Sonnet (State of the Art Coding)',
                                   'gpt-4o' => '⚡ OpenAI GPT-4o (Omni Multimodal Fast)',
                                   'deepseek-r1' => '🔬 DeepSeek R1 (Open Math & Logic Reasoning)',
                                   'mistral-large-2' => '🌪️ Mistral Large 2 (Enterprise Multilingual)'
                               ]"
                               :selected="$settings->default_model ?? 'gemini-3.1-pro-high'" />

            <div class="space-y-3 pt-3 border-t" style="border-color: var(--clay-card-border);">
                <label class="block text-sm font-semibold tracking-tight" style="color: var(--text-primary);">Favorite Quick-Switch Models</label>
                <p class="text-xs" style="color: var(--text-secondary);">Select the models to pin inside your top chat header pill selector.</p>
                
                @php
                    $favModels = $settings->preferences['favorite_models'] ?? ['gemini-3.1-pro-high', 'claude-3-5-sonnet', 'gpt-4o', 'deepseek-r1'];
                @endphp
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 pt-1" x-data="{ favs: {{ json_encode($favModels) }} }">
                    @foreach([
                        'gemini-3.1-pro-high' => 'Gemini 3.1 Pro',
                        'claude-3-5-sonnet' => 'Claude 3.5 Sonnet',
                        'gpt-4o' => 'OpenAI GPT-4o',
                        'deepseek-r1' => 'DeepSeek R1',
                        'mistral-large-2' => 'Mistral Large 2',
                        'perplexity-sonar' => 'Perplexity Sonar'
                    ] as $key => $name)
                        <label class="flex items-center gap-2.5 p-3 rounded-2xl border cursor-pointer select-none transition-all duration-200"
                               :style="favs.includes('{{ $key }}') ? 'background: var(--clay-card-bg); border-color: var(--accent); box-shadow: var(--clay-outer-shadow); color: var(--accent); font-weight: 700;' : 'background: var(--clay-input-bg); border-color: var(--clay-card-border); color: var(--text-secondary); opacity: 0.85;'">
                            <input type="checkbox"
                                   name="preferences[favorite_models][]"
                                   value="{{ $key }}"
                                   x-model="favs"
                                   class="rounded text-[var(--accent)] focus:ring-[var(--accent)] border-gray-400">
                            <span class="text-xs sm:text-sm tracking-tight truncate">{{ $name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>
    </x-settings.card>

    <!-- Sampling Parameters & Reasoning Depth -->
    <x-settings.card title="Model Sampling Parameters" description="Advanced controls for temperature, nucleus sampling, output limits, and reasoning behavior." icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>'>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <x-settings.slider name="preferences[temperature]"
                               label="Temperature (Randomness)"
                               description="Lower values produce focused, deterministic replies. Higher values increase creativity."
                               :min="0.0"
                               :max="2.0"
                               :step="0.1"
                               :value="$settings->preferences['temperature'] ?? 0.7" />

            <x-settings.slider name="preferences[top_p]"
                               label="Top P (Nucleus Sampling)"
                               description="Considers only tokens within the top P probability mass."
                               :min="0.0"
                               :max="1.0"
                               :step="0.05"
                               :value="$settings->preferences['top_p'] ?? 0.95" />

            <x-settings.slider name="preferences[max_tokens]"
                               label="Max Output Tokens Limit"
                               description="Maximum length of generated completion response per turn."
                               :min="512"
                               :max="32768"
                               :step="512"
                               :value="$settings->preferences['max_tokens'] ?? 8192"
                               unit=" tokens" />

            <x-settings.slider name="preferences[creativity]"
                               label="Creativity & Imagination Index"
                               description="Macro slider that auto-balances semantic variety vs strict precision."
                               :min="0"
                               :max="100"
                               :step="5"
                               :value="$settings->preferences['creativity'] ?? 65"
                               unit="%" />
        </div>

        <div class="pt-5 mt-3 border-t grid grid-cols-1 md:grid-cols-3 gap-6" style="border-color: var(--clay-card-border);">
            <x-settings.segmented-control name="preferences[response_length]"
                                          label="Default Response Length"
                                          :options="[
                                              'concise' => ['value' => 'concise', 'label' => 'Concise', 'description' => 'Direct answers only'],
                                              'balanced' => ['value' => 'balanced', 'label' => 'Balanced', 'description' => 'Ideal detail & pace'],
                                              'detailed' => ['value' => 'detailed', 'label' => 'Detailed', 'description' => 'Comprehensive & thorough']
                                          ]"
                                          :selected="$settings->preferences['response_length'] ?? 'balanced'"
                                          columns="3" />

            <x-settings.segmented-control name="preferences[thinking_style]"
                                          label="Thinking & Reasoning Style"
                                          :options="[
                                              'fast' => ['value' => 'fast', 'label' => 'Fast', 'description' => 'Instant generation'],
                                              'balanced' => ['value' => 'balanced', 'label' => 'Balanced', 'description' => 'Smart trade-off'],
                                              'deep' => ['value' => 'deep', 'label' => 'Deep Reasoning', 'description' => 'Multi-step CoT thinking']
                                          ]"
                                          :selected="$settings->preferences['thinking_style'] ?? 'balanced'"
                                          columns="3" />

            <x-settings.segmented-control name="preferences[coding_style]"
                                          label="Coding Output Format"
                                          :options="[
                                              'markdown' => ['value' => 'markdown', 'label' => 'Markdown Blocks', 'description' => 'Rich fenced code'],
                                              'plaintext' => ['value' => 'plaintext', 'label' => 'Plain Text', 'description' => 'Unformatted text'],
                                              'highlight' => ['value' => 'highlight', 'label' => 'Syntax Highlighting', 'description' => 'Collab IDE theme']
                                          ]"
                                          :selected="$settings->preferences['coding_style'] ?? 'markdown'"
                                          columns="3" />
        </div>

        <!-- Assistant Intelligence Capabilities Toggles -->
        <div class="pt-5 mt-3 border-t space-y-3" style="border-color: var(--clay-card-border);">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-settings.toggle name="preferences[reasoning_mode]"
                                   label="Enable Explicit Reasoning Mode"
                                   description="Allow model to display intermediate scratchpad reasoning chains before answering."
                                   :checked="!empty($settings->preferences['reasoning_mode']) || !isset($settings->preferences['reasoning_mode'])" />

                <x-settings.toggle name="preferences[always_explain_code]"
                                   label="Always Explain Code Snippets"
                                   description="Automatically append step-by-step logic breakdowns below generated code."
                                   :checked="!empty($settings->preferences['always_explain_code'])" />

                <x-settings.toggle name="preferences[show_references]"
                                   label="Show Web References & Citations"
                                   description="Display clickable source URLs and citation chips when browsing web knowledge."
                                   :checked="!empty($settings->preferences['show_references']) || !isset($settings->preferences['show_references'])" />

                <x-settings.toggle name="preferences[show_confidence]"
                                   label="Display Confidence Score Badge"
                                   description="Show estimated factual confidence metrics on technical queries."
                                   :checked="!empty($settings->preferences['show_confidence'])" />

                <x-settings.toggle name="preferences[enable_web_search]"
                                   label="Live Web Search Integration"
                                   description="Automatically search live web indexes when queries require current 2026 data."
                                   :checked="!empty($settings->preferences['enable_web_search']) || !isset($settings->preferences['enable_web_search'])" />

                <x-settings.toggle name="preferences[enable_memory]"
                                   label="Autonomous Memory & Recall"
                                   description="Allow AI to remember your project context across different chat sessions."
                                   :checked="!empty($settings->preferences['enable_memory']) || !isset($settings->preferences['enable_memory'])" />

                <x-settings.toggle name="preferences[auto_title_chats]"
                                   label="Auto-Generate Conversation Titles"
                                   description="Automatically summarize the first query into a clean 3-5 word thread title."
                                   :checked="!empty($settings->preferences['auto_title_chats']) || !isset($settings->preferences['auto_title_chats'])" />
            </div>
        </div>
    </x-settings.card>

    <!-- Custom System Prompt & Prompt Library -->
    <x-settings.card title="Custom System Instructions (System Prompt)" description="Define universal behavioral instructions, persona boundaries, and tone across all conversations." icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>'>
        <div class="space-y-4">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-mono px-2.5 py-1 rounded-lg border" style="background: var(--clay-input-bg); border-color: var(--clay-card-border); color: var(--text-secondary);">
                        <span x-text="charCount">0</span> / 4,096 characters
                    </span>
                    <span x-show="charCount > 3500" class="text-xs text-[var(--warning)] font-semibold animate-pulse" style="display: none;">
                        Approaching token budget warning
                    </span>
                </div>

                <div class="flex items-center gap-2">
                    <button type="button" @click="importPrompt()" class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all hover:scale-105 border flex items-center gap-1.5" style="background: var(--clay-input-bg); border-color: var(--clay-card-border); color: var(--text-primary);">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        Import
                    </button>
                    <button type="button" @click="exportPrompt()" class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all hover:scale-105 border flex items-center gap-1.5" style="background: var(--clay-input-bg); border-color: var(--clay-card-border); color: var(--text-primary);">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Export
                    </button>
                    <button type="button" @click="resetPrompt()" class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all hover:scale-105 border flex items-center gap-1.5" style="background: rgba(239, 68, 68, 0.12); border-color: rgba(239, 68, 68, 0.3); color: var(--danger);">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        Reset
                    </button>
                </div>
            </div>

            <textarea name="system_prompt"
                      x-model="systemPrompt"
                      @input="updateCharCount()"
                      rows="7"
                      maxlength="4096"
                      placeholder="You are XrootAI, an advanced enterprise pair-programming assistant. Always follow these guidelines:&#10;1. Provide production-ready, clean, modular code.&#10;2. Use Markdown formatting with accurate syntax highlighting.&#10;3. Explain non-obvious architecture choices clearly..."
                      class="w-full rounded-2xl p-4 sm:p-5 font-mono text-sm leading-relaxed transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-[var(--accent)] resize-y"
                      style="background: var(--clay-input-bg); color: var(--text-primary); border: 1px solid var(--clay-card-border); box-shadow: var(--clay-input-shadow); min-height: 160px;"></textarea>

            <p class="text-xs leading-normal" style="color: var(--text-secondary);">
                Tip: These instructions will be injected as the top-level <code class="font-mono text-[var(--accent)]">system</code> role for all newly initiated chats.
            </p>
        </div>

        <!-- Prompt Templates Library Accordion -->
        <div class="pt-5 mt-4 border-t space-y-3" style="border-color: var(--clay-card-border);">
            <x-settings.accordion title="Prompt Library & Curated Personas" subtitle="Pick from award-winning enterprise templates to instantly populate your system prompt." icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 62v-6m0 0v-6m0 6h6m-6 0H6"/></svg>' badge="6 Presets">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @php
                        $presets = [
                            [
                                'title' => 'Senior Full-Stack Architect',
                                'desc' => 'Specializes in Laravel, Tailwind, and high-scale cloud distributed systems.',
                                'prompt' => 'You are a Senior Full-Stack Architect with 15+ years of expertise specializing in Laravel, PHP 8.3, modern Blade components, and Tailwind CSS. Always prioritize security, modularity, strict typing, and elegant software design patterns.'
                            ],
                            [
                                'title' => 'Code Review & Security Auditor',
                                'desc' => 'Scans snippets for SQL injection, XSS, N+1 query leaks, and race conditions.',
                                'prompt' => 'You are an expert Security Engineer and Code Reviewer. When reviewing code, meticulously check for vulnerability flaws including SQLi, XSS, CSRF, IDOR, memory leaks, and inefficient N+1 database queries. Provide clean remediated diffs.'
                            ],
                            [
                                'title' => 'Product Manager & UX Strategist',
                                'desc' => 'Helps outline product requirements, user stories, and conversion-focused UX flows.',
                                'prompt' => 'You are an award-winning Product Manager and Senior UI/UX Designer. Help structure clear PRDs, user stories, wireframe flows, and conversion-optimized micro-interactions with empathetic user focus.'
                            ],
                            [
                                'title' => 'Data Science & SQL Optimizer',
                                'desc' => 'Crafts high-performance complex SQL queries, migrations, and schema indexes.',
                                'prompt' => 'You are a Principal Database Administrator and Data Scientist. Focus on writing high-concurrency, index-optimized SQL queries, clean Eloquent ORM relationships, and rigorous statistical analysis.'
                            ]
                        ];
                    @endphp

                    @foreach($presets as $preset)
                        <div class="p-4 rounded-2xl border flex flex-col justify-between gap-3 transition-all duration-200 hover:border-[var(--accent)]"
                             style="background: var(--clay-input-bg); border-color: var(--clay-card-border);">
                            <div>
                                <h6 class="text-sm font-bold tracking-tight" style="color: var(--text-primary);">{{ $preset['title'] }}</h6>
                                <p class="text-xs mt-1 leading-normal" style="color: var(--text-secondary);">{{ $preset['desc'] }}</p>
                            </div>
                            <div class="flex justify-end pt-2">
                                <button type="button"
                                        @click="useTemplate(`{{ addslashes($preset['prompt']) }}`)"
                                        class="px-3 py-1.5 rounded-xl text-xs font-semibold transition-all hover:scale-105 shadow-sm"
                                        style="background: var(--clay-card-bg); color: var(--accent); border: 1px solid var(--clay-card-border);">
                                    Use Preset
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-settings.accordion>
        </div>
    </x-settings.card>
</div>
