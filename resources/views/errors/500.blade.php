<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Server Error - {{ \App\Models\SystemSetting::get('general_chatbot_name', 'XrootAI') }}</title>
    <link rel="stylesheet" href="{{ asset('css/claymorphism.css') }}">
    <style>
        .error-container {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
            text-align: center;
        }
        .error-card {
            max-width: 520px;
            padding: 48px 40px;
        }
        .glow {
            position: absolute;
            top: 25%;
            left: 35%;
            width: 350px;
            height: 350px;
            border-radius: 50%;
            background: rgba(255, 75, 75, 0.15);
            filter: blur(100px);
            z-index: -1;
        }
        .error-code {
            font-weight: 900;
            font-size: 4rem;
            margin-bottom: 8px;
            background: linear-gradient(135deg, #ff4b4b 0%, #ff8f00 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
</head>
<body class="dark-mode">
    <div class="glow"></div>
    <div class="error-container">
        <div class="clay-card error-card">
            <div class="error-code">500</div>
            
            <h1 style="font-weight: 800; font-size: 1.7rem; margin-bottom: 16px; color: var(--text-primary);">
                Something Went Wrong
            </h1>
            
            <p style="color: var(--text-muted); font-size: 1.05rem; line-height: 1.6; margin-bottom: 32px;">
                We encountered an unexpected internal server issue while processing your request. Our engineering team has been automatically notified and is working on a fix.
            </p>
            
            <div style="display:flex; justify-content:center; gap: 16px;">
                <a href="{{ url('/') }}" class="clay-btn clay-btn-primary">
                    Return to Home
                </a>
                <a href="mailto:{{ \App\Models\SystemSetting::get('handoff_support_team', 'support@xrootai.com') }}" class="clay-btn clay-btn-secondary">
                    Contact Support
                </a>
            </div>
        </div>
    </div>
</body>
</html>
