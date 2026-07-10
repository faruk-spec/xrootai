<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Two-Factor Authentication Code</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #0f172a; margin: 0; padding: 40px 20px; color: #f8fafc; }
        .container { max-width: 560px; margin: 0 auto; background: #1e293b; padding: 40px; border-radius: 20px; border: 1px solid #334155; box-shadow: 0 10px 30px rgba(0,0,0,0.5); }
        .header { text-align: center; border-bottom: 2px solid #334155; padding-bottom: 25px; margin-bottom: 30px; }
        .logo { font-size: 26px; font-weight: 800; background: linear-gradient(135deg, #60a5fa 0%, #a855f7 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; text-decoration: none; }
        .badge { display: inline-block; background-color: rgba(245, 158, 11, 0.2); color: #fbbf24; padding: 6px 14px; border-radius: 50px; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 15px; border: 1px solid rgba(245, 158, 11, 0.4); }
        .title { font-size: 22px; font-weight: 700; color: #ffffff; margin-top: 0; }
        .code-box { background: #0f172a; border: 2px dashed #3b82f6; border-radius: 14px; padding: 28px; text-align: center; margin: 30px 0; }
        .otp-code { font-size: 38px; font-weight: 800; letter-spacing: 8px; color: #38bdf8; font-family: monospace; }
        .text { font-size: 15px; line-height: 1.6; color: #cbd5e1; margin-bottom: 20px; }
        .warning { font-size: 13px; color: #fca5a5; background: rgba(239, 68, 68, 0.15); padding: 16px; border-radius: 10px; border-left: 4px solid #ef4444; margin-top: 25px; line-height: 1.5; }
        .footer { text-align: center; font-size: 13px; color: #64748b; margin-top: 40px; border-top: 1px solid #334155; padding-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <span class="logo">{{ \App\Models\SystemSetting::get('general_chatbot_name', 'XrootAI') }}</span>
        </div>
        <div style="text-align: center;">
            <span class="badge">🔒 Two-Factor Security Alert</span>
            <h1 class="title">Login Verification Code</h1>
        </div>
        <p class="text">
            Hello <strong>{{ $user->name }}</strong>,<br><br>
            A sign-in attempt requires Two-Factor Authentication (2FA). Please use the secure 6-digit one-time password below to complete your login:
        </p>
        <div class="code-box">
            <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 2px; color: #94a3b8; margin-bottom: 10px;">2FA ONE-TIME PASSWORD</div>
            <div class="otp-code">{{ $otp }}</div>
            <div style="font-size: 12px; color: #f87171; margin-top: 10px;">⏱ Expires in 15 minutes</div>
        </div>
        <p class="text" style="font-size: 14px; color: #94a3b8; text-align: center;">
            Enter this code on the 2FA Challenge screen to authenticate.
        </p>
        <div class="warning">
            <strong>⚠️ Security Notice:</strong> If you did not initiate this login attempt, someone may know your password. Please immediately reset your account password and verify your account activity.
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} {{ \App\Models\SystemSetting::get('general_chatbot_name', 'XrootAI') }} Security Team. All rights reserved.
        </div>
    </div>
</body>
</html>
