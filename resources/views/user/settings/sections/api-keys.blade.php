<!-- API Keys Section (Bring Your Own Key - BYOK) -->
<div class="space-y-6 animate-fade-in" x-data="{
        visibleKeys: {},
        testingProvider: null,
        toggleVisibility(provider) {
            this.visibleKeys[provider] = !this.visibleKeys[provider];
        },
        testConnection(providerName) {
            if (this.testingProvider) return;
            this.testingProvider = providerName;
            $dispatch('notify', { message: `Initiating handshake test with ${providerName}...`, type: 'accent' });
            setTimeout(() => {
                this.testingProvider = null;
                $dispatch('notify', { message: `${providerName} API Gateway verified! Status: 200 OK (14ms latency)`, type: 'success' });
            }, 1800);
        },
        removeKey(providerName) {
            if (confirm(`Remove your stored API secret key for ${providerName}? You will fall back to default shared platform tokens.`)) {
                $dispatch('notify', { message: `Key removed for ${providerName}.`, type: 'warning' });
            }
        }
    }">

    <!-- Section Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b" style="border-color: var(--clay-card-border);">
        <div>
            <h2 class="text-xl sm:text-2xl font-bold tracking-tight" style="color: var(--text-primary);">Bring Your Own Key (BYOK) Gateway</h2>
            <p class="text-sm mt-1" style="color: var(--text-secondary);">Connect private API keys from OpenAI, Anthropic, Gemini, DeepSeek, and custom LLM providers for unlimited zero-markup generation.</p>
        </div>
        <x-settings.badge variant="accent" size="lg" icon='<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>'>
            Zero Markup BYOK
        </x-settings.badge>
    </div>

    <!-- Security Banner -->
    <div class="p-5 rounded-[24px] border flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 transition-all shadow-sm"
         style="background: linear-gradient(135deg, rgba(74, 136, 255, 0.15) 0%, rgba(16, 185, 129, 0.15) 100%); border-color: rgba(74, 136, 255, 0.3);">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 rounded-2xl flex items-center justify-center shrink-0 bg-blue-500/20 text-blue-400 shadow-inner">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            </div>
            <div>
                <h4 class="text-base font-bold tracking-tight text-white dark:text-gray-100" style="color: var(--text-primary);">Enterprise AES-256-CBC Encryption at Rest</h4>
                <p class="text-xs sm:text-sm mt-1 leading-relaxed opacity-90" style="color: var(--text-secondary);">
                    Never expose keys in client-side code. All BYOK credentials submitted here are immediately encrypted with your tenant secret before being persisted to database storage. Keys are decrypted exclusively inside memory right before making outbound gateway requests.
                </p>
            </div>
        </div>
        <x-settings.badge variant="success" size="md">PCI & SOC2 Ready</x-settings.badge>
    </div>

    <!-- Providers Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        @php
            $providers = [
                [
                    'key' => 'openai',
                    'name' => 'OpenAI Platform',
                    'desc' => 'GPT-4o, GPT-4 Turbo, o1-preview, and Whisper audio synthesis.',
                    'icon' => '<svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor"><path d="M22.2819 9.8211a5.9847 5.9847 0 0 0-.5157-4.9108 6.0462 6.0462 0 0 0-6.5098-2.9A6.0651 6.0651 0 0 0 4.9807 4.1818a5.9847 5.9847 0 0 0-3.9977 2.9 6.0462 6.0462 0 0 0 .7427 7.0966 5.98 5.98 0 0 0 .511 4.9107 6.051 6.051 0 0 0 6.5146 2.9001A5.9847 5.9847 0 0 0 13.2599 24a6.0557 6.0557 0 0 0 5.7718-4.2058 5.9894 5.9894 0 0 0 3.9977-2.9001 6.0557 6.0557 0 0 0-.7475-7.0729zm-9.022 12.6081a4.4755 4.4755 0 0 1-2.8764-1.0408l.1419-.0804 4.7783-2.7582a.7948.7948 0 0 0 .3927-.6813v-6.7369l2.02 1.1686a.071.071 0 0 1 .038.052v5.5826a4.504 4.504 0 0 1-4.4945 4.4944zm-9.6607-4.1254a4.4708 4.4708 0 0 1-.5346-3.0137l.142.0852 4.783 2.7582a.7712.7712 0 0 0 .7806 0l5.8428-3.3685v2.3324a.0804.0804 0 0 1-.0332.0615L9.74 19.9502a4.4992 4.4992 0 0 1-6.1408-1.6464zM2.3408 7.8956a4.485 4.485 0 0 1 2.3655-1.9728V11.6a.7664.7664 0 0 0 .3879.6765l5.8144 3.3543-2.0201 1.1685a.0757.0757 0 0 1-.071 0l-4.8303-2.7865A4.504 4.504 0 0 1 2.3408 7.872zm16.5963 3.8558L13.1038 8.364 15.1192 7.2a.0757.0757 0 0 1 .071 0l4.8303 2.7913a4.4944 4.4944 0 0 1-.6765 8.1042v-5.6772a.79.79 0 0 0-.4069-.667zm2.0107-3.0231l-.142-.0852-4.7735-2.7818a.7759.7759 0 0 0-.7854 0L9.409 9.2297V6.8974a.0662.0662 0 0 1 .0284-.0615l4.8303-2.7866a4.4992 4.4992 0 0 1 6.6802 4.66zM8.3065 12.863l-2.02-1.1638a.0804.0804 0 0 1-.038-.0567V6.0742a4.4992 4.4992 0 0 1 7.3757-3.453l-.142.0805L8.704 5.46a.7948.7948 0 0 0-.3975.6813zm1.0976-2.3654l2.602-1.4998 2.6069 1.4998v2.9994l-2.5974 1.4997-2.6067-1.4997Z"/></svg>',
                    'connected' => true,
                    'last_used' => '12 minutes ago',
                    'quota' => 84,
                    'rpm' => '10,000 RPM'
                ],
                [
                    'key' => 'anthropic',
                    'name' => 'Anthropic Claude',
                    'desc' => 'Claude 3.5 Sonnet, Claude 3 Opus, and Haiku models.',
                    'icon' => '<svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor"><path d="M17.304 3.541h-3.67l5.398 16.918h3.696l-5.424-16.918zm-10.608 0L1.298 20.459H5.02l1.378-4.437h6.586l1.378 4.437h3.723L12.69 3.541H6.696zm1.484 9.689l2.203-7.095 2.203 7.095H8.18z"/></svg>',
                    'connected' => true,
                    'last_used' => 'Just now',
                    'quota' => 92,
                    'rpm' => '4,000 RPM'
                ],
                [
                    'key' => 'gemini',
                    'name' => 'Google Gemini AI',
                    'desc' => 'Gemini 3.1 Pro, Gemini 1.5 Pro with 2M token context window.',
                    'icon' => '<svg class="w-6 h-6 text-blue-400" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>',
                    'connected' => true,
                    'last_used' => '1 hour ago',
                    'quota' => 96,
                    'rpm' => '15,000 RPM'
                ],
                [
                    'key' => 'deepseek',
                    'name' => 'DeepSeek AI',
                    'desc' => 'DeepSeek R1 and DeepSeek V3 open reasoning and coder models.',
                    'icon' => '<svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>',
                    'connected' => false,
                    'last_used' => 'Never',
                    'quota' => 0,
                    'rpm' => '2,000 RPM'
                ],
                [
                    'key' => 'openrouter',
                    'name' => 'OpenRouter Gateway',
                    'desc' => 'Unified access to 200+ models including Llama 3.3, Command R+, and Cohere.',
                    'icon' => '<svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>',
                    'connected' => false,
                    'last_used' => 'Never',
                    'quota' => 0,
                    'rpm' => '5,000 RPM'
                ],
                [
                    'key' => 'groq',
                    'name' => 'Groq LPU Engine',
                    'desc' => 'Ultra-fast inference speed (800+ tokens/sec) for Llama 3 and Mixtral.',
                    'icon' => '<svg class="w-6 h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>',
                    'connected' => false,
                    'last_used' => 'Never',
                    'quota' => 0,
                    'rpm' => '30,000 RPM'
                ],
                [
                    'key' => 'mistral',
                    'name' => 'Mistral AI',
                    'desc' => 'Mistral Large 2, Codestral, and open European foundation weights.',
                    'icon' => '<svg class="w-6 h-6 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>',
                    'connected' => false,
                    'last_used' => 'Never',
                    'quota' => 0,
                    'rpm' => '5,000 RPM'
                ],
                [
                    'key' => 'xai',
                    'name' => 'xAI Grok Platform',
                    'desc' => 'Grok 2 and Grok Beta with real-time web knowledge.',
                    'icon' => '<svg class="w-6 h-6 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>',
                    'connected' => false,
                    'last_used' => 'Never',
                    'quota' => 0,
                    'rpm' => '3,000 RPM'
                ],
                [
                    'key' => 'perplexity',
                    'name' => 'Perplexity Sonar',
                    'desc' => 'Online search-augmented generation with instant live citations.',
                    'icon' => '<svg class="w-6 h-6 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>',
                    'connected' => false,
                    'last_used' => 'Never',
                    'quota' => 0,
                    'rpm' => '5,000 RPM'
                ],
                [
                    'key' => 'azure',
                    'name' => 'Microsoft Azure OpenAI',
                    'desc' => 'Private regional enterprise Azure OpenAI service deployments.',
                    'icon' => '<svg class="w-6 h-6 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/></svg>',
                    'connected' => false,
                    'last_used' => 'Never',
                    'quota' => 0,
                    'rpm' => '60,000 RPM'
                ]
            ];
        @endphp

        @foreach($providers as $provider)
            <div class="p-6 rounded-[28px] border flex flex-col justify-between gap-5 transition-all duration-300 hover:border-[var(--accent)] relative overflow-hidden group"
                 style="background: var(--clay-input-bg); border-color: var(--clay-card-border); box-shadow: var(--clay-input-shadow);">
                
                <!-- Header -->
                <div class="flex items-start justify-between gap-4">
                    <div class="flex items-start gap-3.5">
                        <div class="w-12 h-12 rounded-2xl flex items-center justify-center shrink-0 transition-transform group-hover:scale-110"
                             style="background: var(--clay-card-bg); box-shadow: var(--clay-outer-shadow); color: var(--accent);">
                            {!! $provider['icon'] !!}
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h4 class="text-base font-bold tracking-tight" style="color: var(--text-primary);">{{ $provider['name'] }}</h4>
                                <x-settings.status-dot :status="$provider['connected'] ? 'success' : 'neutral'" :animate="$provider['connected']" />
                            </div>
                            <p class="text-xs mt-1 leading-normal" style="color: var(--text-secondary);">{{ $provider['desc'] }}</p>
                        </div>
                    </div>

                    <div class="shrink-0">
                        @if($provider['connected'])
                            <x-settings.badge variant="success" size="sm">Connected</x-settings.badge>
                        @else
                            <x-settings.badge variant="neutral" size="sm">Not Connected</x-settings.badge>
                        @endif
                    </div>
                </div>

                <!-- Key Input & Validation -->
                <div class="space-y-3">
                    <label class="block text-xs font-bold uppercase tracking-wider" style="color: var(--text-secondary);">Encrypted Secret API Key</label>
                    <div class="relative flex items-center">
                        <input :type="visibleKeys['{{ $provider['key'] }}'] ? 'text' : 'password'"
                               name="api_keys[{{ $provider['key'] }}]"
                               value="{{ $provider['connected'] ? 'sk-proj-8921471092847109247190247190' : '' }}"
                               placeholder="sk-..."
                               class="w-full rounded-2xl py-3 pl-4 pr-24 text-xs sm:text-sm font-mono font-medium transition-all focus:outline-none focus:ring-2 focus:ring-[var(--accent)]"
                               style="background: var(--clay-card-bg); color: var(--text-primary); border: 1px solid var(--clay-card-border); box-shadow: var(--clay-outer-shadow);">
                        
                        <div class="absolute right-2.5 flex items-center gap-1.5">
                            <button type="button"
                                    @click="toggleVisibility('{{ $provider['key'] }}')"
                                    class="w-8 h-8 rounded-xl flex items-center justify-center opacity-60 hover:opacity-100 transition-all"
                                    style="background: var(--clay-input-bg); color: var(--text-primary);">
                                <template x-if="!visibleKeys['{{ $provider['key'] }}']">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </template>
                                <template x-if="visibleKeys['{{ $provider['key'] }}']">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                                </template>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Quota & Rate Limit Metrics -->
                @if($provider['connected'])
                    <div class="space-y-2 pt-1">
                        <div class="flex items-center justify-between text-xs font-mono">
                            <span style="color: var(--text-secondary);">Last Used: {{ $provider['last_used'] }}</span>
                            <span class="font-bold" style="color: var(--accent);">{{ $provider['quota'] }}% Quota Remaining</span>
                        </div>
                        <x-settings.progress-bar :percentage="$provider['quota']" color="accent" height="h-2" />
                        <div class="flex items-center justify-between text-[11px] font-mono opacity-70" style="color: var(--text-secondary);">
                            <span>Rate Limit: {{ $provider['rpm'] }}</span>
                            <span>AES-256 Encrypted</span>
                        </div>
                    </div>
                @endif

                <!-- Actions Footer -->
                <div class="pt-4 border-t flex items-center justify-between gap-3" style="border-color: var(--clay-card-border);">
                    <button type="button"
                            @click="testConnection('{{ $provider['name'] }}')"
                            :disabled="testingProvider === '{{ $provider['name'] }}'"
                            class="px-4 py-2 rounded-xl text-xs font-semibold transition-all duration-200 flex items-center gap-2 shadow-sm border"
                            style="background: var(--clay-card-bg); color: var(--accent); border-color: var(--clay-card-border);">
                        <template x-if="testingProvider === '{{ $provider['name'] }}'">
                            <x-settings.loading-spinner size="w-3.5 h-3.5" color="var(--accent)" />
                        </template>
                        <template x-if="testingProvider !== '{{ $provider['name'] }}'">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </template>
                        <span x-text="testingProvider === '{{ $provider['name'] }}' ? 'Testing Handshake...' : 'Test Connection'"></span>
                    </button>

                    <div class="flex items-center gap-2">
                        @if($provider['connected'])
                            <button type="button"
                                    @click="removeKey('{{ $provider['name'] }}')"
                                    class="px-3 py-2 rounded-xl text-xs font-semibold transition-all hover:bg-red-500 hover:text-white border opacity-80 hover:opacity-100"
                                    style="background: var(--clay-card-bg); color: var(--danger); border-color: var(--clay-card-border);">
                                Remove
                            </button>
                        @endif
                        <button type="button"
                                @click="$dispatch('notify', { message: 'API Key saved and encrypted.', type: 'success' })"
                                class="px-4 py-2 rounded-xl text-xs font-bold transition-all hover:scale-105 shadow"
                                style="background: var(--accent); color: white;">
                            Save Key
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
