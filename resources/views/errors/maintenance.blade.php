<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scheduled Maintenance - {{ \App\Models\SystemSetting::get('general_chatbot_name', 'XrootAI') }}</title>
    <link rel="stylesheet" href="{{ asset('css/claymorphism.css') }}">
    <style>
        .maintenance-container {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
            text-align: center;
        }
        .maintenance-card {
            max-width: 500px;
            padding: 48px 40px;
        }
        .glow {
            position: absolute;
            top: 20%;
            left: 30%;
            width: 350px;
            height: 350px;
            border-radius: 50%;
            background: rgba(74, 136, 255, 0.2);
            filter: blur(100px);
            z-index: -1;
        }
    </style>
</head>
<body class="dark-mode">
    <div class="glow"></div>
    <div class="maintenance-container">
        <div class="clay-card maintenance-card">
            <div style="width: 64px; height: 64px; border-radius: 20px; background: linear-gradient(135deg, #4a88ff, #56ab2f); color: white; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1.8rem; margin: 0 auto 24px; box-shadow: var(--accent-blue-shadow);">
                {{ substr(\App\Models\SystemSetting::get('general_chatbot_name', 'XrootAI'), 0, 1) }}
            </div>
            
            <h1 style="font-weight: 800; font-size: 2rem; margin-bottom: 16px; background: linear-gradient(135deg, #4a88ff 0%, #56ab2f 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                Under Maintenance
            </h1>
            
            <p style="color: var(--text-muted); font-size: 1.05rem; line-height: 1.6; margin-bottom: 32px;">
                {{ \App\Models\SystemSetting::get('general_maintenance_message', 'XrootAI is currently undergoing scheduled maintenance. Please check back later.') }}
            </p>
            
            <div style="display:flex; justify-content:center; gap: 16px;">
                <a href="mailto:{{ \App\Models\SystemSetting::get('handoff_support_team', 'support@xrootai.com') }}" class="clay-btn clay-btn-primary">
                    Contact Support
                </a>
            </div>
        </div>
    </div>
</body>
</html>
