<!-- Connected Apps Section -->
<div class="space-y-6 animate-fade-in" x-data="{
        connected: {
            github: true,
            notion: true,
            google: false,
            slack: false,
            discord: false,
            dropbox: false,
            onedrive: false
        },
        toggleApp(app, name) {
            this.connected[app] = !this.connected[app];
            if (this.connected[app]) {
                $dispatch('notify', { message: `Successfully authorized OAuth handshake with ${name}!`, type: 'success' });
            } else {
                $dispatch('notify', { message: `Disconnected ${name} integration and revoked access tokens.`, type: 'warning' });
            }
        },
        generatePat() {
            const pat = 'xroot_pat_' + Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15);
            navigator.clipboard.writeText(pat);
            $dispatch('notify', { message: 'New Personal Access Token generated & copied to clipboard!', type: 'success' });
        }
    }">

    <!-- Section Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b" style="border-color: var(--clay-card-border);">
        <div>
            <h2 class="text-xl sm:text-2xl font-bold tracking-tight" style="color: var(--text-primary);">Integrations & Connected OAuth Applications</h2>
            <p class="text-sm mt-1" style="color: var(--text-secondary);">Link cloud workspace tools to allow your AI assistant to directly index docs, analyze code repos, and trigger webhooks.</p>
        </div>
        <x-settings.badge variant="accent" size="lg" icon='<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>'>
            OAuth 2.0 Hub
        </x-settings.badge>
    </div>

    <!-- Integrations Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @php
            $apps = [
                [
                    'key' => 'github',
                    'name' => 'GitHub Repositories',
                    'desc' => 'Direct repository indexing, code review bots, and automated PR diff suggestions.',
                    'icon' => '<svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.53 1.032 1.53 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z"/></svg>',
                    'badge' => 'repo, user, workflow',
                    'last_sync' => '2 hours ago'
                ],
                [
                    'key' => 'notion',
                    'name' => 'Notion Workspace Sync',
                    'desc' => 'Read and write directly to Notion docs, project databases, and team wikis.',
                    'icon' => '<svg class="w-7 h-7" viewBox="0 0 24 24" fill="currentColor"><path d="M4.459 4.208c.746.606 1.026.56 2.428.466l13.215-.793c.68-.047.886.233.727.886L18.666 18.9c-.186.886-.653 1.119-1.493 1.166l-11.816.746c-.7.047-.933-.233-.886-.886L5.672 5.047c.047-.653-.373-.886-1.213-.84z"/></svg>',
                    'badge' => 'read/write content',
                    'last_sync' => 'Yesterday'
                ],
                [
                    'key' => 'google',
                    'name' => 'Google Drive & Docs Integration',
                    'desc' => 'Import Google Docs, Sheets, and PDF presentations directly into your chat context window.',
                    'icon' => '<svg class="w-7 h-7 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/></svg>',
                    'badge' => 'drive.readonly',
                    'last_sync' => 'Never'
                ],
                [
                    'key' => 'slack',
                    'name' => 'Slack Team Bot integration',
                    'desc' => 'Deploy the XrootAI pair programming assistant bot directly into your Slack channels and DMs.',
                    'icon' => '<svg class="w-7 h-7 text-purple-400" viewBox="0 0 24 24" fill="currentColor"><path d="M5.042 15.165a2.528 2.528 0 0 1-2.52 2.523A2.528 2.528 0 0 1 0 15.165a2.527 2.527 0 0 1 2.522-2.52h2.52v2.52zM6.313 15.165a2.527 2.527 0 0 1 2.521-2.52 2.527 2.527 0 0 1 2.521 2.52v6.313A2.528 2.528 0 0 1 8.834 24a2.528 2.528 0 0 1-2.521-2.522v-6.313zM8.834 5.042a2.528 2.528 0 0 1-2.521-2.52A2.528 2.528 0 0 1 8.834 0a2.528 2.528 0 0 1 2.521 2.522v2.52H8.834zM8.834 6.313a2.528 2.528 0 0 1 2.521 2.521 2.528 2.528 0 0 1-2.521 2.521H2.522A2.528 2.528 0 0 1 0 8.834a2.528 2.528 0 0 1 2.522-2.521h6.312z"/></svg>',
                    'badge' => 'chat:write, bot',
                    'last_sync' => 'Never'
                ],
                [
                    'key' => 'discord',
                    'name' => 'Discord Webhook & Bot Integration',
                    'desc' => 'Send completion digests and code review summaries directly to your Discord server.',
                    'icon' => '<svg class="w-7 h-7 text-indigo-400" viewBox="0 0 24 24" fill="currentColor"><path d="M20.317 4.37a19.791 19.791 0 0 0-4.885-1.515.074.074 0 0 0-.079.037c-.21.375-.444.864-.608 1.25a18.27 18.27 0 0 0-5.487 0 12.64 12.64 0 0 0-.617-1.25.077.077 0 0 0-.079-.037A19.736 19.736 0 0 0 3.677 4.37a.07.07 0 0 0-.032.027C.533 9.046-.32 13.58.099 18.057a.082.082 0 0 0 .031.057 19.9 19.9 0 0 0 5.993 3.03.078.078 0 0 0 .084-.028c.462-.63.874-1.295 1.226-1.994.021-.041.001-.09-.041-.106a13.107 13.107 0 0 1-1.872-.892.077.077 0 0 1-.008-.128 10.2 10.2 0 0 0 .372-.292.074.074 0 0 1 .077-.01c3.928 1.793 8.18 1.793 12.061 0a.074.074 0 0 1 .078.01c.12.098.246.198.373.292a.077.077 0 0 1-.006.127 12.299 12.299 0 0 1-1.873.892.077.077 0 0 0-.041.107c.36.698.772 1.362 1.225 1.993a.076.076 0 0 0 .084.028 19.839 19.839 0 0 0 6.002-3.03.077.077 0 0 0 .032-.054c.5-5.177-.838-9.674-3.549-13.66a.061.061 0 0 0-.031-.028zM8.02 15.33c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.956-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.956 2.418-2.157 2.418zm7.975 0c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.955-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.946 2.418-2.157 2.418z"/></svg>',
                    'badge' => 'webhooks, messages',
                    'last_sync' => 'Never'
                ],
                [
                    'key' => 'dropbox',
                    'name' => 'Dropbox Cloud Storage',
                    'desc' => 'Sync folder assets and large datasets directly into your active conversation workspace.',
                    'icon' => '<svg class="w-7 h-7 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/></svg>',
                    'badge' => 'files.content.read',
                    'last_sync' => 'Never'
                ]
            ];
        @endphp

        @foreach($apps as $app)
            <div class="p-6 rounded-[28px] border flex flex-col justify-between gap-5 transition-all duration-300 hover:border-[var(--accent)]"
                 style="background: var(--clay-input-bg); border-color: var(--clay-card-border); box-shadow: var(--clay-input-shadow);">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex items-start gap-3.5">
                        <div class="w-12 h-12 rounded-2xl flex items-center justify-center shrink-0" style="background: var(--clay-card-bg); box-shadow: var(--clay-outer-shadow); color: var(--accent);">
                            {!! $app['icon'] !!}
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h4 class="text-base font-bold tracking-tight" style="color: var(--text-primary);">{{ $app['name'] }}</h4>
                                <template x-if="connected.{{ $app['key'] }}">
                                    <x-settings.status-dot status="success" />
                                </template>
                            </div>
                            <p class="text-xs mt-1 leading-normal" style="color: var(--text-secondary);">{{ $app['desc'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t flex items-center justify-between text-xs font-mono" style="border-color: var(--clay-card-border);">
                    <div class="flex items-center gap-2 opacity-70">
                        <span>Scope:</span>
                        <code class="px-1.5 py-0.5 rounded bg-black/10 dark:bg-white/10">{{ $app['badge'] }}</code>
                    </div>
                    <button type="button"
                            @click="toggleApp('{{ $app['key'] }}', '{{ $app['name'] }}')"
                            class="px-4 py-2 rounded-xl text-xs font-bold transition-transform hover:scale-105 shadow border"
                            :style="connected.{{ $app['key'] }} ? 'background: var(--clay-card-bg); color: var(--danger); border-color: var(--clay-card-border);' : 'background: var(--accent); color: white; border: none;'">
                        <span x-text="connected.{{ $app['key'] }} ? 'Disconnect' : 'Connect App'"></span>
                    </button>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Webhook & API Access Card -->
    <x-settings.card title="Personal Access Tokens & Webhook Triggers" description="Generate personal API tokens for CLI scripts, VS Code plugins, and custom outbound event webhooks." icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>'>
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h6 class="text-sm font-bold tracking-tight" style="color: var(--text-primary);">Active Developer API Tokens</h6>
                    <p class="text-xs" style="color: var(--text-secondary);">Used to authenticate local terminal sessions and custom REST clients.</p>
                </div>
                <button type="button" @click="generatePat()" class="px-4 py-2 rounded-xl text-xs font-bold shadow transition-transform hover:scale-105" style="background: var(--accent); color: white;">
                    + Generate New Token
                </button>
            </div>

            <!-- Existing token item -->
            <div class="p-4 rounded-2xl border flex items-center justify-between gap-4 font-mono text-xs" style="background: var(--clay-input-bg); border-color: var(--clay-card-border);">
                <div>
                    <span class="font-bold text-sm block" style="color: var(--text-primary);">VSCode Extension Hub (Token #1)</span>
                    <span class="opacity-70" style="color: var(--text-secondary);">xroot_pat_•••••••••••••••••••••••••a89f</span>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-[11px] text-emerald-500 font-bold">Last Used: 5 mins ago</span>
                    <button type="button" @click="$dispatch('notify', { message: 'Token revoked.', type: 'warning' })" class="text-red-500 hover:underline">Revoke</button>
                </div>
            </div>
        </div>
    </x-settings.card>
</div>
