<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Two-Factor Authentication Challenge - {{ \App\Models\SystemSetting::get('general_chatbot_name', 'App') }}</title>
    @if(\App\Models\SystemSetting::get('general_site_icon'))
        <link rel="icon" href="{{ \App\Models\SystemSetting::get('general_site_icon') }}">
    @endif
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/claymorphism.css') }}">
    <script src="{{ asset('js/alpine.min.js') }}" defer></script>
    <style>
        .ambient-bubble { position: absolute; border-radius: 50%; filter: blur(80px); z-index: -1; opacity: 0.5; }
        .bubble-1 { top: 12%; left: 12%; width: 280px; height: 280px; background: rgba(59, 130, 246, 0.35); }
        .bubble-2 { bottom: 12%; right: 12%; width: 300px; height: 300px; background: rgba(16, 185, 129, 0.35); }
        .challenge-input { text-align: center; font-size: 1.6rem; letter-spacing: 8px; font-weight: 700; font-family: monospace; }
        .alert-box { padding: 12px 14px; border-radius: 14px; margin-bottom: 18px; font-size: 0.88rem; text-align: left; display: flex; align-items: center; gap: 10px; line-height: 1.4; }
        .alert-error { background: rgba(239, 68, 68, 0.15); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.35); }
        .alert-success { background: rgba(34, 197, 94, 0.15); color: #22c55e; border: 1px solid rgba(34, 197, 94, 0.35); }
    </style>
</head>
<body x-data="{ darkMode: localStorage.getItem('darkMode') === 'true', recoveryMode: false }" :class="{ 'dark-mode': darkMode }">
    <div class="ambient-bubble bubble-1"></div>
    <div class="ambient-bubble bubble-2"></div>

    <div class="auth-container">
        <div class="clay-card auth-card" style="max-width: 480px; padding: 36px 32px;">
            <!-- Header Icon -->
            <div style="text-align: center; margin-bottom: 24px;">
                <div style="width: 72px; height: 72px; border-radius: 20px; background: rgba(74, 136, 255, 0.15); color: #4a88ff; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; font-size: 2.2rem; box-shadow: inset 2px 2px 4px rgba(255,255,255,0.2), 0 8px 20px rgba(74,136,255,0.2);">
                    <i class="bi bi-shield-lock-fill"></i>
                </div>
                <h1 class="auth-title">Two-Factor Security</h1>
                <p class="auth-subtitle mb-0" x-show="!recoveryMode">
                    Enter the 6-digit verification code from your <strong style="color: #4a88ff;">{{ $user->two_factor_type === 'totp' ? 'Authenticator App' : 'Registered Email' }}</strong>
                </p>
                <p class="auth-subtitle mb-0" x-show="recoveryMode" x-cloak style="color: #f59e0b;">
                    Enter one of your emergency recovery codes to regain access
                </p>
            </div>

            @if(session('error'))
                <div class="alert-box alert-error">
                    <i class="bi bi-exclamation-triangle-fill fs-5 flex-shrink-0"></i>
                    <div>{{ session('error') }}</div>
                </div>
            @endif
            @if(session('status') || session('success'))
                <div class="alert-box alert-success">
                    <i class="bi bi-check-circle-fill fs-5 flex-shrink-0"></i>
                    <div>{{ session('status') ?: session('success') }}</div>
                </div>
            @endif
            @if($errors->any())
                <div class="alert-box alert-error">
                    <i class="bi bi-exclamation-triangle-fill fs-5 flex-shrink-0"></i>
                    <div>{{ $errors->first() }}</div>
                </div>
            @endif

            <form method="POST" action="{{ route('two-factor.verify') }}">
                @csrf

                <div class="clay-input-group mb-4">
                    <label class="clay-input-label fw-bold d-block mb-2" style="font-size: 0.92rem; color: var(--text-primary);" x-text="recoveryMode ? 'Emergency Recovery Code' : '6-Digit Verification Code'"></label>
                    <input type="text" 
                           name="code" 
                           class="clay-inset clay-input challenge-input" 
                           :placeholder="recoveryMode ? 'XXXX-XXXX' : '123456'"
                           :maxlength="recoveryMode ? 20 : 6"
                           :inputmode="recoveryMode ? 'text' : 'numeric'"
                           @input="if (!recoveryMode) { $event.target.value = $event.target.value.replace(/[^0-9]/g, '').slice(0, 6); }"
                           required 
                           autofocus 
                           autocomplete="one-time-code">
                </div>

                <div style="margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between;">
                    <label style="display: flex; align-items: center; gap: 8px; font-size: 0.88rem; cursor: pointer; color: var(--text-muted); user-select: none;">
                        <input type="checkbox" name="remember_device" value="1" style="width: 18px; height: 18px; border-radius: 6px; accent-color: #4a88ff; cursor: pointer;" checked>
                        <span>Trust this device for 30 days</span>
                    </label>
                </div>

                <button type="submit" class="clay-btn clay-btn-primary" style="width: 100%; padding: 14px; font-size: 1.05rem; font-weight: 700; margin-bottom: 20px;">
                    <span>Verify & Continue</span> <i class="bi bi-arrow-right"></i>
                </button>
            </form>

            <div style="text-align: center; border-top: 1px solid var(--clay-card-border); padding-top: 20px; display: flex; flex-direction: column; gap: 14px;">
                @if($user->two_factor_type === 'email')
                    <form method="POST" action="{{ route('two-factor.resend') }}">
                        @csrf
                        <button type="submit" style="background: none; border: none; color: #4a88ff; font-weight: 600; font-size: 0.88rem; cursor: pointer; text-decoration: underline; display: inline-flex; align-items: center; gap: 6px;">
                            <i class="bi bi-envelope-at-fill"></i> Didn't receive the email? Send another code
                        </button>
                    </form>
                @endif

                <div>
                    <button type="button" @click="recoveryMode = !recoveryMode" class="clay-btn clay-btn-secondary py-2 px-3 small" style="font-size: 0.84rem;">
                        <i class="bi" :class="recoveryMode ? 'bi-123' : 'bi-key-fill'"></i>
                        <span x-text="recoveryMode ? 'Use 6-digit Verification Code instead' : 'Lost phone/email? Use Recovery Code'"></span>
                    </button>
                </div>

                <div style="margin-top: 6px;">
                    <form method="POST" action="{{ route('two-factor.cancel') }}">
                        @csrf
                        <button type="submit" style="background: none; border: none; color: var(--text-muted); font-size: 0.84rem; cursor: pointer; text-decoration: underline; display: inline-flex; align-items: center; gap: 6px;">
                            <i class="bi bi-box-arrow-left"></i> Cancel and Back to Login
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
