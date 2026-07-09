<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Terms of Service - {{ \App\Models\SystemSetting::get('general_chatbot_name', 'XrootAI') }}</title>
    @if(\App\Models\SystemSetting::get('general_site_icon'))
        <link rel="icon" href="{{ \App\Models\SystemSetting::get('general_site_icon') }}">
    @endif
    
    <!-- Stylesheets -->
    <link rel="stylesheet" href="{{ asset('css/claymorphism.css') }}">
    <script src="{{ asset('js/alpine.min.js') }}" defer></script>
    
    <style>
        .glow-sphere {
            position: absolute;
            border-radius: 50%;
            filter: blur(120px);
            z-index: -1;
            opacity: 0.35;
            pointer-events: none;
        }
        .glow-1 {
            top: 10%;
            left: 15%;
            width: 400px;
            height: 400px;
            background: rgba(126, 182, 255, 0.3);
        }
        .glow-2 {
            bottom: 15%;
            right: 15%;
            width: 450px;
            height: 450px;
            background: rgba(168, 224, 99, 0.25);
        }

        body {
            background-color: var(--bg-main);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        .header-bar {
            height: 72px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 5%;
            border-bottom: 1px solid var(--clay-card-border);
            backdrop-filter: blur(12px);
            background: rgba(255, 255, 255, 0.6);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .dark-mode .header-bar {
            background: rgba(21, 25, 40, 0.75);
        }

        .app-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            font-weight: 700;
            font-size: 1.25rem;
            color: var(--text-primary);
        }
        .app-brand-icon {
            width: 38px;
            height: 38px;
            border-radius: 12px;
            background: linear-gradient(135deg, #4a88ff, #56ab2f);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 1.2rem;
            box-shadow: 0 4px 10px rgba(74, 136, 255, 0.35);
        }

        .container {
            max-width: 920px;
            margin: 40px auto;
            padding: 0 20px;
            flex-grow: 1;
        }

        .hero-banner {
            padding: 36px 40px;
            border-radius: 28px;
            margin-bottom: 32px;
            background: var(--clay-card-bg);
            border: 1px solid var(--clay-card-border);
            box-shadow: var(--clay-outer-shadow);
            position: relative;
            overflow: hidden;
        }
        .hero-banner::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 6px;
            height: 100%;
            background: linear-gradient(180deg, #4a88ff, #56ab2f);
        }

        .section-card {
            padding: 32px 36px;
            border-radius: 24px;
            margin-bottom: 24px;
            background: var(--clay-card-bg);
            border: 1px solid var(--clay-card-border);
            box-shadow: var(--clay-outer-shadow);
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }
        .section-card:hover {
            transform: translateY(-2px);
        }

        .section-title {
            font-size: 1.35rem;
            font-weight: 700;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 12px;
            color: var(--text-primary);
        }
        .section-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: rgba(74, 136, 255, 0.12);
            color: #4a88ff;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .dark-mode .section-icon {
            background: rgba(74, 136, 255, 0.2);
            color: #7eb6ff;
        }

        .terms-text {
            line-height: 1.75;
            font-size: 0.98rem;
            color: var(--text-muted);
        }
        .terms-text p {
            margin-bottom: 14px;
        }
        .terms-text ul {
            margin: 12px 0 16px 20px;
        }
        .terms-text li {
            margin-bottom: 8px;
        }
        .terms-text strong {
            color: var(--text-primary);
        }

        .badge-update {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            background: rgba(74, 136, 255, 0.15);
            color: #205cff;
            margin-bottom: 14px;
        }
        .dark-mode .badge-update {
            background: rgba(74, 136, 255, 0.25);
            color: #7eb6ff;
        }

        .footer-bar {
            padding: 30px 5%;
            border-top: 1px solid var(--clay-card-border);
            text-align: center;
            font-size: 0.88rem;
            color: var(--text-muted);
            background: var(--clay-card-bg);
            margin-top: 40px;
        }
        .footer-links {
            display: flex;
            justify-content: center;
            gap: 24px;
            margin-top: 12px;
        }
        .footer-links a {
            color: var(--text-primary);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s ease;
        }
        .footer-links a:hover {
            color: #4a88ff;
        }
    </style>
</head>
<body x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" :class="{ 'dark-mode': darkMode }">
    <div class="glow-sphere glow-1"></div>
    <div class="glow-sphere glow-2"></div>

    <!-- Sticky Navigation Header -->
    <header class="header-bar">
        <a href="{{ route('chat') }}" class="app-brand">
            @php 
                $lightLogo = \App\Models\SystemSetting::get('general_logo_light') ?: \App\Models\SystemSetting::get('general_chatbot_logo'); 
                $darkLogo = \App\Models\SystemSetting::get('general_logo_dark') ?: \App\Models\SystemSetting::get('general_chatbot_logo'); 
            @endphp
            @if($lightLogo || $darkLogo)
                <img :src="darkMode ? '{{ $darkLogo ?: $lightLogo }}' : '{{ $lightLogo ?: $darkLogo }}'" alt="Logo" style="width:36px; height:36px; border-radius:10px; object-fit:contain;">
            @else
                <div class="app-brand-icon">{{ substr(\App\Models\SystemSetting::get('general_chatbot_name', 'XrootAI'), 0, 1) }}</div>
            @endif
            <span>{{ \App\Models\SystemSetting::get('general_chatbot_name', 'XrootAI') }}</span>
        </a>

        <div style="display: flex; align-items: center; gap: 16px;">
            <a href="{{ route('privacy') }}" class="clay-btn clay-btn-secondary" style="text-decoration: none; padding: 8px 16px; font-size: 0.9rem; border-radius: 14px;">Privacy Policy</a>
            <a href="{{ route('chat') }}" class="clay-btn clay-btn-primary" style="text-decoration: none; padding: 8px 18px; font-size: 0.9rem; border-radius: 14px; display: flex; align-items: center; gap: 6px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                <span>Back to Chat</span>
            </a>
            <!-- Dark Mode Toggle -->
            <button @click="darkMode = !darkMode; localStorage.setItem('darkMode', darkMode)" class="clay-btn clay-btn-secondary" style="border-radius: 50%; width: 40px; height: 40px; padding: 0; display: flex; align-items: center; justify-content: center;" title="Toggle Theme">
                <template x-if="!darkMode">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                </template>
                <template x-if="darkMode">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>
                </template>
            </button>
        </div>
    </header>

    <!-- Main Content Container -->
    <main class="container">
        <!-- Hero Header -->
        <div class="hero-banner">
            <div class="badge-update">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                <span>Legal Agreement</span>
            </div>
            <h1 style="font-size: 2.3rem; font-weight: 800; margin-bottom: 12px; letter-spacing: -0.02em;">Terms of Service</h1>
            <p style="font-size: 1.05rem; color: var(--text-muted); line-height: 1.6; max-width: 720px; margin: 0;">
                Welcome to <strong>{{ \App\Models\SystemSetting::get('general_chatbot_name', 'XrootAI') }}</strong>. By accessing our website, creating a user account, or engaging with our conversational AI services, you agree to be bound by the terms, guidelines, and conditions detailed below.
            </p>
        </div>

        <!-- Section 1: Acceptance of Terms & Eligibility -->
        <div class="section-card">
            <div class="section-title">
                <div class="section-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                </div>
                <span>1. Acceptance of Terms & Account Responsibilities</span>
            </div>
            <div class="terms-text">
                <p>By registering for an account or using our chat interface, you confirm that you have read, understood, and accepted these Terms of Service in full:</p>
                <ul>
                    <li><strong>Account Security:</strong> You are responsible for maintaining the confidentiality of your login credentials and for all actions that occur under your user session.</li>
                    <li><strong>Accurate Information:</strong> You agree to provide accurate and complete registration information when authenticating through Single Sign-On (Google/GitHub) or email registration.</li>
                    <li><strong>Service Eligibility:</strong> You must be at least the age of digital consent in your jurisdiction to use this platform.</li>
                </ul>
            </div>
        </div>

        <!-- Section 2: Acceptable Use Policy (AUP) -->
        <div class="section-card">
            <div class="section-title">
                <div class="section-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
                </div>
                <span>2. Acceptable Use & Prohibited Conduct</span>
            </div>
            <div class="terms-text">
                <p>To foster a safe, reliable, and ethical AI ecosystem, you strictly agree <strong>not</strong> to use our platform for any of the following prohibited purposes:</p>
                <ul>
                    <li><strong>Illegal or Harmful Content:</strong> Generating, transmitting, or requesting content that promotes violence, discrimination, illegal activities, harassment, hate speech, or child exploitation.</li>
                    <li><strong>Malicious Engineering:</strong> Using our AI models to generate malicious code, malware, phishing templates, vulnerability exploits, or cyberattack automation tools.</li>
                    <li><strong>Abuse of Infrastructure:</strong> Attempting to bypass system rate limits, scraping endpoints via automated scripts without authorization, or engaging in denial-of-service (DoS) attacks against our servers or AI providers.</li>
                </ul>
                <p>Violation of this Acceptable Use Policy may result in immediate account suspension or permanent ban without prior warning or refund.</p>
            </div>
        </div>

        <!-- Section 3: AI Output Disclaimer ("Hallucinations") -->
        <div class="section-card">
            <div class="section-title">
                <div class="section-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                </div>
                <span>3. AI Output Disclaimer & Limitation of Reliance</span>
            </div>
            <div class="terms-text">
                <p>Our platform leverages state-of-the-art large language models (`OpenAI`, `Gemini`, `Claude`, `DeepSeek`) to provide responsive answers, code generation, and creative assistance. However, you acknowledge that:</p>
                <ul>
                    <li><strong>Probabilistic Nature:</strong> Artificial intelligence outputs are generated probabilistically and may occasionally produce inaccurate, misleading, or fabricated information commonly known as "hallucinations."</li>
                    <li><strong>No Professional Advice:</strong> AI completions provided by {{ \App\Models\SystemSetting::get('general_chatbot_name', 'XrootAI') }} are for informational, educational, and workflow efficiency purposes only. They do not constitute authoritative legal, medical, financial, tax, or professional engineering advice.</li>
                    <li><strong>User Verification:</strong> You are solely responsible for verifying the accuracy, safety, and suitability of any AI-generated code or text before deploying it in critical or production environments.</li>
                </ul>
            </div>
        </div>

        <!-- Section 4: Intellectual Property & Content Ownership -->
        <div class="section-card">
            <div class="section-title">
                <div class="section-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                </div>
                <span>4. Intellectual Property & Content Ownership</span>
            </div>
            <div class="terms-text">
                <p>We believe in empowering our users with full creative and technical ownership over their work:</p>
                <ul>
                    <li><strong>Your Prompts & Inputs:</strong> You retain full copyright and intellectual property rights to all original text prompts, files, and queries you submit to the service.</li>
                    <li><strong>Your Generated Outputs:</strong> Subject to third-party model provider terms and applicable law, you own all rights, titles, and interests in the AI completions generated in response to your prompts.</li>
                    <li><strong>Platform Rights:</strong> {{ \App\Models\SystemSetting::get('general_chatbot_name', 'XrootAI') }} retains all rights, titles, and intellectual property in the platform software, UI claymorphism design system, proprietary algorithms, and brand trademarks.</li>
                </ul>
            </div>
        </div>

        <!-- Section 5: Subscription Plans, Service Limits & Modifications -->
        <div class="section-card">
            <div class="section-title">
                <div class="section-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <span>5. Service Tiers, Availability & Modifications</span>
            </div>
            <div class="terms-text">
                <p>We continually innovate to improve model performance and reliability:</p>
                <ul>
                    <li><strong>Guest vs. Pro Tiers:</strong> Guest mode users may be subject to daily prompt limits (`Guest Mode 5 messages limit`) or restricted model selections. Upgrading to an authenticated or Pro tier unlocks extended context windows and advanced models (`Claude 3.5 Sonnet`, `GPT-4o`).</li>
                    <li><strong>Service Availability:</strong> While we strive for 99.9% uptime, access to specific third-party AI endpoints may occasionally experience latency or maintenance downstream.</li>
                    <li><strong>Updates to Terms:</strong> We reserve the right to update these Terms of Service periodically as new AI features emerge. Continued use of the platform after updates indicates your acceptance of the revised terms.</li>
                </ul>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer-bar">
        <div>&copy; {{ date('Y') }} <strong>{{ \App\Models\SystemSetting::get('general_chatbot_name', 'XrootAI') }}</strong>. All rights reserved. {{ \App\Models\SystemSetting::get('branding_footer_text', '') }}</div>
        <div class="footer-links">
            <a href="{{ route('chat') }}">Home / Chat</a>
            <a href="{{ route('privacy') }}">Privacy Policy</a>
            <a href="{{ route('terms') }}" style="color: #4a88ff;">Terms of Service</a>
        </div>
    </footer>
</body>
</html>
