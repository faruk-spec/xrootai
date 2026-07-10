<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verification Failed - {{ \App\Models\SystemSetting::get('general_chatbot_name', 'App') }}</title>
    @if(\App\Models\SystemSetting::get('general_site_icon'))
        <link rel="icon" href="{{ \App\Models\SystemSetting::get('general_site_icon') }}">
    @endif
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/claymorphism.css') }}">
    <script src="{{ asset('js/alpine.min.js') }}" defer></script>
    <style>
        .ambient-bubble { position: absolute; border-radius: 50%; filter: blur(80px); z-index: -1; opacity: 0.5; }
        .bubble-1 { top: 15%; left: 15%; width: 260px; height: 260px; background: rgba(248, 113, 113, 0.3); }
        .bubble-2 { bottom: 15%; right: 15%; width: 300px; height: 300px; background: rgba(251, 191, 36, 0.2); }
    </style>
</head>
<body x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" :class="{ 'dark-mode': darkMode }">
    <div class="ambient-bubble bubble-1"></div>
    <div class="ambient-bubble bubble-2"></div>

    <div class="auth-container">
        <div class="clay-card auth-card text-center" style="max-width: 460px; padding: 40px 30px;">
            <div style="width: 76px; height: 76px; border-radius: 24px; background: rgba(239, 68, 68, 0.15); color: #ef4444; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; font-size: 2.6rem; box-shadow: inset 2px 2px 4px rgba(255,255,255,0.2), 0 8px 20px rgba(239,68,68,0.2);">
                <i class="bi bi-exclamation-triangle-fill"></i>
            </div>
            <h1 class="auth-title" style="color: #ef4444; font-size: 1.8rem; margin-bottom: 12px;">Verification Failed</h1>
            <p class="auth-subtitle" style="font-size: 0.96rem; line-height: 1.6; margin-bottom: 28px;">
                {{ $message ?? 'The verification link or OTP code has expired or is invalid.' }}
            </p>

            @if(auth()->check())
                <form method="POST" action="{{ route('verification.resend') }}" style="margin-bottom: 14px;">
                    @csrf
                    <button type="submit" class="clay-btn clay-btn-primary" style="display: inline-flex; justify-content: center; align-items: center; gap: 8px; width: 100%; padding: 14px; font-size: 1rem;">
                        <i class="bi bi-arrow-clockwise"></i> <span>Resend Verification Code</span>
                    </button>
                </form>
            @endif

            <a href="{{ route('login') }}" class="clay-btn clay-btn-secondary" style="display: inline-flex; justify-content: center; align-items: center; gap: 8px; width: 100%; padding: 12px; text-decoration: none; font-size: 0.95rem;">
                <i class="bi bi-arrow-left"></i> <span>Back to Login</span>
            </a>
        </div>
    </div>
</body>
</html>
