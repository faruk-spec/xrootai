<!-- Appearance Section -->
<div class="space-y-6 animate-fade-in" x-data="{
        selectedTheme: '{{ $settings->theme ?? 'system' }}',
        accentColor: '{{ $settings->preferences['accent_color'] ?? 'blue' }}',
        bubbleStyle: '{{ $settings->preferences['bubble_style'] ?? 'rounded' }}',
        wallpaper: '{{ $settings->preferences['wallpaper'] ?? 'ambient' }}',
        fontSize: {{ $settings->preferences['font_size'] ?? 15 }},
        borderRadius: {{ $settings->preferences['border_radius'] ?? 24 }},
        blurStrength: {{ $settings->preferences['blur_strength'] ?? 16 }},
        applyTheme(theme) {
            this.selectedTheme = theme;
            document.body.classList.remove('dark-mode', 'oled-mode');
            if (theme === 'dark') {
                document.body.classList.add('dark-mode');
                localStorage.setItem('darkMode', 'true');
            } else if (theme === 'oled') {
                document.body.classList.add('dark-mode', 'oled-mode');
                localStorage.setItem('darkMode', 'oled');
            } else if (theme === 'light') {
                localStorage.setItem('darkMode', 'false');
            } else {
                if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
                    document.body.classList.add('dark-mode');
                }
                localStorage.setItem('darkMode', 'system');
            }
            $dispatch('notify', { message: 'Visual theme applied instantly!', type: 'success' });
        }
    }">

    <!-- Section Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b" style="border-color: var(--clay-card-border);">
        <div>
            <h2 class="text-xl sm:text-2xl font-bold tracking-tight" style="color: var(--text-primary);">Interface Appearance & Glassmorphic Customization</h2>
            <p class="text-sm mt-1" style="color: var(--text-secondary);">Tailor lighting themes, claymorphic blur dynamics, accent palettes, and chat bubble geometry.</p>
        </div>
        <x-settings.badge variant="accent" size="lg" icon='<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>'>
            Clay UI System
        </x-settings.badge>
    </div>

    <!-- Theme Selection Cards -->
    <x-settings.card title="Visual Lighting Mode" description="Choose between crisp daylight, low-light clay dark mode, pitch-black OLED, or system auto-sync." icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>'>
        <input type="hidden" name="theme" :value="selectedTheme">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- System -->
            <div @click="applyTheme('system')"
                 class="p-5 rounded-3xl border cursor-pointer transition-all duration-300 flex flex-col items-center text-center gap-3 select-none group"
                 :style="selectedTheme === 'system' ? 'background: var(--clay-card-bg); border-color: var(--accent); box-shadow: 0 12px 28px rgba(74, 136, 255, 0.25); transform: translateY(-3px);' : 'background: var(--clay-input-bg); border-color: var(--clay-card-border); opacity: 0.85;'">
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-2xl border transition-transform duration-300 group-hover:scale-110"
                     style="background: linear-gradient(135deg, #1e293b 50%, #f8fafc 50%); color: #a855f7; border-color: rgba(255,255,255,0.15);">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                <div>
                    <h6 class="font-bold tracking-tight text-sm sm:text-base" style="color: var(--text-primary);">System Auto</h6>
                    <p class="text-xs mt-0.5 leading-normal" style="color: var(--text-secondary);">Syncs with your operating system preference.</p>
                </div>
            </div>

            <!-- Dark Mode -->
            <div @click="applyTheme('dark')"
                 class="p-5 rounded-3xl border cursor-pointer transition-all duration-300 flex flex-col items-center text-center gap-3 select-none group"
                 :style="selectedTheme === 'dark' ? 'background: var(--clay-card-bg); border-color: var(--accent); box-shadow: 0 12px 28px rgba(74, 136, 255, 0.25); transform: translateY(-3px);' : 'background: var(--clay-input-bg); border-color: var(--clay-card-border); opacity: 0.85;'">
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-2xl border transition-transform duration-300 group-hover:scale-110"
                     style="background: #151928; color: #60a5fa; border-color: rgba(255,255,255,0.15);">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                </div>
                <div>
                    <h6 class="font-bold tracking-tight text-sm sm:text-base" style="color: var(--text-primary);">Dark Clay</h6>
                    <p class="text-xs mt-0.5 leading-normal" style="color: var(--text-secondary);">Sleek low-light theme with soft clay shadows.</p>
                </div>
            </div>

            <!-- Light Mode -->
            <div @click="applyTheme('light')"
                 class="p-5 rounded-3xl border cursor-pointer transition-all duration-300 flex flex-col items-center text-center gap-3 select-none group"
                 :style="selectedTheme === 'light' ? 'background: var(--clay-card-bg); border-color: var(--accent); box-shadow: 0 12px 28px rgba(74, 136, 255, 0.25); transform: translateY(-3px);' : 'background: var(--clay-input-bg); border-color: var(--clay-card-border); opacity: 0.85;'">
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-2xl border transition-transform duration-300 group-hover:scale-110"
                     style="background: #f8fafc; color: #f59e0b; border-color: rgba(0,0,0,0.1);">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
                <div>
                    <h6 class="font-bold tracking-tight text-sm sm:text-base" style="color: var(--text-primary);">Bright Daylight</h6>
                    <p class="text-xs mt-0.5 leading-normal" style="color: var(--text-secondary);">Clean, high-contrast bright interface.</p>
                </div>
            </div>

            <!-- OLED Mode -->
            <div @click="applyTheme('oled')"
                 class="p-5 rounded-3xl border cursor-pointer transition-all duration-300 flex flex-col items-center text-center gap-3 select-none group"
                 :style="selectedTheme === 'oled' ? 'background: var(--clay-card-bg); border-color: var(--accent); box-shadow: 0 12px 28px rgba(74, 136, 255, 0.25); transform: translateY(-3px);' : 'background: var(--clay-input-bg); border-color: var(--clay-card-border); opacity: 0.85;'">
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-2xl border transition-transform duration-300 group-hover:scale-110 shadow-inner"
                     style="background: #000000; color: #38bdf8; border-color: rgba(255,255,255,0.2);">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <div>
                    <div class="flex items-center justify-center gap-1.5">
                        <h6 class="font-bold tracking-tight text-sm sm:text-base" style="color: var(--text-primary);">True OLED</h6>
                        <span class="px-1.5 py-0.5 text-[9px] font-extrabold rounded bg-blue-500/20 text-blue-400">PRO</span>
                    </div>
                    <p class="text-xs mt-0.5 leading-normal" style="color: var(--text-secondary);">Pitch-black #000000 for maximum battery & contrast.</p>
                </div>
            </div>
        </div>
    </x-settings.card>

    <!-- Accent Palette & Live Preview Card -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <x-settings.card title="Accent Color Palette" description="Select the primary brand hue used across buttons, switches, and active indicators." icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>'>
                <input type="hidden" name="preferences[accent_color]" :value="accentColor">
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3.5">
                    @foreach([
                        'blue' => ['label' => 'SaaS Blue', 'hex' => '#4a88ff', 'gradient' => 'linear-gradient(135deg, #7eb6ff 0%, #4a88ff 100%)'],
                        'purple' => ['label' => 'Neon Purple', 'hex' => '#a855f7', 'gradient' => 'linear-gradient(135deg, #c084fc 0%, #9333ea 100%)'],
                        'green' => ['label' => 'Emerald Green', 'hex' => '#10b981', 'gradient' => 'linear-gradient(135deg, #34d399 0%, #059669 100%)'],
                        'orange' => ['label' => 'Sunset Orange', 'hex' => '#f97316', 'gradient' => 'linear-gradient(135deg, #fb923c 0%, #ea580c 100%)'],
                        'pink' => ['label' => 'Cyber Rose', 'hex' => '#ec4899', 'gradient' => 'linear-gradient(135deg, #f472b6 0%, #db2777 100%)'],
                        'gray' => ['label' => 'Slate Gray', 'hex' => '#64748b', 'gradient' => 'linear-gradient(135deg, #94a3b8 0%, #475569 100%)']
                    ] as $key => $color)
                        <div @click="accentColor = '{{ $key }}'"
                             class="p-3.5 rounded-2xl border cursor-pointer flex items-center gap-3 transition-all duration-200 select-none"
                             :style="accentColor === '{{ $key }}' ? 'background: var(--clay-card-bg); border-color: {{ $color['hex'] }}; box-shadow: 0 4px 14px {{ $color['hex'] }}40;' : 'background: var(--clay-input-bg); border-color: var(--clay-card-border); opacity: 0.85;'">
                            <span class="w-7 h-7 rounded-full shrink-0 shadow-md flex items-center justify-center text-white text-xs font-bold"
                                  style="background: {{ $color['gradient'] }};">
                                <template x-if="accentColor === '{{ $key }}'">✓</template>
                            </span>
                            <span class="text-xs sm:text-sm font-semibold tracking-tight" style="color: var(--text-primary);">{{ $color['label'] }}</span>
                        </div>
                    @endforeach
                </div>
            </x-settings.card>

            <!-- Layout Geometry & Typography -->
            <x-settings.card title="Geometry, Typography & Density" description="Fine-tune font scaling, container border curvature, and workspace compactness." icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>'>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-settings.slider name="preferences[font_size]"
                                       label="Base UI Font Size"
                                       description="Scale text across chat responses and settings controls."
                                       :min="12"
                                       :max="20"
                                       :step="1"
                                       model="fontSize"
                                       :value="$settings->preferences['font_size'] ?? 15"
                                       unit="px" />

                    <x-settings.slider name="preferences[border_radius]"
                                       label="Clay Card Border Radius"
                                       description="Adjust softness of container corners."
                                       :min="8"
                                       :max="32"
                                       :step="4"
                                       model="borderRadius"
                                       :value="$settings->preferences['border_radius'] ?? 24"
                                       unit="px" />

                    <x-settings.slider name="preferences[sidebar_width]"
                                       label="Desktop Sidebar Width"
                                       description="Width of the left navigation sidebar."
                                       :min="260"
                                       :max="400"
                                       :step="10"
                                       :value="$settings->preferences['sidebar_width'] ?? 320"
                                       unit="px" />

                    <x-settings.slider name="preferences[blur_strength]"
                                       label="Glassmorphic Backdrop Blur"
                                       description="Intensity of frosted glass transparency."
                                       :min="0"
                                       :max="32"
                                       :step="4"
                                       model="blurStrength"
                                       :value="$settings->preferences['blur_strength'] ?? 16"
                                       unit="px" />
                </div>

                <div class="pt-5 mt-3 border-t grid grid-cols-1 md:grid-cols-2 gap-6" style="border-color: var(--clay-card-border);">
                    <x-settings.segmented-control name="preferences[density]"
                                                  label="Interface Spacing Density"
                                                  :options="[
                                                      'compact' => ['value' => 'compact', 'label' => 'Compact', 'description' => 'Tight vertical padding'],
                                                      'comfortable' => ['value' => 'comfortable', 'label' => 'Comfortable', 'description' => 'Balanced spacing'],
                                                      'spacious' => ['value' => 'spacious', 'label' => 'Spacious', 'description' => 'Airy SaaS feel']
                                                  ]"
                                                  :selected="$settings->preferences['density'] ?? 'comfortable'" />

                    <x-settings.segmented-control name="preferences[bubble_style]"
                                                  label="Chat Bubble Geometry"
                                                  model="bubbleStyle"
                                                  :options="[
                                                      'rounded' => ['value' => 'rounded', 'label' => 'Puffed Rounded', 'description' => 'Classic clay curves'],
                                                      'minimal' => ['value' => 'minimal', 'label' => 'Flat Minimal', 'description' => 'Sharp clean lines'],
                                                      'modern' => ['value' => 'modern', 'label' => 'Modern Floating', 'description' => 'Bordered elevated cards']
                                                  ]"
                                                  :selected="$settings->preferences['bubble_style'] ?? 'rounded'" />
                </div>
            </x-settings.card>
        </div>

        <!-- Live Interactive Chat Preview Box -->
        <div class="lg:col-span-1">
            <div class="sticky top-24 rounded-[28px] p-6 border shadow-2xl space-y-4"
                 :style="`background: var(--clay-card-bg); border-color: var(--clay-card-border); border-radius: ${borderRadius}px; backdrop-filter: blur(${blurStrength}px);`">
                <div class="flex items-center justify-between pb-3 border-b" style="border-color: var(--clay-card-border);">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-red-400"></span>
                        <span class="w-3 h-3 rounded-full bg-amber-400"></span>
                        <span class="w-3 h-3 rounded-full bg-emerald-400"></span>
                        <span class="text-xs font-semibold tracking-tight ml-2" style="color: var(--text-primary);">Live Theme Preview</span>
                    </div>
                    <x-settings.badge variant="accent" size="sm">Real-time</x-settings.badge>
                </div>

                <!-- Simulated Chat Log -->
                <div class="space-y-4 py-2" :style="`font-size: ${fontSize}px;`">
                    <!-- User Message -->
                    <div class="flex justify-end">
                        <div class="max-w-[85%] p-3.5 rounded-2xl shadow-sm transition-all"
                             :style="bubbleStyle === 'minimal'
                                 ? 'background: var(--accent); color: white; border-radius: 8px;'
                                 : (bubbleStyle === 'modern'
                                     ? 'background: var(--accent); color: white; border-radius: 16px; border: 1px solid rgba(255,255,255,0.3);'
                                     : `background: var(--accent); color: white; border-radius: ${borderRadius * 0.8}px ${borderRadius * 0.8}px 4px ${borderRadius * 0.8}px; box-shadow: 0 4px 12px rgba(74, 136, 255, 0.3);`)">
                            <p class="leading-relaxed">Can you optimize our database index structure?</p>
                        </div>
                    </div>

                    <!-- Assistant Response -->
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0 shadow-sm text-white font-bold text-xs"
                             style="background: linear-gradient(135deg, #4a88ff, #56ab2f);">
                            AI
                        </div>
                        <div class="max-w-[88%] p-4 rounded-2xl shadow-sm transition-all border"
                             :style="bubbleStyle === 'minimal'
                                 ? 'background: var(--clay-input-bg); border-color: var(--clay-card-border); color: var(--text-primary); border-radius: 8px;'
                                 : (bubbleStyle === 'modern'
                                     ? 'background: var(--clay-input-bg); border-color: var(--clay-card-border); color: var(--text-primary); border-radius: 16px; box-shadow: 0 8px 24px rgba(0,0,0,0.1);'
                                     : `background: var(--clay-input-bg); border-color: var(--clay-card-border); color: var(--text-primary); border-radius: ${borderRadius * 0.8}px ${borderRadius * 0.8}px ${borderRadius * 0.8}px 4px; box-shadow: var(--clay-input-shadow);`)">
                            <p class="leading-relaxed font-semibold mb-2">Analyzing schema performance...</p>
                            <p class="text-xs sm:text-sm leading-relaxed opacity-90">I recommend adding a composite <code class="px-1.5 py-0.5 rounded font-mono text-xs bg-black/20 dark:bg-white/10">B-Tree</code> index on <span class="font-mono text-blue-400">(user_id, created_at)</span> to eliminate full table scans.</p>
                            
                            <!-- Action buttons -->
                            <div class="flex items-center gap-2 mt-3 pt-3 border-t" style="border-color: var(--clay-card-border);">
                                <span class="text-[11px] opacity-60 flex items-center gap-1"><svg class="w-3 h-3 text-emerald-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg> 18ms latency</span>
                                <span class="ml-auto px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-blue-500/10 text-blue-400 border border-blue-500/20">Gemini 3.1</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Motion & Effects Card -->
    <x-settings.card title="Motion & Glassmorphic Effects" description="Toggle micro-animations and GPU backdrop rendering properties." icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>'>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <x-settings.toggle name="preferences[animations]"
                               label="Enable Micro-Animations"
                               description="Smooth button hover scaling, modal transitions, and accordion slide physics."
                               :checked="!empty($settings->preferences['animations']) || !isset($settings->preferences['animations'])" />

            <x-settings.toggle name="preferences[reduce_motion]"
                               label="Reduce Motion (Accessibility)"
                               description="Disable non-essential slide transitions and particle glows for motion sensitivity."
                               :checked="!empty($settings->preferences['reduce_motion'])" />

            <x-settings.toggle name="preferences[glass_effect]"
                               label="Glassmorphic Backdrop Filter"
                               description="Enable GPU-accelerated frosted glass transparency on cards and sidebars."
                               :checked="!empty($settings->preferences['glass_effect']) || !isset($settings->preferences['glass_effect'])" />
        </div>
    </x-settings.card>

    <!-- Wallpaper & Backgrounds Card -->
    <x-settings.card title="Ambient Wallpaper & Backgrounds" description="Select ambient gradient spheres or upload a custom desktop background image." icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>'>
        <input type="hidden" name="preferences[wallpaper]" :value="wallpaper">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach([
                'ambient' => ['title' => 'Default Ambient Glow', 'desc' => 'Soft cyan & emerald spheres', 'style' => 'background: radial-gradient(circle at top left, rgba(74, 136, 255, 0.3), transparent 70%), radial-gradient(circle at bottom right, rgba(34, 197, 94, 0.25), transparent 70%);'],
                'cosmic' => ['title' => 'Cosmic Glass', 'desc' => 'Deep violet nebula haze', 'style' => 'background: radial-gradient(circle at top right, rgba(168, 85, 247, 0.35), transparent 70%), radial-gradient(circle at bottom left, rgba(59, 130, 246, 0.3), transparent 70%);'],
                'grid' => ['title' => 'Deep Developer Grid', 'desc' => 'Matrix grid lines', 'style' => 'background-image: radial-gradient(var(--clay-card-border) 1px, transparent 1px); background-size: 20px 20px;'],
                'minimal' => ['title' => 'Minimal Solid', 'desc' => 'Clean distraction-free canvas', 'style' => 'background: var(--bg-main);']
            ] as $key => $wp)
                <div @click="wallpaper = '{{ $key }}'"
                     class="p-4 rounded-2xl border cursor-pointer transition-all duration-200 flex flex-col justify-between h-36 select-none relative overflow-hidden"
                     :style="wallpaper === '{{ $key }}' ? 'border-color: var(--accent); box-shadow: 0 8px 24px rgba(74, 136, 255, 0.25); transform: scale(1.02);' : 'border-color: var(--clay-card-border); opacity: 0.85;'">
                    <!-- Preview background box -->
                    <div class="absolute inset-0 z-0 opacity-80 pointer-events-none" style="{{ $wp['style'] }}"></div>
                    <div class="relative z-10 flex justify-between items-start">
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase shadow-sm" style="background: var(--clay-card-bg); color: var(--text-primary);">Preset</span>
                        <template x-if="wallpaper === '{{ $key }}'">
                            <span class="w-5 h-5 rounded-full bg-[var(--accent)] text-white flex items-center justify-center text-xs shadow">✓</span>
                        </template>
                    </div>
                    <div class="relative z-10 pt-4">
                        <h6 class="text-sm font-bold tracking-tight" style="color: var(--text-primary);">{{ $wp['title'] }}</h6>
                        <p class="text-[11px]" style="color: var(--text-secondary);">{{ $wp['desc'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="pt-5 mt-4 border-t flex flex-col sm:flex-row items-center justify-between gap-4" style="border-color: var(--clay-card-border);">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center border" style="background: var(--clay-input-bg); border-color: var(--clay-card-border); color: var(--accent);">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                </div>
                <div>
                    <h6 class="text-sm font-bold tracking-tight" style="color: var(--text-primary);">Upload Custom Wallpaper Image</h6>
                    <p class="text-xs" style="color: var(--text-secondary);">Supports PNG, JPG, or WebP up to 10MB.</p>
                </div>
            </div>
            <label class="px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold cursor-pointer transition-transform hover:scale-105 shadow-sm border"
                   style="background: var(--clay-card-bg); color: var(--accent); border-color: var(--clay-card-border);">
                Browse Image...
                <input type="file" accept="image/*" class="sr-only" @change="$dispatch('notify', { message: 'Custom wallpaper image selected!', type: 'success' })">
            </label>
        </div>
    </x-settings.card>
</div>
