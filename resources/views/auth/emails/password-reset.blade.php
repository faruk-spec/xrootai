<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reset Your Password</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #0f172a; color: #f8fafc; margin: 0; padding: 40px 20px; }
        .email-card { max-width: 580px; margin: 0 auto; background-color: #1e293b; border: 1px solid #334155; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.5); overflow: hidden; }
        .header { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); padding: 30px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; font-weight: 700; color: #ffffff; }
        .body { padding: 35px 30px; line-height: 1.6; color: #cbd5e1; }
        .otp-box { background-color: #0f172a; border: 2px dashed #f59e0b; border-radius: 14px; padding: 20px; text-align: center; margin: 25px 0; }
        .otp-code { font-size: 36px; font-weight: 800; letter-spacing: 8px; color: #fbbf24; font-family: monospace; }
        .btn-link { display: inline-block; background-color: #f59e0b; color: #ffffff !important; text-decoration: none; font-weight: 600; padding: 14px 28px; border-radius: 12px; margin: 20px 0; box-shadow: 0 4px 12px rgba(245, 158, 11, 0.4); }
        .footer { background-color: #0f172a; padding: 20px; text-align: center; font-size: 12px; color: #64748b; border-top: 1px solid #334155; }
    </style>
</head>
<body>
    <div class="email-card">
        <div class="header">
            <h1>🔐 Password Reset Request</h1>
        </div>
        <div class="body">
            <p>Hi <strong>{{ $userName }}</strong>,</p>
            <p>We received a request to reset your password for your <strong>{{ $appName }}</strong> account @if($ipAddress)(from IP: <code>{{ $ipAddress }}</code>)@endif.</p>

            <div class="otp-box">
                <div style="font-size: 12px; text-transform: uppercase; letter-spacing: 1.5px; color: #94a3b8; margin-bottom: 8px;">Your Verification Code (OTP)</div>
                <div class="otp-code">{{ $otpCode }}</div>
                <div style="font-size: 12px; color: #f87171; margin-top: 8px;">⏱ Expires in {{ $expiryMinutes }} minutes</div>
            </div>

            <div style="text-align: center;">
                <p>Or click the secure button below to set a new password directly:</p>
                <a href="{{ $resetUrl }}" class="btn-link">Reset Password Now</a>
            </div>

            <p style="font-size: 13px; color: #94a3b8; margin-top: 30px; border-top: 1px solid #334155; padding-top: 20px;">
                If the button above does not open, copy and paste this link into your web browser:<br>
                <a href="{{ $resetUrl }}" style="color: #fbbf24; word-break: break-all;">{{ $resetUrl }}</a>
            </p>
            <p style="font-size: 13px; color: #f87171; font-weight: 600;">If you did NOT request a password reset, please ignore this email or contact support immediately if you suspect unauthorized access to your account.</p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} {{ $appName }}. All rights reserved.
        </div>
    </div>
</body>
</html>
