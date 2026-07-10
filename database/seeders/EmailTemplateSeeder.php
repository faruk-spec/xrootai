<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            [
                'slug' => 'user_verification_otp',
                'name' => 'User Registration Verification (OTP)',
                'subject' => 'Verify Your Email Address — {{app_name}}',
                'description' => 'Sent when a new user registers or requests email verification via 6-digit OTP code.',
                'available_variables' => [
                    'user_name' => 'Full name of the registered user',
                    'otp_code' => 'The 6-digit verification code',
                    'expiry_minutes' => 'Minutes until the verification code expires',
                    'app_name' => 'Application brand name',
                    'app_url' => 'Base application URL',
                    'current_year' => 'Current calendar year',
                ],
                'body_html' => <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
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
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <span class="logo">{{app_name}}</span>
        </div>
        <h1 class="title">Verify Your Email Address</h1>
        <p class="text">
            Hello <strong>{{user_name}}</strong>,<br><br>
            Thank you for signing up with {{app_name}}! Please enter the 6-digit verification code below to confirm your email address and activate your account:
        </p>
        <div class="code-box">
            <div class="otp-code">{{otp_code}}</div>
        </div>
        <p class="text" style="text-align: center; font-size: 14px; color: #64748b;">
            This verification code expires in <strong>{{expiry_minutes}} minutes</strong>.
        </p>
        <div class="footer">
            &copy; {{current_year}} {{app_name}}. All rights reserved.
        </div>
    </div>
</body>
</html>
HTML,
                'body_text' => "Hello {{user_name}},\n\nThank you for signing up with {{app_name}}! Your email verification code is: {{otp_code}}\n\nThis code expires in {{expiry_minutes}} minutes.\n\n© {{current_year}} {{app_name}}.",
            ],
            [
                'slug' => 'user_verification_link',
                'name' => 'User Registration Verification (Magic Link)',
                'subject' => 'Confirm Your Email Address — {{app_name}}',
                'description' => 'Sent when a new user registers or requests verification via a clickable verification URL link.',
                'available_variables' => [
                    'user_name' => 'Full name of the registered user',
                    'verification_link' => 'Full clickable email verification URL',
                    'expiry_minutes' => 'Minutes until the link expires',
                    'app_name' => 'Application brand name',
                    'app_url' => 'Base application URL',
                    'current_year' => 'Current calendar year',
                ],
                'body_html' => <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f7f6; margin: 0; padding: 30px; color: #333; }
        .container { max-width: 550px; margin: 0 auto; background: #ffffff; padding: 40px; border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .header { text-align: center; border-bottom: 2px solid #f0f4f8; padding-bottom: 20px; margin-bottom: 30px; }
        .logo { font-size: 24px; font-weight: bold; color: #4361ee; text-decoration: none; }
        .title { font-size: 20px; font-weight: 700; color: #1e293b; margin-top: 0; }
        .btn-box { text-align: center; margin: 35px 0; }
        .btn { background-color: #4361ee; color: #ffffff !important; text-decoration: none; padding: 14px 32px; font-size: 16px; font-weight: 600; border-radius: 8px; display: inline-block; box-shadow: 0 4px 12px rgba(67, 97, 238, 0.3); }
        .text { font-size: 15px; line-height: 1.6; color: #475569; margin-bottom: 20px; }
        .footer { text-align: center; font-size: 13px; color: #94a3b8; margin-top: 40px; border-top: 1px solid #f0f4f8; padding-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <span class="logo">{{app_name}}</span>
        </div>
        <h1 class="title">Confirm Your Email Address</h1>
        <p class="text">
            Hello <strong>{{user_name}}</strong>,<br><br>
            Please click the button below to verify your email address and unlock your {{app_name}} account:
        </p>
        <div class="btn-box">
            <a href="{{verification_link}}" class="btn">Verify Email Address ➜</a>
        </div>
        <p class="text" style="font-size: 13px; color: #64748b; word-break: break-all;">
            If the button doesn't work, copy and paste this URL into your browser:<br>
            <a href="{{verification_link}}" style="color: #4361ee;">{{verification_link}}</a>
        </p>
        <div class="footer">
            &copy; {{current_year}} {{app_name}}. All rights reserved.
        </div>
    </div>
</body>
</html>
HTML,
                'body_text' => "Hello {{user_name}},\n\nPlease verify your email address by visiting this link: {{verification_link}}\n\nThis link expires in {{expiry_minutes}} minutes.\n\n© {{current_year}} {{app_name}}.",
            ],
            [
                'slug' => 'password_reset_otp',
                'name' => 'Password Reset Code (OTP)',
                'subject' => 'Password Reset Verification Code — {{app_name}}',
                'description' => 'Sent when a user requests to reset their account password using a 6-digit verification code.',
                'available_variables' => [
                    'user_name' => 'Full name of the user',
                    'reset_code' => 'The 6-digit password reset verification code',
                    'expiry_minutes' => 'Minutes until the reset code expires',
                    'app_name' => 'Application brand name',
                    'app_url' => 'Base application URL',
                    'current_year' => 'Current calendar year',
                ],
                'body_html' => <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f7f6; margin: 0; padding: 30px; color: #333; }
        .container { max-width: 550px; margin: 0 auto; background: #ffffff; padding: 40px; border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .header { text-align: center; border-bottom: 2px solid #f0f4f8; padding-bottom: 20px; margin-bottom: 30px; }
        .logo { font-size: 24px; font-weight: bold; color: #4361ee; text-decoration: none; }
        .title { font-size: 20px; font-weight: 700; color: #1e293b; margin-top: 0; }
        .code-box { background: #fffbeb; border: 2px dashed #f59e0b; border-radius: 12px; padding: 25px; text-align: center; margin: 30px 0; }
        .otp-code { font-size: 34px; font-weight: 800; letter-spacing: 6px; color: #d97706; font-family: monospace; }
        .text { font-size: 15px; line-height: 1.6; color: #475569; margin-bottom: 20px; }
        .warning { font-size: 13px; color: #dc2626; background: #fef2f2; padding: 12px; border-radius: 8px; margin-top: 20px; }
        .footer { text-align: center; font-size: 13px; color: #94a3b8; margin-top: 40px; border-top: 1px solid #f0f4f8; padding-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <span class="logo">{{app_name}}</span>
        </div>
        <h1 class="title">Reset Your Password</h1>
        <p class="text">
            Hello <strong>{{user_name}}</strong>,<br><br>
            We received a request to reset the password for your {{app_name}} account. Please use the verification code below to proceed:
        </p>
        <div class="code-box">
            <div class="otp-code">{{reset_code}}</div>
        </div>
        <p class="text" style="text-align: center; font-size: 14px; color: #64748b;">
            This reset code expires in <strong>{{expiry_minutes}} minutes</strong>.
        </p>
        <div class="warning">
            <strong>Security Alert:</strong> If you did not request a password reset, you can safely ignore this email. Your account remains secure.
        </div>
        <div class="footer">
            &copy; {{current_year}} {{app_name}}. All rights reserved.
        </div>
    </div>
</body>
</html>
HTML,
                'body_text' => "Hello {{user_name}},\n\nWe received a request to reset your password. Your reset code is: {{reset_code}}\n\nThis code expires in {{expiry_minutes}} minutes. If you did not request this, please ignore this email.\n\n© {{current_year}} {{app_name}}.",
            ],
            [
                'slug' => 'password_reset_link',
                'name' => 'Password Reset Link',
                'subject' => 'Reset Your Password — {{app_name}}',
                'description' => 'Sent when a user requests to reset their account password via a secure token URL link.',
                'available_variables' => [
                    'user_name' => 'Full name of the user',
                    'reset_link' => 'Secure clickable password reset URL',
                    'expiry_minutes' => 'Minutes until the reset link expires',
                    'app_name' => 'Application brand name',
                    'app_url' => 'Base application URL',
                    'current_year' => 'Current calendar year',
                ],
                'body_html' => <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f7f6; margin: 0; padding: 30px; color: #333; }
        .container { max-width: 550px; margin: 0 auto; background: #ffffff; padding: 40px; border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .header { text-align: center; border-bottom: 2px solid #f0f4f8; padding-bottom: 20px; margin-bottom: 30px; }
        .logo { font-size: 24px; font-weight: bold; color: #4361ee; text-decoration: none; }
        .title { font-size: 20px; font-weight: 700; color: #1e293b; margin-top: 0; }
        .btn-box { text-align: center; margin: 35px 0; }
        .btn { background-color: #4361ee; color: #ffffff !important; text-decoration: none; padding: 14px 32px; font-size: 16px; font-weight: 600; border-radius: 8px; display: inline-block; box-shadow: 0 4px 12px rgba(67, 97, 238, 0.3); }
        .text { font-size: 15px; line-height: 1.6; color: #475569; margin-bottom: 20px; }
        .warning { font-size: 13px; color: #dc2626; background: #fef2f2; padding: 12px; border-radius: 8px; margin-top: 20px; }
        .footer { text-align: center; font-size: 13px; color: #94a3b8; margin-top: 40px; border-top: 1px solid #f0f4f8; padding-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <span class="logo">{{app_name}}</span>
        </div>
        <h1 class="title">Reset Your Password</h1>
        <p class="text">
            Hello <strong>{{user_name}}</strong>,<br><br>
            You recently requested to reset your password for your {{app_name}} account. Click the button below to choose a new password:
        </p>
        <div class="btn-box">
            <a href="{{reset_link}}" class="btn">Reset Password Now ➜</a>
        </div>
        <p class="text" style="font-size: 13px; color: #64748b; word-break: break-all;">
            Or copy and paste this URL into your browser:<br>
            <a href="{{reset_link}}" style="color: #4361ee;">{{reset_link}}</a>
        </p>
        <div class="warning">
            <strong>Note:</strong> This link is valid for {{expiry_minutes}} minutes. If you didn't request a password reset, please ignore this email.
        </div>
        <div class="footer">
            &copy; {{current_year}} {{app_name}}. All rights reserved.
        </div>
    </div>
</body>
</html>
HTML,
                'body_text' => "Hello {{user_name}},\n\nTo reset your password, please visit: {{reset_link}}\n\nThis link expires in {{expiry_minutes}} minutes. If you did not request this, please ignore this email.\n\n© {{current_year}} {{app_name}}.",
            ],
            [
                'slug' => 'two_factor_otp',
                'name' => 'Two-Factor Authentication Login Code',
                'subject' => 'Two-Factor Verification Code: {{otp_code}} — {{app_name}}',
                'description' => 'Sent during login when a user has Email Two-Factor Authentication (2FA) active.',
                'available_variables' => [
                    'user_name' => 'Full name of the user attempting to log in',
                    'otp_code' => 'The 6-digit login verification code',
                    'app_name' => 'Application brand name',
                    'app_url' => 'Base application URL',
                    'current_year' => 'Current calendar year',
                ],
                'body_html' => <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f7f6; margin: 0; padding: 30px; color: #333; }
        .container { max-width: 550px; margin: 0 auto; background: #ffffff; padding: 40px; border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .header { text-align: center; border-bottom: 2px solid #f0f4f8; padding-bottom: 20px; margin-bottom: 30px; }
        .logo { font-size: 24px; font-weight: bold; color: #4361ee; text-decoration: none; }
        .title { font-size: 20px; font-weight: 700; color: #1e293b; margin-top: 0; }
        .code-box { background: #f8fafc; border: 2px dashed #cbd5e1; border-radius: 12px; padding: 25px; text-align: center; margin: 30px 0; }
        .otp-code { font-size: 34px; font-weight: 800; letter-spacing: 6px; color: #4361ee; font-family: monospace; }
        .text { font-size: 15px; line-height: 1.6; color: #475569; margin-bottom: 20px; }
        .warning { font-size: 13px; color: #dc2626; background: #fef2f2; padding: 12px; border-radius: 8px; margin-top: 20px; }
        .footer { text-align: center; font-size: 13px; color: #94a3b8; margin-top: 40px; border-top: 1px solid #f0f4f8; padding-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <span class="logo">{{app_name}}</span>
        </div>
        <h1 class="title">Security Check: Verification Code</h1>
        <p class="text">
            Hello <strong>{{user_name}}</strong>,<br><br>
            We detected a login attempt to your account. Please enter the following 6-digit verification code to complete your login:
        </p>
        <div class="code-box">
            <div class="otp-code">{{otp_code}}</div>
        </div>
        <p class="text" style="text-align: center; font-size: 14px; color: #64748b;">
            This verification code expires in <strong>15 minutes</strong>.
        </p>
        <div class="warning">
            <strong>Security Alert:</strong> If you did not attempt to log in, your password or account details may be compromised. Please change your password immediately.
        </div>
        <div class="footer">
            &copy; {{current_year}} {{app_name}}. All rights reserved.
        </div>
    </div>
</body>
</html>
HTML,
                'body_text' => "Hello {{user_name}},\n\nYour 2FA verification login code is: {{otp_code}}\n\nThis code expires in 15 minutes. If you did not attempt to log in, please reset your password immediately.\n\n© {{current_year}} {{app_name}}.",
            ],
        ];

        foreach ($templates as $t) {
            EmailTemplate::updateOrCreate(
                ['slug' => $t['slug']],
                [
                    'name' => $t['name'],
                    'subject' => $t['subject'],
                    'description' => $t['description'],
                    'available_variables' => $t['available_variables'],
                    'body_html' => $t['body_html'],
                    'body_text' => $t['body_text'],
                    'is_active' => true,
                ]
            );
        }
    }
}
