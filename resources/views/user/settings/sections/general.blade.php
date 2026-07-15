<!-- General Section -->
<div class="space-y-6 animate-fade-in">
    <!-- Section Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b" style="border-color: var(--clay-card-border);">
        <div>
            <h2 class="text-xl sm:text-2xl font-bold tracking-tight" style="color: var(--text-primary);">General Account & Localization</h2>
            <p class="text-sm mt-1" style="color: var(--text-secondary);">Manage your personal profile identity, regional preferences, and default workspace environment.</p>
        </div>
        <x-settings.badge variant="accent" size="lg" icon='<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>'>
            Personal Profile
        </x-settings.badge>
    </div>

    <!-- Identity & Profile Card -->
    <x-settings.card title="Profile Information" description="Update your display name and public username." icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-2">
                <label class="block text-sm font-semibold tracking-tight" style="color: var(--text-primary);">Display Name</label>
                <input type="text"
                       name="preferences[display_name]"
                       value="{{ old('preferences.display_name', $user->name) }}"
                       class="w-full rounded-2xl py-3 px-4 text-sm font-medium transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-[var(--accent)]"
                       style="background: var(--clay-input-bg); color: var(--text-primary); border: 1px solid var(--clay-card-border); box-shadow: var(--clay-input-shadow);"
                       placeholder="Enter your full name">
                <p class="text-xs" style="color: var(--text-secondary);">This name is displayed in your workspace and on team chats.</p>
            </div>

            <div class="space-y-2">
                <label class="block text-sm font-semibold tracking-tight" style="color: var(--text-primary);">Username</label>
                <div class="relative flex items-center">
                    <span class="absolute left-4 font-mono text-sm opacity-60" style="color: var(--text-secondary);">@</span>
                    <input type="text"
                           name="preferences[username]"
                           value="{{ old('preferences.username', $settings->preferences['username'] ?? strtolower(str_replace(' ', '_', $user->name))) }}"
                           class="w-full rounded-2xl py-3 pl-8 pr-4 text-sm font-mono font-medium transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-[var(--accent)]"
                           style="background: var(--clay-input-bg); color: var(--text-primary); border: 1px solid var(--clay-card-border); box-shadow: var(--clay-input-shadow);"
                           placeholder="username">
                </div>
                <p class="text-xs" style="color: var(--text-secondary);">Your unique identifier across shared AI prompts and mentions.</p>
            </div>

            <div class="space-y-2 md:col-span-2">
                <label class="block text-sm font-semibold tracking-tight" style="color: var(--text-primary);">Email Address</label>
                <div class="flex flex-col sm:flex-row gap-3">
                    <input type="email"
                           value="{{ $user->email }}"
                           disabled
                           class="flex-1 rounded-2xl py-3 px-4 text-sm font-medium opacity-70 cursor-not-allowed"
                           style="background: var(--clay-input-bg); color: var(--text-primary); border: 1px solid var(--clay-card-border); box-shadow: var(--clay-input-shadow);">
                    <button type="button"
                            @click="activeSection = 'security'"
                            class="px-5 py-3 rounded-2xl text-xs sm:text-sm font-semibold transition-transform duration-200 hover:scale-105 shrink-0 shadow-sm"
                            style="background: var(--clay-card-bg); color: var(--accent); border: 1px solid var(--clay-card-border); box-shadow: var(--clay-outer-shadow);">
                        Change Email Address
                    </button>
                </div>
                <p class="text-xs" style="color: var(--text-secondary);">Primary email used for account notifications, login, and billing invoices.</p>
            </div>
        </div>
    </x-settings.card>

    <!-- Regional & Localization Card -->
    <x-settings.card title="Localization & Time Formatting" description="Adjust date, time, and language formatting for your global workflow." icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <x-settings.select name="preferences[language]"
                               label="UI Language"
                               description="Select the language for interface navigation and system labels."
                               icon='<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/></svg>'
                               :options="[
                                   'en' => 'English (United States)',
                                   'en-gb' => 'English (United Kingdom)',
                                   'es' => 'Español (Spanish)',
                                   'fr' => 'Français (French)',
                                   'de' => 'Deutsch (German)',
                                   'ja' => '日本語 (Japanese)',
                                   'zh' => '中文 (Simplified Chinese)',
                                   'pt' => 'Português (Portuguese)',
                                   'hi' => 'हिन्दी (Hindi)'
                               ]"
                               :selected="$settings->preferences['language'] ?? 'en'" />

            <x-settings.select name="preferences[response_language]"
                               label="Preferred AI Response Language"
                               description="Instruct the assistant to default responses to this language."
                               icon='<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>'
                               :options="[
                                   'auto' => 'Auto-Detect (Match User Query)',
                                   'en' => 'Always Respond in English',
                                   'es' => 'Always Respond in Spanish',
                                   'fr' => 'Always Respond in French',
                                   'de' => 'Always Respond in German',
                                   'ja' => 'Always Respond in Japanese'
                               ]"
                               :selected="$settings->preferences['response_language'] ?? 'auto'" />

            <x-settings.select name="preferences[timezone]"
                               label="Timezone"
                               description="Your local timezone for message timestamps and scheduled tasks."
                               icon='<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
                               :options="[
                                   'UTC' => 'UTC (Coordinated Universal Time)',
                                   'America/New_York' => 'Eastern Time (US & Canada)',
                                   'America/Chicago' => 'Central Time (US & Canada)',
                                   'America/Denver' => 'Mountain Time (US & Canada)',
                                   'America/Los_Angeles' => 'Pacific Time (US & Canada)',
                                   'Europe/London' => 'London / Dublin (GMT/BST)',
                                   'Europe/Paris' => 'Paris / Berlin / Rome (CET)',
                                   'Asia/Tokyo' => 'Tokyo / Osaka (JST)',
                                   'Asia/Kolkata' => 'India Standard Time (IST)',
                                   'Australia/Sydney' => 'Sydney / Melbourne (AEST)'
                               ]"
                               :selected="$settings->preferences['timezone'] ?? 'UTC'" />

            <x-settings.select name="preferences[region]"
                               label="Region & Currency"
                               description="Sets default currency ($/€/£/¥) and regional server routing."
                               icon='<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>'
                               :options="[
                                   'us' => 'United States ($ USD)',
                                   'eu' => 'European Union (€ EUR)',
                                   'uk' => 'United Kingdom (£ GBP)',
                                   'jp' => 'Japan (¥ JPY)',
                                   'global' => 'Global Routing (Dynamic)'
                               ]"
                               :selected="$settings->preferences['region'] ?? 'global'" />

            <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
                <x-settings.segmented-control name="preferences[date_format]"
                                              label="Date Format"
                                              description="How dates are displayed across chat logs."
                                              :options="[
                                                  'YYYY-MM-DD' => ['value' => 'YYYY-MM-DD', 'label' => '2026-07-10'],
                                                  'DD/MM/YYYY' => ['value' => 'DD/MM/YYYY', 'label' => '10/07/2026'],
                                                  'MM/DD/YYYY' => ['value' => 'MM/DD/YYYY', 'label' => '07/10/2026']
                                              ]"
                                              :selected="$settings->preferences['date_format'] ?? 'YYYY-MM-DD'" />

                <x-settings.segmented-control name="preferences[time_format]"
                                              label="Time Format"
                                              description="12-hour AM/PM vs 24-hour military clock."
                                              :options="[
                                                  '12h' => ['value' => '12h', 'label' => '07:48 PM'],
                                                  '24h' => ['value' => '24h', 'label' => '19:48']
                                              ]"
                                              :selected="$settings->preferences['time_format'] ?? '12h'" />
            </div>
        </div>

        <div class="pt-4 border-t space-y-3" style="border-color: var(--clay-card-border);">
            <x-settings.toggle name="preferences[auto_detect_timezone]"
                               label="Auto-Detect Timezone & Location"
                               description="Automatically sync your timezone whenever you sign in from a new geographic location."
                               :checked="!empty($settings->preferences['auto_detect_timezone']) || !isset($settings->preferences['auto_detect_timezone'])" />
        </div>
    </x-settings.card>

    <!-- Workspace & Autosave Card -->
    <x-settings.card title="Workspace & Behavior defaults" description="Configure automatic draft saving and active workspace routing." icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>'>
        <div class="space-y-6">
            <x-settings.select name="preferences[default_workspace]"
                               label="Default Startup Workspace"
                               description="Choose which workspace environment loads first when you open the application."
                               :options="[
                                   'personal' => 'Personal AI Workspace (Default)',
                                   'team' => 'Team Collaboration Hub',
                                   'dev' => 'Developer Sandbox & Terminal Hub',
                                   'research' => 'Deep Research & Document Analyzer'
                               ]"
                               :selected="$settings->preferences['default_workspace'] ?? 'personal'" />

            <div class="pt-3 border-t" style="border-color: var(--clay-card-border);">
                <x-settings.toggle name="preferences[autosave]"
                                   label="Enable Background Autosave"
                                   description="Automatically save unsaved prompt drafts, system instructions, and custom templates every 3 seconds."
                                   :checked="!empty($settings->preferences['autosave']) || !isset($settings->preferences['autosave'])" />
            </div>
        </div>
    </x-settings.card>
</div>
