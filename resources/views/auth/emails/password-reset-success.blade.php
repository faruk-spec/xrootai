<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Security Alert: Password Changed</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #0f172a; color: #f8fafc; margin: 0; padding: 40px 20px; }
        .email-card { max-width: 580px; margin: 0 auto; background-color: #1e293b; border: 1px solid #334155; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.5); overflow: hidden; }
        .header { background: linear-gradient(135deg, #10b981 0%, #059669 100%); padding: 30px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; font-weight: 700; color: #ffffff; }
        .body { padding: 35px 30px; line-height: 1.6; color: #cbd5e1; }
        .info-box { background-color: #0f172a; border-left: 4px solid #10b981; border-radius: 8px; padding: 18px; margin: 24px 0; font-size: 14px; }
        .footer { background-color: #0f172a; padding: 20px; text-align: center; font-size: 12px; color: #64748b; border-top: 1px solid #334155; }
    </style>
</head>
<body>
    <div class="email-card">
        <div class="header">
            <h1>🛡️ Password Changed Successfully</h1>
        </div>
        <div class="body">
            <p>Hi <strong>{{ $userName }}</strong>,</p>
            <p>This is a security notification confirming that the password for your <strong>{{ $appName }}</strong> account (<code>{{ $email }}</code>) has just been successfully changed.</p>

            <div class="info-box">
                <div style="margin-bottom: 6px;"><strong>Time:</strong> {{ $timestamp }}</div>
                @if($ipAddress)
                    <div><strong>IP Address:</strong> <code>{{ $ipAddress }}</code></div>
                @endif
            </div>

            <p style="font-size: 14px; color: #f87171; font-weight: 600; margin-top: 28px;">
                If you did NOT perform this change, please reset your password immediately or contact our support team to lock your account against unauthorized access.
            </p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} {{ $appName }}. All rights reserved.
        </div>
    </div>
</body>
</html>
