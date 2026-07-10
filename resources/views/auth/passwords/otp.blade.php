<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password via OTP - {{ \App\Models\SystemSetting::get('general_chatbot_name', 'XrootAI') }}</title>
    @if(\App\Models\SystemSetting::get('general_site_icon'))
        <link rel="icon" href="{{ \App\Models\SystemSetting::get('general_site_icon') }}">
    @endif
    <link rel="stylesheet" href="{{ asset('css/claymorphism.css') }}">
    <script src="{{ asset('js/alpine.min.js') }}" defer></script>
    <style>
        .ambient-bubble { position: absolute; border-radius: 50%; filter: blur(80px); z-index: -1; opacity: 0.5; }
        .bubble-1 { top: 12%; left: 12%; width: 260px; height: 260px; background: rgba(245, 158, 11, 0.35); }
        .bubble-2 { bottom: 12%; right: 12%; width: 300px; height: 300px; background: rgba(59, 130, 246, 0.35); }
        .otp-input { text-align: center; font-size: 1.6rem; letter-spacing: 10px; font-weight: 700; font-family: monospace; }
        .password-rule { font-size: 0.78rem; display: flex; align-items: center; gap: 6px; margin-top: 4px; }
    </style>
</head>
<body x-data="{ darkMode: localStorage.getItem('darkMode') === 'true', password: '' }" :class="{ 'dark-mode': darkMode }">
    <div class="ambient-bubble bubble-1"></div>
    <div class="ambient-bubble bubble-2"></div>

    <div class="auth-container">
        <div class="clay-card auth-card" style="max-width: 480px;">
            <!-- Header Icon -->
            <div style="text-align: center; margin-bottom: 20px;">
                <div style="width: 64px; height: 64px; border-radius: 50%; background: rgba(245, 158, 11, 0.15); color: #f59e0b; display: flex; align-items: center; justify-content: center; margin: 0 auto 14px; font-size: 1.9rem;">
                    🔢
                </div>
                <h1 class="auth-title">Verify Code & Reset</h1>
                <p class="auth-subtitle">Enter the verification code sent to your email along with your new password</p>
            </div>

            @if(session('error'))
                <div style="background: #fee2e2; color: #b91c1c; border: 1px solid #f87171; padding: 12px 14px; border-radius: 12px; margin-bottom: 16px; font-size: 0.88rem; text-align: left;">
                    ⚠️ {{ session('error') }}
                </div>
            @endif
            @if(session('status'))
                <div style="background: #dcfce7; color: #15803d; border: 1px solid #86efac; padding: 12px 14px; border-radius: 12px; margin-bottom: 16px; font-size: 0.88rem; text-align: left;">
                    ✅ {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}">
                @csrf

                <!-- Email Address -->
                <div class="clay-input-group">
                    <label for="email" class="clay-input-label">Email Address</label>
                    <input id="email" class="clay-inset clay-input" type="email" name="email" value="{{ $email ?? old('email') }}" required autofocus placeholder="name@example.com">
                    @error('email')
                        <div class="text-danger mt-1" style="font-size: 0.85rem;">{{ $message }}</div>
                    @enderror
                </div>

                <!-- OTP Code -->
                <div class="clay-input-group">
                    <label for="token" class="clay-input-label" style="text-align: center; width: 100%;">VERIFICATION CODE (OTP)</label>
                    <input id="token" type="text" name="token" class="clay-inset clay-input otp-input" required placeholder="••••••" maxlength="32">
                    @error('token')
                        <div class="text-danger mt-1" style="font-size: 0.85rem;">{{ $message }}</div>
                    @enderror
                </div>

                <!-- New Password -->
                <div class="clay-input-group">
                    <label for="password" class="clay-input-label">New Password</label>
                    <input id="password" x-model="password" class="clay-inset clay-input" type="password" name="password" required placeholder="Min. {{ \App\Models\SystemSetting::get('auth_password_reset_min_length', 8) }} characters">
                    
                    <div style="margin-top: 8px; padding: 10px 12px; background: rgba(0,0,0,0.04); border-radius: 10px;">
                        <div class="password-rule" :style="password.length >= {{ \App\Models\SystemSetting::get('auth_password_reset_min_length', 8) }} ? 'color: #10b981;' : 'color: var(--text-muted);'">
                            <span x-text="password.length >= {{ \App\Models\SystemSetting::get('auth_password_reset_min_length', 8) }} ? '✓' : '•'"></span>
                            At least {{ \App\Models\SystemSetting::get('auth_password_reset_min_length', 8) }} characters long
                        </div>
                        @if(\App\Models\SystemSetting::get('auth_password_reset_require_uppercase', true))
                            <div class="password-rule" :style="/[A-Z]/.test(password) ? 'color: #10b981;' : 'color: var(--text-muted);'">
                                <span x-text="/[A-Z]/.test(password) ? '✓' : '•'"></span>
                                At least one uppercase letter
                            </div>
                        @endif
                        @if(\App\Models\SystemSetting::get('auth_password_reset_require_numbers', true))
                            <div class="password-rule" :style="/[0-9]/.test(password) ? 'color: #10b981;' : 'color: var(--text-muted);'">
                                <span x-text="/[0-9]/.test(password) ? '✓' : '•'"></span>
                                At least one number
                            </div>
                        @endif
                        @if(\App\Models\SystemSetting::get('auth_password_reset_require_symbols', true))
                            <div class="password-rule" :style="/[^A-Za-z0-9]/.test(password) ? 'color: #10b981;' : 'color: var(--text-muted);'">
                                <span x-text="/[^A-Za-z0-9]/.test(password) ? '✓' : '•'"></span>
                                At least one symbol
                            </div>
                        @endif
                    </div>

                    @error('password')
                        <div class="text-danger mt-1" style="font-size: 0.85rem;">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div class="clay-input-group">
                    <label for="password_confirmation" class="clay-input-label">Confirm New Password</label>
                    <input id="password_confirmation" class="clay-inset clay-input" type="password" name="password_confirmation" required placeholder="Re-enter password">
                </div>

                <!-- Submit Button -->
                <button type="submit" class="clay-btn clay-btn-primary" style="width: 100%; padding: 14px; margin-top: 10px; margin-bottom: 16px;">
                    Verify & Reset Password
                </button>
            </form>

            <div style="border-top: 1px solid rgba(0,0,0,0.1); padding-top: 16px; display: flex; justify-content: space-between; font-size: 0.85rem;">
                <a href="{{ route('password.request') }}" style="color: var(--text-muted); text-decoration: underline;">← Request new code</a>
                <a href="{{ route('login') }}" style="color: #3b82f6; text-decoration: underline; font-weight: 500;">Back to Login</a>
            </div>

            <!-- Floating Theme Toggle -->
            <div style="position: absolute; top: 15px; right: 15px;">
                <button @click="darkMode = !darkMode; localStorage.setItem('darkMode', darkMode)" class="clay-btn clay-btn-secondary" style="border-radius: 50%; width: 40px; height: 40px; padding: 0; display: flex; align-items: center; justify-content: center;">
                    <span x-show="!darkMode">🌙</span>
                    <span x-show="darkMode">☀️</span>
                </button>
            </div>
        </div>
    </div>
</body>
</html>
