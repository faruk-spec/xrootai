<!-- Chat Experience Section -->
<div class="space-y-6 animate-fade-in" x-data="{
        archiveAllChats() {
            if (confirm('Archive all inactive chat threads older than 14 days into zip/archive storage?')) {
                $dispatch('notify', { message: 'Archiving process initiated in background.', type: 'success' });
            }
        }
    }">

    <!-- Section Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b" style="border-color: var(--clay-card-border);">
        <div>
            <h2 class="text-xl sm:text-2xl font-bold tracking-tight" style="color: var(--text-primary);">Chat Interface & Messaging Ergonomics</h2>
            <p class="text-sm mt-1" style="color: var(--text-secondary);">Customize keyboard send shortcuts, streaming physics, code block syntax themes, and conversation history organization.</p>
        </div>
        <x-settings.badge variant="accent" size="lg" icon='<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>'>
            Ergonomics
        </x-settings.badge>
    </div>

    <!-- Keyboard & Layout Box -->
    <x-settings.card title="Input Ergonomics & Message Width" description="Configure enter shortcut behaviors and container width across ultra-wide monitors." icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>'>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <x-settings.segmented-control name="preferences[send_shortcut]"
                                          label="Enter Key Action Shortcut"
                                          description="How the return key behaves inside the message prompt input."
                                          :options="[
                                              'enter' => ['value' => 'enter', 'label' => 'Enter to Send', 'description' => 'Shift+Enter for newline'],
                                              'shift_enter' => ['value' => 'shift_enter', 'label' => 'Shift+Enter to Send', 'description' => 'Enter adds newline (IDE mode)']
                                          ]"
                                          :selected="$settings->preferences['send_shortcut'] ?? 'enter'"
                                          columns="2" />

            <x-settings.segmented-control name="preferences[message_width]"
                                          label="Chat Container Max-Width"
                                          description="Adjust the maximum horizontal width of message logs."
                                          :options="[
                                              '768' => ['value' => '768', 'label' => 'Standard (768px)', 'description' => 'Focused reading'],
                                              '1024' => ['value' => '1024', 'label' => 'Wide (1024px)', 'description' => 'Recommended'],
                                              'full' => ['value' => 'full', 'label' => 'Ultra-Wide / Full', 'description' => 'Stretches monitor']
                                          ]"
                                          :selected="$settings->preferences['message_width'] ?? '1024'"
                                          columns="3" />
        </div>
    </x-settings.card>

    <!-- Streaming & Rendering Toggles -->
    <x-settings.card title="Streaming & Code Block Formatting" description="Control live token typing animations, code syntax themes, and copy tools." icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>'>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <x-settings.select name="preferences[code_theme]"
                               label="Code Block Syntax Theme"
                               description="Select the color palette for Markdown code snippets."
                               icon='<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>'
                               :options="[
                                   'github-dark' => 'GitHub Dark (Default Enterprise)',
                                   'monokai' => 'Monokai High-Contrast',
                                   'dracula' => 'Dracula Dark & Pink',
                                   'one-dark' => 'One Dark Pro (Atom IDE)',
                                   'nord' => 'Nord Arctic Frost'
                               ]"
                               :selected="$settings->preferences['code_theme'] ?? 'github-dark'" />

            <div class="space-y-3 pt-2">
                <x-settings.toggle name="preferences[typing_indicator]"
                                   label="Show AI Typing Indicator"
                                   description="Display the animated 3-dot thinking indicator while the model prepares tokens."
                                   :checked="!empty($settings->preferences['typing_indicator']) || !isset($settings->preferences['typing_indicator'])" />

                <x-settings.toggle name="preferences[streaming_animation]"
                                   label="Smooth Token Streaming Animation"
                                   description="Render incoming words with smooth incremental typing physics instead of block jumps."
                                   :checked="!empty($settings->preferences['streaming_animation']) || !isset($settings->preferences['streaming_animation'])" />
            </div>
        </div>

        <div class="pt-5 border-t grid grid-cols-1 md:grid-cols-3 gap-6" style="border-color: var(--clay-card-border);">
            <x-settings.toggle name="preferences[auto_scroll]"
                               label="Auto-Scroll to Bottom"
                               description="Keep viewport locked to the newest streaming message."
                               :checked="!empty($settings->preferences['auto_scroll']) || !isset($settings->preferences['auto_scroll'])" />

            <x-settings.toggle name="preferences[copy_buttons]"
                               label="Show Copy Buttons on Code Blocks"
                               description="Display one-click copy and language tags inside fenced code blocks."
                               :checked="!empty($settings->preferences['copy_buttons']) || !isset($settings->preferences['copy_buttons'])" />

            <x-settings.toggle name="preferences[message_timestamps]"
                               label="Display Message Timestamps"
                               description="Show exact timestamp on hover or beside every message turn."
                               :checked="!empty($settings->preferences['message_timestamps']) || !isset($settings->preferences['message_timestamps'])" />
        </div>
    </x-settings.card>

    <!-- History, Folders & Retention Card -->
    <x-settings.card title="Chat History, Folders & Auto-Deletion" description="Organize pinned threads into folders and set automatic data cleanup rules." icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>'>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <x-settings.toggle name="preferences[chat_history]"
                               label="Save Chat History & Logs"
                               description="Store conversation logs across devices for future search and retrieval."
                               :checked="!empty($settings->preferences['chat_history']) || !isset($settings->preferences['chat_history'])" />

            <x-settings.toggle name="preferences[pinned_chats]"
                               label="Enable Pinned Chats Section"
                               description="Keep critical project threads pinned permanently at the top of your sidebar."
                               :checked="!empty($settings->preferences['pinned_chats']) || !isset($settings->preferences['pinned_chats'])" />

            <x-settings.toggle name="preferences[folders_enabled]"
                               label="Organize with Custom Folders"
                               description="Allow grouping conversations into hierarchical custom folders and tags."
                               :checked="!empty($settings->preferences['folders_enabled']) || !isset($settings->preferences['folders_enabled'])" />
        </div>

        <div class="pt-5 border-t flex flex-col sm:flex-row sm:items-center justify-between gap-4" style="border-color: var(--clay-card-border);">
            <div class="flex-1 max-w-sm">
                <x-settings.select name="preferences[auto_delete_history]"
                                   label="Auto-Delete History Schedule"
                                   description="Automatically purge old conversation history after a specified timeframe."
                                   :options="[
                                       'never' => 'Never Delete (Keep Indefinitely)',
                                       '30days' => 'Auto-Delete after 30 Days',
                                       '90days' => 'Auto-Delete after 90 Days',
                                       '1year' => 'Auto-Delete after 1 Year'
                                   ]"
                                   :selected="$settings->preferences['auto_delete_history'] ?? 'never'" />
            </div>

            <div class="flex items-center gap-3 self-end sm:self-center">
                <div class="text-right">
                    <span class="text-xs block" style="color: var(--text-secondary);">Total Active Threads</span>
                    <span class="text-sm font-bold" style="color: var(--text-primary);">{{ $conversationsCount ?? 12 }} conversations</span>
                </div>
                <button type="button" @click="archiveAllChats()" class="px-4 py-2.5 rounded-xl text-xs sm:text-sm font-semibold transition-transform hover:scale-105 border flex items-center gap-2" style="background: var(--clay-input-bg); border-color: var(--clay-card-border); color: var(--text-primary);">
                    <svg class="w-4 h-4 text-[var(--accent)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                    Archive All Inactive Chats
                </button>
            </div>
        </div>
    </x-settings.card>
</div>
