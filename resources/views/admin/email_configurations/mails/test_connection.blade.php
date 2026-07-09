<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>SMTP Connection Test</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f6f9; color: #333333; margin: 0; padding: 30px; }
        .container { max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); overflow: hidden; border: 1px solid #e1e8ed; }
        .header { background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); color: #ffffff; padding: 25px 30px; text-align: center; }
        .header h1 { margin: 0; font-size: 22px; font-weight: 700; }
        .body { padding: 30px; line-height: 1.6; }
        .badge { display: inline-block; background-color: #dcfce7; color: #166534; padding: 5px 12px; border-radius: 20px; font-weight: 600; font-size: 14px; margin-bottom: 20px; }
        .details-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .details-table td { padding: 10px 14px; border-bottom: 1px solid #f1f5f9; font-size: 14px; }
        .details-table td.label { font-weight: 600; color: #64748b; width: 35%; }
        .details-table td.value { color: #1e293b; font-family: monospace; }
        .footer { background-color: #f8fafc; padding: 20px 30px; text-align: center; font-size: 12px; color: #94a3b8; border-top: 1px solid #f1f5f9; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>SaaS SMTP Connection Verified</h1>
        </div>
        <div class="body">
            <div style="text-align: center;">
                <span class="badge">✔ Connection Successful</span>
            </div>
            <p>Hello Admin,</p>
            <p>This is a verification email automatically sent by your <strong>XrootAI</strong> application to confirm that the SMTP settings for <strong>{{ $providerName }}</strong> are working correctly.</p>

            <table class="details-table">
                <tr>
                    <td class="label">Provider Name</td>
                    <td class="value">{{ $providerName }}</td>
                </tr>
                <tr>
                    <td class="label">SMTP Host</td>
                    <td class="value">{{ $host }}</td>
                </tr>
                <tr>
                    <td class="label">SMTP Port</td>
                    <td class="value">{{ $port }}</td>
                </tr>
                <tr>
                    <td class="label">Encryption</td>
                    <td class="value">{{ strtoupper(strval($encryption ?: 'None')) }}</td>
                </tr>
                <tr>
                    <td class="label">From Address</td>
                    <td class="value">{{ $fromEmail }}</td>
                </tr>
                <tr>
                    <td class="label">Tested At</td>
                    <td class="value">{{ $timestamp }}</td>
                </tr>
            </table>

            <p style="margin-top: 25px; font-size: 13px; color: #64748b;">If you received this message, emails from this provider are ready to be sent to users, notifications, and queues.</p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} XrootAI System. All rights reserved.
        </div>
    </div>
</body>
</html>
