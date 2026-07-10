<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Settings - {{ \App\Models\SystemSetting::get('general_chatbot_name', 'XrootAI') }}</title>
    @if(\App\Models\SystemSetting::get('general_site_icon'))
        <link rel="icon" href="{{ \App\Models\SystemSetting::get('general_site_icon') }}">
    @endif
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/claymorphism.css') }}">
    <script src="{{ asset('js/alpine.min.js') }}" defer></script>
    <style>
        body { background-color: #0f172a; font-family: 'Segoe UI', system-ui, -apple-system, sans-serif; color: #f8fafc; min-height: 100vh; overflow-x: hidden; }
        .ambient-bubble { position: absolute; border-radius: 50%; filter: blur(100px); z-index: -1; opacity: 0.35; pointer-events: none; }
        .bubble-1 { top: 5%; left: 10%; width: 400px; height: 400px; background: rgba(74, 136, 255, 0.35); }
        .bubble-2 { bottom: 10%; right: 10%; width: 450px; height: 450px; background: rgba(34, 197, 94, 0.28); }
        
        /* Layout Grid */
        .settings-wrapper { display: grid; grid-template-columns: 280px 1fr; gap: 28px; min-height: calc(100vh - 140px); }
        @media (max-width: 991px) {
            .settings-wrapper { grid-template-columns: 1fr; }
        }

        /* Clay Card Customizations for Settings */
        .settings-sidebar { background: rgba(30, 41, 59, 0.7); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.12); border-radius: 24px; padding: 24px; box-shadow: 0 16px 40px rgba(0,0,0,0.25); height: fit-content; }
        .settings-content { background: rgba(30, 41, 59, 0.7); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.12); border-radius: 24px; padding: 36px; box-shadow: 0 16px 40px rgba(0,0,0,0.25); }
        @media (max-width: 768px) {
            .settings-content { padding: 24px; }
        }

        /* Nav Tab Item */
        .nav-tab-btn { display: flex; align-items: center; gap: 14px; width: 100%; padding: 14px 18px; border-radius: 16px; font-weight: 600; font-size: 0.95rem; color: #94a3b8; background: transparent; border: none; transition: all 0.22s cubic-bezier(0.2,0,0,1); text-align: left; cursor: pointer; margin-bottom: 6px; text-decoration: none; }
        .nav-tab-btn:hover { color: #f8fafc; background: rgba(255, 255, 255, 0.06); }
        .nav-tab-btn.active { color: #ffffff; background: linear-gradient(135deg, #4a88ff 0%, #3b5bdb 100%); box-shadow: 0 6px 18px rgba(74, 136, 255, 0.35); }
        .nav-tab-btn i { font-size: 1.2rem; }

        /* Inputs & Controls inside dark glass */
        .form-label { font-weight: 600; color: #e2e8f0; margin-bottom: 8px; font-size: 0.92rem; }
        .form-control, .form-select { background-color: #0f172a !important; border: 1px solid rgba(255, 255, 255, 0.15) !important; color: #f8fafc !important; border-radius: 14px; padding: 12px 16px; font-size: 0.95rem; transition: border-color 0.2s ease, box-shadow 0.2s ease; }
        .form-control:focus, .form-select:focus { border-color: #4a88ff !important; box-shadow: 0 0 0 4px rgba(74, 136, 255, 0.2) !important; }
        .form-text { color: #94a3b8 !important; font-size: 0.82rem; margin-top: 6px; }

        /* Security Cards */
        .recovery-box { font-family: monospace; font-size: 1.15rem; letter-spacing: 3px; font-weight: 700; background: #0f172a; border: 2px dashed rgba(255, 255, 255, 0.2); border-radius: 16px; padding: 22px; display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 14px; text-align: center; color: #38bdf8; }
        .qr-container { background: #ffffff; border: 3px solid rgba(255, 255, 255, 0.2); padding: 20px; border-radius: 20px; display: inline-block; margin: 15px 0; box-shadow: 0 8px 24px rgba(0,0,0,0.15); }
        .status-badge { display: inline-flex; align-items: center; gap: 8px; padding: 6px 14px; border-radius: 50px; font-weight: 600; font-size: 0.85rem; }
        .status-badge.active { background: rgba(34, 197, 94, 0.2); color: #4ade80; border: 1px solid rgba(34, 197, 94, 0.35); }
        .status-badge.inactive { background: rgba(245, 158, 11, 0.2); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.35); }

        /* Theme Selection Cards */
        .theme-option-card { border: 2px solid rgba(255, 255, 255, 0.12); border-radius: 18px; padding: 20px; cursor: pointer; transition: all 0.2s ease; background: rgba(15, 23, 42, 0.6); display: flex; flex-direction: column; align-items: center; gap: 12px; text-align: center; }
        .theme-option-card:hover { border-color: rgba(74, 136, 255, 0.5); transform: translateY(-3px); }
        .theme-option-card.selected { border-color: #4a88ff; background: rgba(74, 136, 255, 0.12); box-shadow: 0 8px 24px rgba(74, 136, 255, 0.25); }
    </style>
</head>
<body x-data="{ activeTab: '{{ $activeTab }}' }">
    <div class="ambient-bubble bubble-1"></div>
    <div class="ambient-bubble bubble-2"></div>

    <div class="container py-4 py-md-5" style="max-width: 1240px;">
        <!-- Top Navigation Header -->
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4 pb-3 border-bottom border-secondary border-opacity-25">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-4 d-flex align-items-center justify-content-center shadow-sm" style="width: 52px; height: 52px; background: linear-gradient(135deg, #4a88ff, #56ab2f); color: white; font-weight: 800; font-size: 1.4rem;">
                    {{ substr(\App\Models\SystemSetting::get('general_chatbot_name', 'XrootAI'), 0, 1) }}
                </div>
                <div>
                    <h3 class="fw-bold mb-0 text-white">Account & System Settings</h3>
                    <p class="text-secondary small mb-0">Manage your profile instructions, interface appearance, API keys, and two-factor security without page refresh.</p>
                </div>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('chat') }}" class="clay-btn clay-btn-secondary px-4 py-2 text-decoration-none d-flex align-items-center gap-2">
                    <i class="bi bi-arrow-left"></i> Back to Chat
                </a>
            </div>
        </div>

        <!-- Global Alerts -->
        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm rounded-4 p-3 d-flex align-items-center gap-3 mb-4" role="alert" style="background-color: rgba(34, 197, 94, 0.18); color: #4ade80; border-left: 5px solid #22c55e !important;">
                <i class="bi bi-check-circle-fill fs-4 text-success"></i>
                <div>{{ session('success') }}</div>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger border-0 shadow-sm rounded-4 p-3 d-flex align-items-center gap-3 mb-4" role="alert" style="background-color: rgba(239, 68, 68, 0.18); color: #f87171; border-left: 5px solid #ef4444 !important;">
                <i class="bi bi-exclamation-triangle-fill fs-4 text-danger"></i>
                <div>{{ session('error') }}</div>
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger border-0 shadow-sm rounded-4 p-3 d-flex align-items-center gap-3 mb-4" role="alert" style="background-color: rgba(239, 68, 68, 0.18); color: #f87171; border-left: 5px solid #ef4444 !important;">
                <i class="bi bi-exclamation-triangle-fill fs-4 text-danger"></i>
                <div>
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <div class="settings-wrapper">
            <!-- Left Sidebar Navigation Tabs -->
            <aside class="settings-sidebar">
                <!-- User Identity Card -->
                <div class="d-flex align-items-center gap-3 p-3 mb-4 rounded-4" style="background: rgba(15, 23, 42, 0.6); border: 1px solid rgba(255, 255, 255, 0.08);">
                    <div style="width: 44px; height: 44px; border-radius: 50%; background: #4a88ff; color: white; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 1.2rem; flex-shrink: 0;">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                    <div style="overflow: hidden;">
                        <div class="fw-bold text-truncate text-white" style="font-size: 0.95rem;">{{ Auth::user()->name }}</div>
                        <div class="text-secondary small text-truncate">{{ Auth::user()->email }}</div>
                    </div>
                </div>

                <div class="text-uppercase text-secondary fw-bold mb-2 px-2" style="font-size: 0.72rem; letter-spacing: 0.06em;">Navigation</div>

                <button type="button" class="nav-tab-btn" :class="{ 'active': activeTab === 'general' }" @click="activeTab = 'general'">
                    <i class="bi bi-sliders"></i>
                    <span>General & AI</span>
                </button>

                <button type="button" class="nav-tab-btn" :class="{ 'active': activeTab === 'theme' }" @click="activeTab = 'theme'">
                    <i class="bi bi-palette"></i>
                    <span>Appearance & Theme</span>
                </button>

                <button type="button" class="nav-tab-btn" :class="{ 'active': activeTab === 'keys' }" @click="activeTab = 'keys'">
                    <i class="bi bi-key"></i>
                    <span>API Keys (BYOK)</span>
                </button>

                <button type="button" class="nav-tab-btn" :class="{ 'active': activeTab === 'security' }" @click="activeTab = 'security'">
                    <i class="bi bi-shield-lock"></i>
                    <span>Security & 2FA</span>
                </button>

                <hr class="border-secondary border-opacity-25 my-3">

                <!-- Sign Out -->
                <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="nav-tab-btn text-danger">
                        <i class="bi bi-box-arrow-right"></i>
                        <span>Sign Out</span>
                    </button>
                </form>
            </aside>

            <!-- Main Content Area -->
            <main class="settings-content">
                <!-- TAB 1: GENERAL & AI -->
                <div x-show="activeTab === 'general'" x-transition.opacity>
                    <div class="pb-3 mb-4 border-bottom border-secondary border-opacity-25 d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="fw-bold mb-1 text-white"><i class="bi bi-sliders me-2 text-primary"></i>General & AI Preferences</h4>
                            <p class="text-secondary small mb-0">Configure default assistant behaviors, models, and personal instructions.</p>
                        </div>
                    </div>

                    <form action="{{ route('settings.update') }}" method="POST">
                        @csrf
                        <input type="hidden" name="theme" value="{{ $settings->theme ?? 'system' }}">

                        <div class="mb-4">
                            <label class="form-label">Default AI Model Selection</label>
                            <select name="default_model" class="form-select">
                                @if(!empty($models))
                                    @foreach($models as $model)
                                        <option value="{{ $model['id'] }}" {{ ($settings->default_model ?? '') === $model['id'] ? 'selected' : '' }}>
                                            {{ $model['name'] }}
                                        </option>
                                    @endforeach
                                @else
                                    <option value="mock">Default Model</option>
                                @endif
                            </select>
                            <div class="form-text">This model will be automatically selected when initiating new conversations.</div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Custom System Instructions (System Prompt)</label>
                            <textarea name="system_prompt" class="form-control font-monospace" rows="6" placeholder="Example: Always answer concisely, format code blocks cleanly, and prefer Python for examples...">{{ $settings->system_prompt }}</textarea>
                            <div class="form-text">These instructions guide the AI's tone, formatting style, and approach for every chat response.</div>
                        </div>

                        <div class="d-flex justify-content-end pt-3 border-top border-secondary border-opacity-25">
                            <button type="submit" class="clay-btn clay-btn-primary px-4 py-2 d-flex align-items-center gap-2">
                                <i class="bi bi-save-fill"></i> Save Preferences
                            </button>
                        </div>
                    </form>
                </div>

                <!-- TAB 2: APPEARANCE & THEME -->
                <div x-show="activeTab === 'theme'" x-transition.opacity style="display: none;">
                    <div class="pb-3 mb-4 border-bottom border-secondary border-opacity-25 d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="fw-bold mb-1 text-white"><i class="bi bi-palette me-2 text-info"></i>Interface Appearance</h4>
                            <p class="text-secondary small mb-0">Select your preferred lighting theme across the chat workspace.</p>
                        </div>
                    </div>

                    <form action="{{ route('settings.update') }}" method="POST" x-data="{ selectedTheme: '{{ $settings->theme ?? 'system' }}' }">
                        @csrf
                        <input type="hidden" name="default_model" value="{{ $settings->default_model ?? 'mock' }}">
                        <input type="hidden" name="system_prompt" value="{{ $settings->system_prompt ?? '' }}">
                        <input type="hidden" name="theme" :value="selectedTheme">

                        <div class="row g-4 mb-5">
                            <div class="col-md-4">
                                <div class="theme-option-card" :class="{ 'selected': selectedTheme === 'dark' }" @click="selectedTheme = 'dark'">
                                    <div style="width: 60px; height: 60px; border-radius: 16px; background: #1e293b; color: #60a5fa; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; border: 1px solid rgba(255,255,255,0.1);">
                                        <i class="bi bi-moon-stars-fill"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-1 text-white">Dark Mode</h6>
                                        <p class="text-secondary small mb-0">Sleek dark interface tailored for low-light environments and coding.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="theme-option-card" :class="{ 'selected': selectedTheme === 'light' }" @click="selectedTheme = 'light'">
                                    <div style="width: 60px; height: 60px; border-radius: 16px; background: #f8fafc; color: #f59e0b; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; border: 1px solid rgba(0,0,0,0.1);">
                                        <i class="bi bi-sun-fill"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-1 text-white">Light Mode</h6>
                                        <p class="text-secondary small mb-0">Clean, crisp bright interface with high readability.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="theme-option-card" :class="{ 'selected': selectedTheme === 'system' }" @click="selectedTheme = 'system'">
                                    <div style="width: 60px; height: 60px; border-radius: 16px; background: linear-gradient(135deg, #1e293b 50%, #f8fafc 50%); color: #a855f7; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; border: 1px solid rgba(255,255,255,0.1);">
                                        <i class="bi bi-laptop"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-1 text-white">System Default</h6>
                                        <p class="text-secondary small mb-0">Automatically matches your operating system preference.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end pt-3 border-top border-secondary border-opacity-25">
                            <button type="submit" class="clay-btn clay-btn-primary px-4 py-2 d-flex align-items-center gap-2">
                                <i class="bi bi-check-lg"></i> Apply Theme Preference
                            </button>
                        </div>
                    </form>
                </div>

                <!-- TAB 3: API KEYS (BYOK) -->
                <div x-show="activeTab === 'keys'" x-transition.opacity style="display: none;">
                    <div class="pb-3 mb-4 border-bottom border-secondary border-opacity-25 d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="fw-bold mb-1 text-white"><i class="bi bi-key me-2 text-warning"></i>API Keys (Bring Your Own Key)</h4>
                            <p class="text-secondary small mb-0">Enter your personal API keys to bypass rate limits or access restricted models directly.</p>
                        </div>
                    </div>

                    <form action="{{ route('settings.keys') }}" method="POST">
                        @csrf
                        @php
                            $providers = ['openai' => 'OpenAI API Key', 'anthropic' => 'Anthropic / Claude API Key', 'gemini' => 'Google Gemini API Key', 'deepseek' => 'DeepSeek API Key'];
                        @endphp
                        <div class="row g-4 mb-4">
                            @foreach($providers as $providerSlug => $providerLabel)
                                <div class="col-md-6">
                                    <label class="form-label">{{ $providerLabel }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-dark text-secondary border-secondary border-opacity-25"><i class="bi bi-key-fill"></i></span>
                                        <input type="password" name="keys[{{ $providerSlug }}]" class="form-control" placeholder="sk-..." autocomplete="off">
                                    </div>
                                    <div class="form-text">Leave blank to retain any previously saved encrypted key.</div>
                                </div>
                            @endforeach
                        </div>

                        <div class="d-flex justify-content-end pt-3 border-top border-secondary border-opacity-25">
                            <button type="submit" class="clay-btn clay-btn-primary px-4 py-2 d-flex align-items-center gap-2">
                                <i class="bi bi-shield-check"></i> Save Encrypted API Keys
                            </button>
                        </div>
                    </form>
                </div>

                <!-- TAB 4: SECURITY & 2FA -->
                <div x-show="activeTab === 'security'" x-transition.opacity style="display: none;">
                    <div class="pb-3 mb-4 border-bottom border-secondary border-opacity-25 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <h4 class="fw-bold mb-1 text-white"><i class="bi bi-shield-lock me-2 text-success"></i>Security & Two-Factor Authentication</h4>
                            <p class="text-secondary small mb-0">Protect your account with authenticator apps (TOTP), email OTP, and emergency backup codes.</p>
                        </div>
                    </div>

                    <!-- Status Summary Card -->
                    <div class="p-4 rounded-4 mb-4" style="background: rgba(15, 23, 42, 0.6); border: 1px solid rgba(255, 255, 255, 0.08);">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-4">
                            <div class="d-flex align-items-center gap-4">
                                <div style="width: 60px; height: 60px;" class="rounded-4 d-flex align-items-center justify-content-center shadow-sm {{ $user->hasTwoFactorEnabled() ? 'bg-success bg-opacity-20 text-success' : 'bg-warning bg-opacity-20 text-warning' }}">
                                    <i class="bi {{ $user->hasTwoFactorEnabled() ? 'bi-shield-check' : 'bi-shield-exclamation' }} fs-1"></i>
                                </div>
                                <div>
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <h5 class="fw-bold mb-0 text-white">Two-Factor Authentication Status</h5>
                                        @if($user->hasTwoFactorEnabled())
                                            <span class="status-badge active"><i class="bi bi-check-circle-fill"></i> Active</span>
                                        @else
                                            <span class="status-badge inactive"><i class="bi bi-exclamation-circle-fill"></i> Disabled</span>
                                        @endif
                                    </div>
                                    <p class="text-secondary mb-0 small">
                                        @if($user->hasTwoFactorEnabled())
                                            Protected via <strong class="text-white">{{ $user->two_factor_type === 'totp' ? 'Authenticator App (TOTP)' : 'Email OTP Verification' }}</strong> since {{ $user->two_factor_confirmed_at?->format('M d, Y') ?? 'recently' }}.
                                        @else
                                            Add an extra layer of security to your account. You will need a 6-digit code upon every sign in.
                                        @endif
                                    </p>
                                </div>
                            </div>

                            <div>
                                @if($user->hasTwoFactorEnabled())
                                    <button type="button" data-bs-toggle="modal" data-bs-target="#disable2FAModal" class="btn btn-outline-danger px-4 py-2 rounded-3 fw-semibold d-flex align-items-center gap-2">
                                        <i class="bi bi-shield-slash-fill"></i> Disable 2FA
                                    </button>
                                @else
                                    <div class="d-flex flex-wrap gap-3">
                                        <form method="POST" action="{{ route('profile.security.update') }}" class="m-0">
                                            @csrf
                                            <input type="hidden" name="action" value="enable_email">
                                            <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 fw-bold d-flex align-items-center gap-2">
                                                <i class="bi bi-envelope-check-fill"></i> Enable Email OTP
                                            </button>
                                        </form>
                                        <a href="{{ route('user.settings', ['tab' => 'security', 'setup' => 'totp']) }}" class="btn btn-success px-4 py-2 rounded-3 fw-bold d-flex align-items-center gap-2 text-decoration-none">
                                            <i class="bi bi-qr-code-scan"></i> Setup Authenticator App
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- TOTP SETUP PANEL (WHEN SETUP = TOTP) -->
                    @if($qrCodeUrl)
                        <div class="p-4 rounded-4 mb-4 border border-success border-opacity-50" style="background: rgba(16, 185, 129, 0.08);">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                                <span class="badge bg-success px-3 py-2 rounded-pill fs-6"><i class="bi bi-qr-code me-1"></i> Step-by-Step Authenticator Setup</span>
                                <a href="{{ route('user.settings', ['tab' => 'security']) }}" class="text-secondary small text-decoration-none"><i class="bi bi-x-lg me-1"></i>Close Setup</a>
                            </div>
                            <h5 class="fw-bold mb-2 text-white">Scan QR Code with your Authenticator App</h5>
                            <p class="text-secondary small">Open your 2FA app (Google Authenticator, Authy, 1Password, or Microsoft Authenticator) and scan the QR code below. Or copy the manual secret key if your camera is unavailable.</p>

                            <div class="text-center my-4">
                                <div class="qr-container">
                                    <img src="{{ $qrCodeUrl }}" alt="2FA QR Code" width="220" height="220">
                                </div>
                                <div class="mt-3">
                                    <small class="text-secondary d-block fw-semibold mb-1">Manual Secret Key:</small>
                                    <code class="fs-5 fw-bold text-info px-4 py-2 bg-dark border border-secondary border-opacity-50 rounded-4 d-inline-block shadow-sm">{{ implode(' ', str_split($secretKey, 4)) }}</code>
                                </div>
                            </div>

                            <hr class="border-secondary border-opacity-25 my-4">

                            <form method="POST" action="{{ route('profile.security.totp-confirm') }}" class="mt-3">
                                @csrf
                                <label class="form-label fw-bold text-white mb-3">Enter the 6-digit verification code from your app to confirm activation:</label>
                                <div class="d-flex gap-3 align-items-center flex-wrap">
                                    <input type="text" name="totp_code" class="form-control text-center fw-bold fs-4 shadow-sm rounded-3" style="max-width: 200px; letter-spacing: 8px;" placeholder="123456" maxlength="6" required autofocus autocomplete="one-time-code">
                                    <button type="submit" class="btn btn-success px-4 py-2 fw-bold rounded-3 d-flex align-items-center gap-2">
                                        <i class="bi bi-check-circle-fill"></i> Confirm & Activate TOTP
                                    </button>
                                    <a href="{{ route('user.settings', ['tab' => 'security']) }}" class="btn btn-outline-secondary px-4 py-2 rounded-3 text-decoration-none">Cancel</a>
                                </div>
                            </form>
                        </div>
                    @endif

                    <!-- EMERGENCY RECOVERY CODES -->
                    @if($user->hasTwoFactorEnabled())
                        <div class="p-4 rounded-4 mb-4" style="background: rgba(15, 23, 42, 0.6); border: 1px solid rgba(255, 255, 255, 0.08);">
                            <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
                                <div>
                                    <h5 class="fw-bold mb-1 text-white"><i class="bi bi-key-fill text-warning me-2"></i>Emergency Recovery Codes</h5>
                                    <p class="text-secondary small mb-0">Store these backup codes in a safe place (like a password manager). Each code can only be used once to log in if you lose phone/email access.</p>
                                </div>
                                <form method="POST" action="{{ route('profile.security.update') }}" onsubmit="return confirm('Regenerating will invalidate all existing recovery codes immediately. Continue?');" class="m-0">
                                    @csrf
                                    <input type="hidden" name="action" value="regenerate_codes">
                                    <button type="submit" class="btn btn-outline-secondary px-3 py-2 small rounded-3 d-flex align-items-center gap-2">
                                        <i class="bi bi-arrow-clockwise"></i> Regenerate Codes
                                    </button>
                                </form>
                            </div>

                            @if(!empty($recoveryCodes))
                                <div class="recovery-box my-3">
                                    @foreach($recoveryCodes as $code)
                                        <div class="p-2 bg-dark rounded-3 border border-secondary border-opacity-25 shadow-sm"><code>{{ $code }}</code></div>
                                    @endforeach
                                </div>
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-3">
                                    <small class="text-secondary"><i class="bi bi-shield-check text-success me-1"></i> Showing {{ count($recoveryCodes) }} emergency codes available.</small>
                                </div>
                            @else
                                <div class="alert alert-warning rounded-4 p-3 d-flex align-items-center gap-3 mb-0 mt-3 border-0 shadow-sm" style="background-color: rgba(245, 158, 11, 0.15); color: #fbbf24; border-left: 5px solid #f59e0b !important;">
                                    <i class="bi bi-exclamation-circle-fill fs-4 text-warning"></i>
                                    <div>You currently have no emergency recovery codes generated. Click "Regenerate Codes" above to create a fresh set immediately.</div>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </main>
        </div>

        <!-- DISABLE 2FA CONFIRMATION MODAL -->
        <div class="modal fade" id="disable2FAModal" tabindex="-1" aria-labelledby="disable2FAModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content bg-dark text-white border border-secondary border-opacity-25 shadow-lg rounded-4 overflow-hidden">
                    <div class="modal-header bg-danger bg-opacity-20 px-4 py-3 border-bottom border-secondary border-opacity-25">
                        <h5 class="modal-title fw-bold text-danger d-flex align-items-center gap-2" id="disable2FAModalLabel">
                            <i class="bi bi-exclamation-triangle-fill fs-4"></i> Disable Two-Factor Authentication?
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST" action="{{ route('profile.security.update') }}">
                        @csrf
                        <input type="hidden" name="action" value="disable_2fa">
                        <div class="modal-body p-4">
                            <p class="text-secondary">Turning off Two-Factor Authentication will remove extra login protection from your account and invalidate all of your current emergency recovery codes.</p>
                            @if($user->password)
                                <div class="mt-4">
                                    <label class="form-label fw-bold text-white small">Current Password</label>
                                    <input type="password" name="current_password" class="form-control form-control-lg rounded-3" placeholder="Enter password to confirm">
                                </div>
                            @endif
                        </div>
                        <div class="modal-footer bg-dark px-4 py-3 border-top border-secondary border-opacity-25 d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-outline-secondary rounded-3 px-4 py-2 fw-semibold" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger rounded-3 px-4 py-2 fw-bold shadow-sm">Turn Off 2FA</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
