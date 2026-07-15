<!-- Privacy Section -->
<div class="space-y-6 animate-fade-in" x-data="{
        showDeleteAccountModal: false,
        deleteConfirmText: '',
        exportArchive(format) {
            $dispatch('notify', { message: `Generating comprehensive account export package (${format.toUpperCase()}). Download will start in a few seconds...`, type: 'accent' });
        },
        confirmDeleteAccount() {
            if (this.deleteConfirmText !== 'DELETE') {
                $dispatch('notify', { message: 'You must type DELETE in all capital letters to confirm account deletion.', type: 'danger' });
                return;
            }
            alert('Account deletion scheduled. Your data will be permanently wiped within 72 hours per GDPR regulations.');
            this.showDeleteAccountModal = false;
        }
    }">

    <!-- Section Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b" style="border-color: var(--clay-card-border);">
        <div>
            <h2 class="text-xl sm:text-2xl font-bold tracking-tight" style="color: var(--text-primary);">Data Privacy, GDPR Compliance & Exports</h2>
            <p class="text-sm mt-1" style="color: var(--text-secondary);">Take full sovereign control over your conversation telemetry, model training opt-outs, cookie consent, and data export.</p>
        </div>
        <x-settings.badge variant="success" size="lg" icon='<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>'>
            GDPR Compliant
        </x-settings.badge>
    </div>

    <!-- AI Training & Telemetry Controls Card -->
    <x-settings.card title="AI Training & Telemetry Controls" description="Decide how your prompt text and completions interact with upstream foundation providers." icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/></svg>'>
        <div class="space-y-4">
            <x-settings.toggle name="preferences[opt_out_training]"
                               label="Do Not Use My Chat Data for AI Model Training"
                               description="Strictly prohibit OpenAI, Anthropic, Google, or third-party vendors from logging or using your workspace conversations to train future model releases."
                               :checked="!empty($settings->preferences['opt_out_training']) || !isset($settings->preferences['opt_out_training'])" />

            <x-settings.toggle name="preferences[opt_out_analytics]"
                               label="Opt-Out of Application Telemetry & Analytics"
                               description="Disable anonymous feature interaction tracking and UI click-path analytics."
                               :checked="!empty($settings->preferences['opt_out_analytics'])" />
        </div>

        <div class="pt-5 mt-4 border-t grid grid-cols-1 md:grid-cols-3 gap-6" style="border-color: var(--clay-card-border);">
            <div class="space-y-1">
                <span class="text-xs font-bold uppercase tracking-wider block" style="color: var(--text-primary);">Strictly Necessary Cookies</span>
                <p class="text-[11px] leading-relaxed opacity-75" style="color: var(--text-secondary);">Required for session authentication, CSRF security, and theme persistence.</p>
                <span class="text-xs font-bold text-emerald-500 block pt-1">Always Enabled (Mandatory)</span>
            </div>

            <div class="space-y-2">
                <x-settings.toggle name="preferences[cookie_functional]"
                                   label="Functional Cookies"
                                   description="Remember workspace layout dimensions and custom sidebar preferences."
                                   :checked="!empty($settings->preferences['cookie_functional']) || !isset($settings->preferences['cookie_functional'])" />
            </div>

            <div class="space-y-2">
                <x-settings.toggle name="preferences[cookie_performance]"
                                   label="Performance Cookies"
                                   description="Measure token stream latency and network gateway diagnostic timing."
                                   :checked="!empty($settings->preferences['cookie_performance']) || !isset($settings->preferences['cookie_performance'])" />
            </div>
        </div>
    </x-settings.card>

    <!-- Data Export & GDPR Rights Card -->
    <x-settings.card title="Data Export & Portability (GDPR Article 20)" description="Download your complete personal data archive including chat history, custom prompts, and settings in portable machine-readable formats." icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>'>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="p-5 rounded-2xl border space-y-3 flex flex-col justify-between" style="background: var(--clay-input-bg); border-color: var(--clay-card-border);">
                <div>
                    <h6 class="text-sm font-bold tracking-tight" style="color: var(--text-primary);">Full JSON Data Archive</h6>
                    <p class="text-xs mt-1 leading-normal" style="color: var(--text-secondary);">Complete raw JSON export of all conversations, vector memories, and settings.</p>
                </div>
                <button type="button" @click="exportArchive('json')" class="w-full py-2 rounded-xl text-xs font-bold transition-transform hover:scale-105 shadow border flex items-center justify-center gap-1.5" style="background: var(--clay-card-bg); color: var(--accent); border-color: var(--clay-card-border);">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Download JSON Archive
                </button>
            </div>

            <div class="p-5 rounded-2xl border space-y-3 flex flex-col justify-between" style="background: var(--clay-input-bg); border-color: var(--clay-card-border);">
                <div>
                    <h6 class="text-sm font-bold tracking-tight" style="color: var(--text-primary);">CSV Spreadsheet Logs</h6>
                    <p class="text-xs mt-1 leading-normal" style="color: var(--text-secondary);">Tabular CSV export containing message turns, timestamps, and token usage tables.</p>
                </div>
                <button type="button" @click="exportArchive('csv')" class="w-full py-2 rounded-xl text-xs font-bold transition-transform hover:scale-105 shadow border flex items-center justify-center gap-1.5" style="background: var(--clay-card-bg); color: var(--accent); border-color: var(--clay-card-border);">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Download CSV Logs
                </button>
            </div>

            <div class="p-5 rounded-2xl border space-y-3 flex flex-col justify-between" style="background: var(--clay-input-bg); border-color: var(--clay-card-border);">
                <div>
                    <h6 class="text-sm font-bold tracking-tight" style="color: var(--text-primary);">Markdown Notebooks</h6>
                    <p class="text-xs mt-1 leading-normal" style="color: var(--text-secondary);">Export every chat thread as an individual GitHub-flavored Markdown file.</p>
                </div>
                <button type="button" @click="exportArchive('markdown')" class="w-full py-2 rounded-xl text-xs font-bold transition-transform hover:scale-105 shadow border flex items-center justify-center gap-1.5" style="background: var(--clay-card-bg); color: var(--accent); border-color: var(--clay-card-border);">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Export as Markdown
                </button>
            </div>
        </div>
    </x-settings.card>

    <!-- Danger Zone Card -->
    <div class="p-6 sm:p-8 rounded-[28px] border-2 space-y-6 transition-all shadow-lg"
         style="background: rgba(239, 68, 68, 0.06); border-color: rgba(239, 68, 68, 0.3);">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-lg font-bold tracking-tight text-red-500 dark:text-red-400">Account Danger Zone</h3>
                <p class="text-xs sm:text-sm mt-1 leading-relaxed opacity-90 max-w-xl" style="color: var(--text-secondary);">
                    Permanently delete your entire workspace, all user accounts, custom prompts, stored BYOK keys, and conversation logs. This action is irreversible once the 72-hour grace period expires.
                </p>
            </div>
            <button type="button"
                    @click="showDeleteAccountModal = true"
                    class="px-6 py-3 rounded-2xl text-xs sm:text-sm font-bold bg-red-600 text-white shadow-xl hover:bg-red-700 transition-all shrink-0">
                Delete Account & Wipe Data
            </button>
        </div>
    </div>

    <!-- Modal: Type-to-Confirm Account Deletion -->
    <div x-show="showDeleteAccountModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 backdrop-blur-md bg-black/60" style="display: none;">
        <div class="max-w-md w-full rounded-[28px] p-6 sm:p-8 border shadow-2xl space-y-6" style="background: var(--clay-card-bg); border-color: var(--clay-card-border);">
            <div class="flex items-center gap-3 text-[var(--danger)]">
                <svg class="w-8 h-8 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <h3 class="text-lg font-bold tracking-tight" style="color: var(--text-primary);">Confirm Account Destruction</h3>
            </div>
            <p class="text-xs sm:text-sm leading-relaxed" style="color: var(--text-secondary);">
                To verify this action, please type <code class="font-bold text-red-500 bg-red-500/10 px-1.5 py-0.5 rounded">DELETE</code> in the box below:
            </p>
            <div class="space-y-2">
                <input type="text" x-model="deleteConfirmText" placeholder="Type DELETE here..." class="w-full rounded-2xl py-3 px-4 text-center font-mono font-bold text-lg tracking-widest transition-all focus:ring-2 focus:ring-red-500" style="background: var(--clay-input-bg); color: var(--text-primary); border: 1px solid var(--clay-card-border);">
            </div>
            <div class="pt-4 border-t flex justify-end gap-3" style="border-color: var(--clay-card-border);">
                <button type="button" @click="showDeleteAccountModal = false" class="px-4 py-2.5 rounded-xl text-sm font-semibold border" style="background: var(--clay-input-bg); color: var(--text-primary);">Cancel</button>
                <button type="button" @click="confirmDeleteAccount()" class="px-5 py-2.5 rounded-xl text-sm font-bold bg-red-600 text-white hover:bg-red-700 transition-all">Permanently Delete Everything</button>
            </div>
        </div>
    </div>
</div>
