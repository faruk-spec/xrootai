<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - {{ \App\Models\SystemSetting::get('general_chatbot_name', 'XrootAI') }}</title>
    @if(\App\Models\SystemSetting::get('general_site_icon'))
        <link rel="icon" href="{{ \App\Models\SystemSetting::get('general_site_icon') }}">
    @endif
    <link rel="stylesheet" href="{{ asset('css/claymorphism.css') }}">
    <script src="{{ asset('js/alpine.min.js') }}" defer></script>
    <style>
        .ambient-bubble { position: absolute; border-radius: 50%; filter: blur(80px); z-index: -1; opacity: 0.5; }
        .bubble-1 { top: 12%; left: 12%; width: 260px; height: 260px; background: rgba(245, 158, 11, 0.35); }
        .bubble-2 { bottom: 12%; right: 12%; width: 300px; height: 300px; background: rgba(59, 130, 246, 0.35); }
    </style>
</head>
<body x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" :class="{ 'dark-mode': darkMode }">
    <div class="ambient-bubble bubble-1"></div>
    <div class="ambient-bubble bubble-2"></div>

    <div class="auth-container">
        <div class="clay-card auth-card" style="max-width: 460px;">
            <!-- Back to Login Button -->
            <div style="display: flex; justify-content: flex-start; margin-bottom: 14px;">
                <a href="{{ route('login') }}" class="clay-btn clay-btn-secondary" style="display: inline-flex; align-items: center; gap: 6px; text-decoration: none; padding: 6px 12px; border-radius: 10px; font-size: 0.85rem; color: var(--text-color);">
                    ← Back to Login
                </a>
            </div>

            <!-- Header Icon -->
            <div style="text-align: center; margin-bottom: 20px;">
                <div style="width: 64px; height: 64px; border-radius: 50%; background: rgba(245, 158, 11, 0.15); color: #f59e0b; display: flex; align-items: center; justify-content: center; margin: 0 auto 14px; font-size: 1.9rem;">
                    🔑
                </div>
                <h1 class="auth-title">Forgot Password?</h1>
                <p class="auth-subtitle">Enter your registered email address and we will send you a verification code and secure reset link.</p>
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

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <!-- Email Address -->
                <div class="clay-input-group">
                    <label for="email" class="clay-input-label">Email Address</label>
                    <input id="email" class="clay-inset clay-input" type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="name@example.com">
                    @error('email')
                        <div class="text-danger mt-1" style="font-size: 0.85rem;">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Submit Button -->
                <button type="submit" class="clay-btn clay-btn-primary" style="width: 100%; padding: 14px; margin-top: 10px; margin-bottom: 16px;">
                    Send Verification Code & Link
                </button>
            </form>

            <div class="text-center mt-3" style="font-size: 0.86rem; color: var(--text-muted);">
                Remembered your password? <a href="{{ route('login') }}" class="auth-link">Log in here</a>
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
