<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email - {{ \App\Models\SystemSetting::get('general_chatbot_name', 'XrootAI') }}</title>
    @if(\App\Models\SystemSetting::get('general_site_icon'))
        <link rel="icon" href="{{ \App\Models\SystemSetting::get('general_site_icon') }}">
    @endif
    <link rel="stylesheet" href="{{ asset('css/claymorphism.css') }}">
    <script src="{{ asset('js/alpine.min.js') }}" defer></script>
    <style>
        .ambient-bubble { position: absolute; border-radius: 50%; filter: blur(80px); z-index: -1; opacity: 0.5; }
        .bubble-1 { top: 15%; left: 15%; width: 260px; height: 260px; background: rgba(126, 182, 255, 0.4); }
        .bubble-2 { bottom: 15%; right: 15%; width: 300px; height: 300px; background: rgba(168, 224, 99, 0.3); }
        .otp-input { text-align: center; font-size: 1.8rem; letter-spacing: 12px; font-weight: 700; font-family: monospace; }
    </style>
</head>
<body x-data="{ darkMode: localStorage.getItem('darkMode') === 'true', showChangeEmail: false }" :class="{ 'dark-mode': darkMode }">
    <div class="ambient-bubble bubble-1"></div>
    <div class="ambient-bubble bubble-2"></div>

    <div class="auth-container">
        <div class="clay-card auth-card" style="max-width: 520px; padding: 36px;">
            <!-- Logout / Home navigation -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                <a href="{{ route('chat') }}" class="clay-btn clay-btn-secondary" style="padding: 6px 14px; font-size: 0.82rem; text-decoration: none;">← Dashboard</a>
                
                <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" class="clay-btn clay-btn-secondary" style="padding: 6px 14px; font-size: 0.82rem; color: #ef4444;">Sign Out</button>
                </form>
            </div>

            <!-- Header -->
            <div style="text-align: center; margin-bottom: 20px;">
                <div style="width: 60px; height: 60px; border-radius: 50%; background: rgba(59, 130, 246, 0.15); color: #3b82f6; display: flex; align-items: center; justify-content: center; margin: 0 auto 14px; font-size: 1.8rem;">
                    ✉️
                </div>
                <h1 class="auth-title" style="font-size: 1.6rem; margin-bottom: 6px;">Verify Your Email</h1>
                <p class="auth-subtitle" style="font-size: 0.9rem; line-height: 1.5;">
                    We have sent a verification code and secure link to:<br>
                    <strong style="color: #3b82f6; font-size: 1.05rem;">{{ $targetEmail }}</strong>
                </p>
            </div>

            @if(session('error'))
                <div style="background: #fee2e2; color: #b91c1c; border: 1px solid #f87171; padding: 12px 14px; border-radius: 12px; margin-bottom: 18px; font-size: 0.88rem; text-align: left;">
                    ⚠️ {{ session('error') }}
                </div>
            @endif
            @if(session('status'))
                <div style="background: #dcfce7; color: #15803d; border: 1px solid #86efac; padding: 12px 14px; border-radius: 12px; margin-bottom: 18px; font-size: 0.88rem; text-align: left;">
                    ✅ {{ session('status') }}
                </div>
            @endif

            <!-- OTP Submission Form -->
            <form method="POST" action="{{ route('verification.verify-otp') }}" style="margin-bottom: 24px;">
                @csrf
                <div class="clay-input-group">
                    <label for="otp" class="clay-input-label" style="text-align: center; width: 100%; font-size: 0.85rem;">ENTER ONE-TIME PASSWORD (OTP)</label>
                    <input id="otp" type="text" name="otp" class="clay-inset clay-input otp-input" required autofocus placeholder="••••••" maxlength="32">
                    @error('otp')
                        <div class="text-danger mt-1" style="font-size: 0.82rem;">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="clay-btn clay-btn-primary" style="width: 100%; padding: 14px; font-size: 1rem; margin-top: 10px;">
                    Verify Account Now
                </button>
            </form>

            <!-- Actions row: Resend & Change Email -->
            <div style="border-top: 1px solid rgba(0,0,0,0.1); padding-top: 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                <form method="POST" action="{{ route('verification.resend') }}" style="margin: 0;">
                    @csrf
                    <button type="submit" style="background: none; border: none; color: #3b82f6; font-size: 0.86rem; font-weight: 600; cursor: pointer; text-decoration: underline;">
                        🔄 Resend Code & Link
                    </button>
                </form>

                <button type="button" @click="showChangeEmail = !showChangeEmail" style="background: none; border: none; color: var(--text-muted); font-size: 0.86rem; cursor: pointer; text-decoration: underline;">
                    ✏️ Change Email Address
                </button>
            </div>

            <!-- Change Email Section (Collapsible) -->
            <div x-show="showChangeEmail" x-transition style="margin-top: 20px; padding: 18px; background: rgba(0,0,0,0.04); border-radius: 14px; border: 1px solid rgba(0,0,0,0.1);">
                <form method="POST" action="{{ route('verification.change-email') }}" style="margin: 0;">
                    @csrf
                    <div class="clay-input-group" style="margin-bottom: 12px;">
                        <label for="new_email" class="clay-input-label" style="font-size: 0.82rem;">NEW EMAIL ADDRESS</label>
                        <input id="new_email" type="email" name="new_email" class="clay-inset clay-input" required placeholder="new-email@domain.com" style="padding: 10px;">
                    </div>
                    <div style="display: flex; justify-content: flex-end; gap: 8px;">
                        <button type="button" @click="showChangeEmail = false" class="clay-btn clay-btn-secondary" style="padding: 8px 14px; font-size: 0.8rem;">Cancel</button>
                        <button type="submit" class="clay-btn clay-btn-primary" style="padding: 8px 16px; font-size: 0.8rem;">Update & Resend Code</button>
                    </div>
                </form>
            </div>

            <!-- Floating Theme Toggle -->
            <div style="position: absolute; top: 15px; right: 15px;">
                <button @click="darkMode = !darkMode; localStorage.setItem('darkMode', darkMode)" class="clay-btn clay-btn-secondary" style="border-radius: 50%; width: 38px; height: 38px; padding: 0; display: flex; align-items: center; justify-content: center;">
                    <span x-show="!darkMode">🌙</span>
                    <span x-show="darkMode">☀️</span>
                </button>
            </div>
        </div>
    </div>
</body>
</html>
