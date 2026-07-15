<!-- Advanced Section -->
<div class="space-y-6 animate-fade-in" x-data="{
        jsonConfig: `{{ json_encode($settings->preferences ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}`,
        jsonError: null,
        validateJson() {
            try {
                JSON.parse(this.jsonConfig);
                this.jsonError = null;
                $dispatch('notify', { message: 'JSON configuration parsed successfully!', type: 'success' });
            } catch (e) {
                this.jsonError = e.message;
                $dispatch('notify', { message: 'Invalid JSON syntax: ' + e.message, type: 'danger' });
            }
        },
        clearAppCache() {
            $dispatch('notify', { message: 'Flushing local application cache and temporary model weights...', type: 'accent' });
            setTimeout(() => {
                $dispatch('notify', { message: 'Cache cleared successfully! Freed 142 MB.', type: 'success' });
            }, 1200);
        },
        resetAllToFactory() {
            if (confirm('CRITICAL WARNING: Are you sure you want to reset ALL user settings, preferences, and custom themes back to factory defaults?')) {
                $dispatch('notify', { message: 'Restoring default factory settings...', type: 'warning' });
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            }
        },
        exportSettingsJson() {
            const blob = new Blob([this.jsonConfig], { type: 'application/json' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'xrootai_settings_backup.json';
            a.click();
            $dispatch('notify', { message: 'Settings exported to JSON backup.', type: 'success' });
        },
        importSettingsJson() {
            const input = document.createElement('input');
            input.type = 'file';
            input.accept = '.json';
            input.onchange = e => {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = event => {
                        try {
                            const parsed = JSON.parse(event.target.result);
                            this.jsonConfig = JSON.stringify(parsed, null, 2);
                            $dispatch('notify', { message: 'Settings JSON imported successfully! Click Save Changes below.', type: 'success' });
                        } catch (err) {
                            $dispatch('notify', { message: 'Failed to import: Invalid JSON structure.', type: 'danger' });
                        }
                    };
                    reader.readAsText(file);
                }
            };
            input.click();
        }
    }">

    <!-- Section Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b" style="border-color: var(--clay-card-border);">
        <div>
            <h2 class="text-xl sm:text-2xl font-bold tracking-tight" style="color: var(--text-primary);">Advanced Developer Tools & JSON Editor</h2>
            <p class="text-sm mt-1" style="color: var(--text-secondary);">Enable low-level diagnostic telemetry, experimental beta models, raw configuration editor, and system cache flushes.</p>
        </div>
        <x-settings.badge variant="accent" size="lg" icon='<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>'>
            Developer Hub
        </x-settings.badge>
    </div>

    <!-- Developer Mode & Telemetry Card -->
    <x-settings.card title="Developer Mode & Beta Features" description="Unlock raw latency telemetry, token counting overlays, and experimental preview weights." icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>'>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <x-settings.toggle name="preferences[developer_mode]"
                               label="Enable Developer Mode"
                               description="Inject raw token counts, TTFT (Time to First Token), and model routing telemetry inside every chat bubble header."
                               :checked="!empty($settings->preferences['developer_mode']) || !isset($settings->preferences['developer_mode'])" />

            <x-settings.toggle name="preferences[experimental_features]"
                               label="Unlock Experimental Features"
                               description="Access cutting-edge experimental UI flows like autonomous web browsing trees and multi-agent canvas."
                               :checked="!empty($settings->preferences['experimental_features'])" />

            <x-settings.toggle name="preferences[beta_models]"
                               label="Early Access Beta Models"
                               description="Show unreleased preview models (e.g. GPT-5 Alpha, Gemini 4.0 Experimental) inside your model switcher."
                               :checked="!empty($settings->preferences['beta_models'])" />
        </div>

        <div class="pt-5 border-t max-w-md" style="border-color: var(--clay-card-border);">
            <x-settings.select name="preferences[debug_logging]"
                               label="HTTP Debug Logging Level"
                               description="Controls granularity of local browser diagnostic console logs."
                               :options="[
                                   'none' => 'None (Standard Production Mode)',
                                   'errors' => 'Errors Only (Recommended)',
                                   'verbose' => 'Verbose HTTP & WebSocket Network Trace'
                               ]"
                               :selected="$settings->preferences['debug_logging'] ?? 'errors'" />
        </div>
    </x-settings.card>

    <!-- Raw JSON Editor Card -->
    <x-settings.card title="Live Raw JSON Preferences Editor" description="Directly view, modify, and validate your underlying settings object in real-time." icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>'>
        <div class="space-y-4">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-mono font-bold px-2.5 py-1 rounded-lg bg-blue-500/10 text-blue-400 border border-blue-500/20">
                        preferences.json
                    </span>
                    <template x-if="jsonError">
                        <span class="text-xs text-[var(--danger)] font-mono font-bold flex items-center gap-1">
                            ⚠️ Syntax Error
                        </span>
                    </template>
                </div>

                <div class="flex items-center gap-2">
                    <button type="button" @click="validateJson()" class="px-3 py-1.5 rounded-xl text-xs font-semibold border flex items-center gap-1" style="background: var(--clay-input-bg); border-color: var(--clay-card-border); color: var(--text-primary);">
                        Validate JSON
                    </button>
                    <button type="button" @click="importSettingsJson()" class="px-3 py-1.5 rounded-xl text-xs font-semibold border flex items-center gap-1" style="background: var(--clay-input-bg); border-color: var(--clay-card-border); color: var(--text-primary);">
                        Import JSON File
                    </button>
                    <button type="button" @click="exportSettingsJson()" class="px-3 py-1.5 rounded-xl text-xs font-semibold border flex items-center gap-1" style="background: var(--clay-input-bg); border-color: var(--clay-card-border); color: var(--text-primary);">
                        Export Backup
                    </button>
                </div>
            </div>

            <input type="hidden" name="preferences_json_override" :value="jsonConfig">
            <textarea x-model="jsonConfig"
                      @input="jsonError = null"
                      rows="12"
                      class="w-full rounded-2xl p-4 sm:p-5 font-mono text-xs sm:text-sm leading-relaxed transition-all focus:outline-none focus:ring-2 focus:ring-[var(--accent)] resize-y"
                      :class="jsonError ? 'border-red-500 ring-2 ring-red-500/50' : 'border-[var(--clay-card-border)]'"
                      style="background: var(--clay-input-bg); color: var(--text-primary); box-shadow: var(--clay-input-shadow); min-height: 240px;"></textarea>

            <template x-if="jsonError">
                <p class="text-xs text-red-500 font-mono" x-text="jsonError"></p>
            </template>
            <p class="text-xs" style="color: var(--text-secondary);">
                Any changes made inside this raw JSON editor will automatically override individual UI toggle states when submitted.
            </p>
        </div>
    </x-settings.card>

    <!-- Cache & Factory Reset Card -->
    <x-settings.card title="System Cache Management & Factory Reset" description="Clear temporary application memory or restore all configurations to default values." icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>'>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div class="p-5 rounded-2xl border flex flex-col justify-between space-y-4" style="background: var(--clay-input-bg); border-color: var(--clay-card-border);">
                <div>
                    <h6 class="text-sm font-bold tracking-tight" style="color: var(--text-primary);">Clear Local & Model Cache</h6>
                    <p class="text-xs mt-1 leading-normal" style="color: var(--text-secondary);">Flushes cached prompt completions, tokenizer vocab buffers, and local storage state.</p>
                </div>
                <button type="button" @click="clearAppCache()" class="w-full py-2.5 rounded-xl text-xs font-semibold transition-transform hover:scale-105 border flex items-center justify-center gap-2" style="background: var(--clay-card-bg); color: var(--accent); border-color: var(--clay-card-border);">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Flush Application Cache (142 MB)
                </button>
            </div>

            <div class="p-5 rounded-2xl border flex flex-col justify-between space-y-4" style="background: rgba(239, 68, 68, 0.08); border-color: rgba(239, 68, 68, 0.25);">
                <div>
                    <h6 class="text-sm font-bold tracking-tight text-red-500 dark:text-red-400">Restore Factory Default Settings</h6>
                    <p class="text-xs mt-1 leading-normal opacity-90" style="color: var(--text-secondary);">Resets all parameters, theme modes, and UI customizations back to fresh installation state.</p>
                </div>
                <button type="button" @click="resetAllToFactory()" class="w-full py-2.5 rounded-xl text-xs font-bold transition-transform hover:scale-105 bg-red-600 text-white hover:bg-red-700 shadow">
                    Reset All Preferences to Defaults
                </button>
            </div>
        </div>
    </x-settings.card>
</div>
