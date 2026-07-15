<!-- Notifications Section -->
<div class="space-y-6 animate-fade-in">
    <!-- Section Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b" style="border-color: var(--clay-card-border);">
        <div>
            <h2 class="text-xl sm:text-2xl font-bold tracking-tight" style="color: var(--text-primary);">Notification Channels & Quiet Hours</h2>
            <p class="text-sm mt-1" style="color: var(--text-secondary);">Manage multi-channel delivery rules, long-running AI completion alerts, and do-not-disturb schedules.</p>
        </div>
        <x-settings.badge variant="accent" size="lg" icon='<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>'>
            Smart Alerting
        </x-settings.badge>
    </div>

    <!-- Core Channels & AI Completion Alerts Card -->
    <x-settings.card title="Delivery Channels & AI Event Alerts" description="Select where and how notifications are delivered across your devices." icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>'>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <x-settings.toggle name="preferences[notif_email]"
                               label="Email Notifications"
                               description="Send critical account updates, weekly summaries, and billing receipts to your inbox."
                               :checked="!empty($settings->preferences['notif_email']) || !isset($settings->preferences['notif_email'])" />

            <x-settings.toggle name="preferences[notif_desktop]"
                               label="Desktop Browser Banners"
                               description="Show native browser toast alerts when working across tabs."
                               :checked="!empty($settings->preferences['notif_desktop']) || !isset($settings->preferences['notif_desktop'])" />

            <x-settings.toggle name="preferences[notif_push]"
                               label="Mobile Push Notifications"
                               description="Push alerts directly to iOS and Android progressive web apps (PWA)."
                               :checked="!empty($settings->preferences['notif_push'])" />

            <x-settings.toggle name="preferences[notif_completion]"
                               label="Long-Running AI Completion Alerts"
                               description="Notify me via push/toast immediately when deep CoT reasoning or complex code generation tasks finish."
                               :checked="!empty($settings->preferences['notif_completion']) || !isset($settings->preferences['notif_completion'])" />
        </div>
    </x-settings.card>

    <!-- Categories Card -->
    <x-settings.card title="Alert Categories & Subscriptions" description="Fine-tune which types of events trigger notification dispatches." icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>'>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <x-settings.toggle name="preferences[notif_security]"
                               label="Urgent Security & Login Alerts"
                               description="Instant notifications whenever a new device signs into your account or changes passwords."
                               :checked="true"
                               disabled />

            <x-settings.toggle name="preferences[notif_updates]"
                               label="Product & Feature Releases"
                               description="Get notified when new AI models, BYOK capabilities, and UI updates roll out."
                               :checked="!empty($settings->preferences['notif_updates']) || !isset($settings->preferences['notif_updates'])" />

            <x-settings.toggle name="preferences[notif_weekly]"
                               label="Weekly Activity Digest"
                               description="Receive an automated Monday summary of your token consumption, saved memories, and active prompts."
                               :checked="!empty($settings->preferences['notif_weekly'])" />

            <x-settings.toggle name="preferences[notif_tips]"
                               label="AI Prompting Tips & Best Practices"
                               description="Periodic guidance on optimizing system instructions and lowering token costs."
                               :checked="!empty($settings->preferences['notif_tips'])" />

            <x-settings.toggle name="preferences[notif_marketing]"
                               label="Marketing & Special Promotions"
                               description="Exclusive SaaS discounts, referral bonuses, and partner integration news."
                               :checked="!empty($settings->preferences['notif_marketing'])" />
        </div>
    </x-settings.card>

    <!-- Schedule & Quiet Hours Card -->
    <x-settings.card title="Delivery Schedule & Quiet Muted Hours" description="Prevent notifications from ringing or vibrating during off-hours or deep sleep." icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>'>
        <div class="space-y-6">
            <div class="max-w-md">
                <x-settings.select name="preferences[notif_schedule]"
                                   label="Batch Delivery Cadence"
                                   description="Choose whether non-urgent notifications arrive instantly or bundled in digests."
                                   :options="[
                                       'immediate' => 'Immediate Real-Time Delivery (Default)',
                                       'hourly' => 'Hourly Batched Digest',
                                       'daily' => 'Daily Evening Summary (5:00 PM)'
                                   ]"
                                   :selected="$settings->preferences['notif_schedule'] ?? 'immediate'" />
            </div>

            <div class="pt-4 border-t space-y-4" style="border-color: var(--clay-card-border);">
                <x-settings.toggle name="preferences[notif_quiet_hours]"
                                   label="Enable Quiet Muted Hours"
                                   description="Temporarily pause push alerts and sound notifications during specified window."
                                   :checked="!empty($settings->preferences['notif_quiet_hours'])" />

                <div class="grid grid-cols-1 sm:grid-cols-2 max-w-md gap-4 pl-0 sm:pl-4">
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold uppercase tracking-wider" style="color: var(--text-secondary);">Quiet Start Time</label>
                        <input type="time"
                               name="preferences[quiet_start]"
                               value="{{ $settings->preferences['quiet_start'] ?? '22:00' }}"
                               class="w-full rounded-2xl py-2.5 px-4 text-sm font-mono font-medium transition-all focus:outline-none focus:ring-2 focus:ring-[var(--accent)]"
                               style="background: var(--clay-input-bg); color: var(--text-primary); border: 1px solid var(--clay-card-border);">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold uppercase tracking-wider" style="color: var(--text-secondary);">Quiet End Time</label>
                        <input type="time"
                               name="preferences[quiet_end]"
                               value="{{ $settings->preferences['quiet_end'] ?? '08:00' }}"
                               class="w-full rounded-2xl py-2.5 px-4 text-sm font-mono font-medium transition-all focus:outline-none focus:ring-2 focus:ring-[var(--accent)]"
                               style="background: var(--clay-input-bg); color: var(--text-primary); border: 1px solid var(--clay-card-border);">
                    </div>
                </div>
            </div>
        </div>
    </x-settings.card>
</div>
