<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Two-Factor Authentication Code</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f7f6; margin: 0; padding: 30px; color: #333; }
        .container { max-width: 550px; margin: 0 auto; background: #ffffff; padding: 40px; border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .header { text-align: center; border-bottom: 2px solid #f0f4f8; padding-bottom: 20px; margin-bottom: 30px; }
        .logo { font-size: 24px; font-weight: bold; color: #4361ee; text-decoration: none; }
        .title { font-size: 20px; font-weight: 700; color: #1e293b; margin-top: 0; }
        .code-box { background: #f8fafc; border: 2px dashed #cbd5e1; border-radius: 12px; padding: 25px; text-align: center; margin: 30px 0; }
        .otp-code { font-size: 34px; font-weight: 800; letter-spacing: 6px; color: #4361ee; font-family: monospace; }
        .text { font-size: 15px; line-height: 1.6; color: #475569; margin-bottom: 20px; }
        .footer { text-align: center; font-size: 13px; color: #94a3b8; margin-top: 40px; border-top: 1px solid #f0f4f8; padding-top: 20px; }
        .warning { font-size: 13px; color: #dc2626; background: #fef2f2; padding: 12px; border-radius: 8px; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <span class="logo">{{ \App\Models\SystemSetting::get('general_chatbot_name', 'XrootAI') }}</span>
        </div>

        <h1 class="title">Security Check: Verification Code</h1>

        <p class="text">
            Hello <strong>{{ $user->name }}</strong>,<br><br>
            We detected a login attempt to your account from a new or unverified session. Please enter the following 6-digit verification code to complete your login:
        </p>

        <div class="code-box">
            <div class="otp-code">{{ $otp }}</div>
        </div>

        <p class="text" style="text-align: center; font-size: 14px; color: #64748b;">
            This verification code expires in <strong>15 minutes</strong>.
        </p>

        <div class="warning">
            <strong>Security Alert:</strong> If you did not attempt to log in, your password may be compromised. Please reset your password immediately.
        </div>

        <div class="footer">
            &copy; {{ date('Y') }} {{ \App\Models\SystemSetting::get('general_chatbot_name', 'XrootAI') }}. All rights reserved.
        </div>
    </div>
</body>
</html>
