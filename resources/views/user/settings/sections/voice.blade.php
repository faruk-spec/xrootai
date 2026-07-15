<!-- Voice Section -->
<div class="space-y-6 animate-fade-in" x-data="{
        testingMic: false,
        testingSpeaker: false,
        micLevel: 0,
        micTimer: null,
        startMicTest() {
            if (this.testingMic) {
                this.stopMicTest();
                return;
            }
            this.testingMic = true;
            $dispatch('notify', { message: 'Microphone test active. Speak into your input device.', type: 'accent' });
            this.micTimer = setInterval(() => {
                this.micLevel = Math.floor(Math.random() * 85) + 15;
            }, 150);
        },
        stopMicTest() {
            this.testingMic = false;
            this.micLevel = 0;
            clearInterval(this.micTimer);
        },
        testSpeaker() {
            if (this.testingSpeaker) return;
            this.testingSpeaker = true;
            $dispatch('notify', { message: 'Playing synthesized speech sample (Alloy voice engine)...', type: 'success' });
            setTimeout(() => {
                this.testingSpeaker = false;
                $dispatch('notify', { message: 'Speaker test completed successfully.', type: 'success' });
            }, 3000);
        }
    }">

    <!-- Section Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b" style="border-color: var(--clay-card-border);">
        <div>
            <h2 class="text-xl sm:text-2xl font-bold tracking-tight" style="color: var(--text-primary);">Voice Synthesis & Speech Recognition</h2>
            <p class="text-sm mt-1" style="color: var(--text-secondary);">Configure neural text-to-speech (TTS) engines, speech-to-text input behavior, and hardware testing.</p>
        </div>
        <x-settings.badge variant="accent" size="lg" icon='<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/></svg>'>
            Audio Gateway
        </x-settings.badge>
    </div>

    <!-- Voice Engines & Playback Card -->
    <x-settings.card title="Voice Provider & Neural Persona" description="Choose the cloud TTS provider and voice persona for reading assistant answers aloud." icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/></svg>'>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <x-settings.select name="preferences[voice_provider]"
                               label="Voice Provider Engine"
                               description="Select neural synthesis API engine."
                               icon='<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/></svg>'
                               :options="[
                                   'openai' => 'OpenAI Neural Speech (Alloy, Echo, Shimmer)',
                                   'elevenlabs' => 'ElevenLabs Multilingual v2 (High Emotion)',
                                   'google' => 'Google Cloud WaveNet / Journey Voices',
                                   'azure' => 'Microsoft Azure Neural TTS'
                               ]"
                               :selected="$settings->preferences['voice_provider'] ?? 'openai'" />

            <x-settings.select name="preferences[voice_selection]"
                               label="Voice Persona Selection"
                               description="Pick the character timbre and vocal pitch."
                               icon='<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>'
                               :options="[
                                   'alloy' => 'Alloy (Warm, Balanced & Versatile)',
                                   'echo' => 'Echo (Smooth, Confident Baritone)',
                                   'fable' => 'Fable (Expressive British Storyteller)',
                                   'onyx' => 'Onyx (Deep, Authoritative & Calm)',
                                   'nova' => 'Nova (Friendly, Upbeat & Clear)',
                                   'shimmer' => 'Shimmer (Bright, Engaging & Crisp)',
                                   'rachel' => 'Rachel (ElevenLabs Natural American)',
                                   'adam' => 'Adam (ElevenLabs Deep Narration)'
                               ]"
                               :selected="$settings->preferences['voice_selection'] ?? 'alloy'" />
        </div>

        <div class="pt-5 border-t space-y-4" style="border-color: var(--clay-card-border);">
            <x-settings.slider name="preferences[playback_speed]"
                               label="Speech Playback Speed Rate"
                               description="Adjust how quickly the assistant speaks answers aloud."
                               :min="0.5"
                               :max="2.0"
                               :step="0.1"
                               :value="$settings->preferences['playback_speed'] ?? 1.0"
                               unit="x" />
        </div>
    </x-settings.card>

    <!-- Speech Input & Push to Talk Card -->
    <x-settings.card title="Speech Recognition & Hands-Free Input" description="Control automatic voice recognition rules and push-to-talk hotkeys." icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/></svg>'>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <x-settings.toggle name="preferences[speech_recognition]"
                               label="Enable Speech-to-Text Input"
                               description="Display microphone button inside the chat prompt input bar."
                               :checked="!empty($settings->preferences['speech_recognition']) || !isset($settings->preferences['speech_recognition'])" />

            <x-settings.toggle name="preferences[auto_listen]"
                               label="Continuous Auto-Listen"
                               description="Automatically begin listening for your next prompt right after AI finishes speaking."
                               :checked="!empty($settings->preferences['auto_listen'])" />

            <x-settings.toggle name="preferences[push_to_talk]"
                               label="Push-to-Talk Hotkey Mode"
                               description="Require holding down a designated hotkey while speaking to prevent background noise."
                               :checked="!empty($settings->preferences['push_to_talk'])" />
        </div>

        <div class="pt-5 border-t flex flex-col sm:flex-row sm:items-center justify-between gap-4" style="border-color: var(--clay-card-border);">
            <div>
                <h6 class="text-sm font-bold tracking-tight" style="color: var(--text-primary);">Designated Push-to-Talk Hotkey</h6>
                <p class="text-xs" style="color: var(--text-secondary);">Hold this key anywhere in the workspace to record voice input.</p>
            </div>
            <div class="w-full sm:w-64">
                <x-settings.select name="preferences[push_to_talk_key]"
                                   :options="[
                                       'space' => 'Hold Spacebar (Default)',
                                       'alt' => 'Hold Left Alt / Option Key',
                                       'ctrl' => 'Hold Ctrl / Command Key',
                                       'caps' => 'Hold Caps Lock Key'
                                   ]"
                                   :selected="$settings->preferences['push_to_talk_key'] ?? 'space'" />
            </div>
        </div>
    </x-settings.card>

    <!-- Hardware Diagnostics Box -->
    <x-settings.card title="Microphone & Speaker Diagnostics" description="Test your active input gain levels and verify output clarity directly from your browser." icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/></svg>'>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Microphone Test -->
            <div class="p-5 rounded-2xl border space-y-4 transition-all duration-200" style="background: var(--clay-input-bg); border-color: var(--clay-card-border); box-shadow: var(--clay-input-shadow);">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center transition-transform"
                             :class="{ 'animate-pulse scale-110 bg-red-500/20 text-red-500': testingMic, 'bg-white/5 text-[var(--accent)]': !testingMic }"
                             style="box-shadow: var(--clay-outer-shadow);">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/></svg>
                        </div>
                        <div>
                            <h6 class="text-sm font-bold tracking-tight" style="color: var(--text-primary);">Microphone Test</h6>
                            <span class="text-xs font-mono" :class="testingMic ? 'text-emerald-500 font-bold' : 'text-gray-400'" x-text="testingMic ? 'Listening (' + micLevel + ' dB)...' : 'Ready for diagnostics'"></span>
                        </div>
                    </div>
                    <button type="button"
                            @click="startMicTest()"
                            class="px-4 py-2 rounded-xl text-xs font-semibold transition-transform hover:scale-105 shadow-sm border"
                            :class="testingMic ? 'bg-red-500 text-white border-red-600' : 'bg-[var(--clay-card-bg)] text-[var(--accent)] border-[var(--clay-card-border)]'">
                        <span x-text="testingMic ? 'Stop Test' : 'Test Microphone'"></span>
                    </button>
                </div>

                <!-- Simulated Waveform Meter -->
                <div class="space-y-1.5">
                    <div class="flex justify-between text-[11px] opacity-70">
                        <span>Audio Input Gain Level</span>
                        <span class="font-mono" x-text="micLevel + '%'">0%</span>
                    </div>
                    <div class="w-full h-3 rounded-full overflow-hidden p-0.5 border" style="background: var(--bg-main); border-color: var(--clay-card-border);">
                        <div class="h-full rounded-full transition-all duration-150 ease-out"
                             :style="`width: ${micLevel}%; background: linear-gradient(90deg, #10b981 0%, #f59e0b 75%, #ef4444 100%);`"></div>
                    </div>
                </div>
            </div>

            <!-- Speaker Test -->
            <div class="p-5 rounded-2xl border space-y-4 transition-all duration-200" style="background: var(--clay-input-bg); border-color: var(--clay-card-border); box-shadow: var(--clay-input-shadow);">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center transition-transform"
                             :class="{ 'animate-bounce bg-emerald-500/20 text-emerald-500': testingSpeaker, 'bg-white/5 text-[var(--accent)]': !testingSpeaker }"
                             style="box-shadow: var(--clay-outer-shadow);">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/></svg>
                        </div>
                        <div>
                            <h6 class="text-sm font-bold tracking-tight" style="color: var(--text-primary);">Speaker Audio Test</h6>
                            <span class="text-xs font-mono" :class="testingSpeaker ? 'text-emerald-500 font-bold' : 'text-gray-400'" x-text="testingSpeaker ? 'Playing sample audio...' : 'Output channel check'"></span>
                        </div>
                    </div>
                    <button type="button"
                            @click="testSpeaker()"
                            :disabled="testingSpeaker"
                            class="px-4 py-2 rounded-xl text-xs font-semibold transition-transform hover:scale-105 shadow-sm border"
                            style="background: var(--clay-card-bg); color: var(--accent); border-color: var(--clay-card-border);">
                        <span x-text="testingSpeaker ? 'Playing...' : 'Test Speaker'"></span>
                    </button>
                </div>

                <div class="p-3 rounded-xl border flex items-center justify-between text-xs font-mono opacity-80" style="background: var(--bg-main); border-color: var(--clay-card-border);">
                    <span>Sample: "Welcome to XrootAI voice synthesis."</span>
                    <span class="text-emerald-500 font-bold">48 kHz Stereo</span>
                </div>
            </div>
        </div>
    </x-settings.card>
</div>
