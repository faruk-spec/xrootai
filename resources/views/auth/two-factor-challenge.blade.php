<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Two-Factor Authentication Challenge - {{ \App\Models\SystemSetting::get('general_chatbot_name', 'XrootAI') }}</title>
    @if(\App\Models\SystemSetting::get('general_site_icon'))
        <link rel="icon" href="{{ \App\Models\SystemSetting::get('general_site_icon') }}">
    @endif
    <link rel="stylesheet" href="{{ asset('css/claymorphism.css') }}">
    <script src="{{ asset('js/alpine.min.js') }}" defer></script>
    <style>
        .ambient-bubble { position: absolute; border-radius: 50%; filter: blur(80px); z-index: -1; opacity: 0.5; }
        .bubble-1 { top: 12%; left: 12%; width: 280px; height: 280px; background: rgba(59, 130, 246, 0.35); }
        .bubble-2 { bottom: 12%; right: 12%; width: 300px; height: 300px; background: rgba(16, 185, 129, 0.35); }
        .challenge-input { text-align: center; font-size: 1.6rem; letter-spacing: 8px; font-weight: 700; font-family: monospace; }
    </style>
</head>
<body x-data="{ darkMode: localStorage.getItem('darkMode') === 'true', recoveryMode: false }" :class="{ 'dark-mode': darkMode }">
    <div class="ambient-bubble bubble-1"></div>
    <div class="ambient-bubble bubble-2"></div>

    <div class="auth-container">
        <div class="clay-card auth-card" style="max-width: 460px;">
            <!-- Header Icon -->
            <div style="text-align: center; margin-bottom: 22px;">
                <div style="width: 68px; height: 68px; border-radius: 50%; background: rgba(59, 130, 246, 0.15); color: #3b82f6; display: flex; align-items: center; justify-content: center; margin: 0 auto 14px; font-size: 2rem;">
                    🛡️
                </div>
                <h1 class="auth-title">Two-Factor Security</h1>
                <p class="auth-subtitle" x-show="!recoveryMode">
                    Enter the 6-digit verification code from your <strong style="color:#3b82f6;">{{ $user->two_factor_type === 'totp' ? 'Authenticator App' : 'Registered Email' }}</strong>
                </p>
                <p class="auth-subtitle" x-show="recoveryMode" x-cloak style="color: #d97706;">
                    Enter one of your emergency recovery codes to regain access
                </p>
            </div>

            @if(session('error'))
                <div style="background: #fee2e2; color: #b91c1c; border: 1px solid #f87171; padding: 12px 14px; border-radius: 12px; margin-bottom: 16px; font-size: 0.88rem; text-align: left;">
                    ⚠️ {{ session('error') }}
                </div>
            @endif
            @if(session('status') || session('success'))
                <div style="background: #dcfce7; color: #15803d; border: 1px solid #86efac; padding: 12px 14px; border-radius: 12px; margin-bottom: 16px; font-size: 0.88rem; text-align: left;">
                    ✅ {{ session('status') ?: session('success') }}
                </div>
            @endif
            @if($errors->any())
                <div style="background: #fee2e2; color: #b91c1c; border: 1px solid #f87171; padding: 12px 14px; border-radius: 12px; margin-bottom: 16px; font-size: 0.88rem; text-align: left;">
                    ⚠️ {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('two-factor.verify') }}">
                @csrf

                <div class="form-group mb-4">
                    <label class="form-label" x-text="recoveryMode ? 'Emergency Recovery Code' : '6-Digit Verification Code'"></label>
                    <input type="text" 
                           name="code" 
                           class="clay-input challenge-input" 
                           :placeholder="recoveryMode ? 'XXXX-XXXX' : '123456'"
                           :maxlength="recoveryMode ? 20 : 6"
                           :inputmode="recoveryMode ? 'text' : 'numeric'"
                           @input="if (!recoveryMode) { $event.target.value = $event.target.value.replace(/[^0-9]/g, '').slice(0, 6); }"
                           required 
                           autofocus 
                           autocomplete="one-time-code">
                </div>

                <div style="margin-bottom: 22px; display: flex; align-items: center; justify-content: space-between;">
                    <label style="display: flex; align-items: center; gap: 8px; font-size: 0.88rem; cursor: pointer; color: #64748b;">
                        <input type="checkbox" name="remember_device" value="1" style="width: 18px; height: 18px; border-radius: 6px; cursor: pointer;" checked>
                        <span>Trust this device for 30 days</span>
                    </label>
                </div>

                <button type="submit" class="clay-button primary" style="width: 100%; padding: 14px; font-size: 1.05rem; font-weight: 700; margin-bottom: 16px;">
                    Verify & Continue ➜
                </button>
            </form>

            <div style="text-align: center; border-top: 1px solid rgba(0,0,0,0.06); padding-top: 18px; display: flex; flex-direction: column; gap: 12px;">
                @if($user->two_factor_type === 'email')
                    <form method="POST" action="{{ route('two-factor.resend') }}">
                        @csrf
                        <button type="submit" style="background: none; border: none; color: #3b82f6; font-weight: 600; font-size: 0.88rem; cursor: pointer; text-decoration: underline;">
                            Didn't receive the email? Send another code
                        </button>
                    </form>
                @endif

                <div>
                    <button type="button" @click="recoveryMode = !recoveryMode" style="background: none; border: none; color: #64748b; font-size: 0.84rem; cursor: pointer;">
                        <span x-text="recoveryMode ? '⬅ Use 6-digit Verification Code instead' : 'Lost your phone/email? Use an Emergency Recovery Code 🔑'"></span>
                    </button>
                </div>

                <div style="margin-top: 8px;">
                    <form method="POST" action="{{ route('two-factor.cancel') }}">
                        @csrf
                        <button type="submit" style="background: none; border: none; color: #94a3b8; font-size: 0.82rem; cursor: pointer; text-decoration: underline;">
                            ⬅ Cancel and Back to Login
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
