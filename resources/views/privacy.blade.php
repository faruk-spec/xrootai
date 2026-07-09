<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Privacy Policy - {{ \App\Models\SystemSetting::get('general_chatbot_name', 'XrootAI') }}</title>
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

        .policy-text {
            line-height: 1.75;
            font-size: 0.98rem;
            color: var(--text-muted);
        }
        .policy-text p {
            margin-bottom: 14px;
        }
        .policy-text ul {
            margin: 12px 0 16px 20px;
        }
        .policy-text li {
            margin-bottom: 8px;
        }
        .policy-text strong {
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
            background: rgba(86, 171, 47, 0.15);
            color: #3b8216;
            margin-bottom: 14px;
        }
        .dark-mode .badge-update {
            background: rgba(86, 171, 47, 0.25);
            color: #a8e063;
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
            <a href="{{ route('terms') }}" class="clay-btn clay-btn-secondary" style="text-decoration: none; padding: 8px 16px; font-size: 0.9rem; border-radius: 14px;">Terms of Service</a>
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
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                <span>GDPR & CCPA Compliant</span>
            </div>
            <h1 style="font-size: 2.3rem; font-weight: 800; margin-bottom: 12px; letter-spacing: -0.02em;">Privacy Policy</h1>
            <p style="font-size: 1.05rem; color: var(--text-muted); line-height: 1.6; max-width: 720px; margin: 0;">
                At <strong>{{ \App\Models\SystemSetting::get('general_chatbot_name', 'XrootAI') }}</strong>, protecting your data privacy and security is our top priority. This document explains transparently what information we collect, how our intelligent inference pipelines process your prompts, and your absolute rights regarding your personal data.
            </p>
        </div>

        <!-- Section 1: Information We Collect -->
        <div class="section-card">
            <div class="section-title">
                <div class="section-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/></svg>
                </div>
                <span>1. Information We Collect</span>
            </div>
            <div class="policy-text">
                <p>We collect and store only the minimal data required to deliver reliable, high-performance conversational AI experiences:</p>
                <ul>
                    <li><strong>Account Information:</strong> When you register or sign in via Single Sign-On (such as Google or GitHub), we securely collect your name, email address, and profile picture avatar.</li>
                    <li><strong>Chat Prompts & Conversations:</strong> We store your chat messages, AI completions, conversation titles, and customized preferences (such as selected models or system prompts) to maintain your chat history across devices.</li>
                    <li><strong>Uploaded Files & Attachments:</strong> If you upload documents, images, or code files for AI analysis, those files are securely stored on our servers so the AI models can process and inspect their contents.</li>
                    <li><strong>Usage & Technical Diagnostics:</strong> We record standard technical metrics, including browser type, IP address, and request timestamps, solely for diagnostic reliability, rate-limiting, and security monitoring.</li>
                </ul>
            </div>
        </div>

        <!-- Section 2: AI Processing & Third-Party LLM Providers -->
        <div class="section-card">
            <div class="section-title">
                <div class="section-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2a10 10 0 1 0 10 10H12V2z"/><path d="M12 12 2.88 6.72"/><path d="M12 12l9.12 5.28"/></svg>
                </div>
                <span>2. AI Inference & Model Providers</span>
            </div>
            <div class="policy-text">
                <p>To generate intelligent answers, your prompts and relevant context are transmitted securely over encrypted TLS connections to our designated AI inference engines based on your model selection:</p>
                <ul>
                    <li><strong>Supported Model Providers:</strong> Depending on the active model chosen, inferences may be routed to enterprise APIs provided by OpenAI, Google Gemini, Anthropic Claude, DeepSeek, or local self-hosted instances (Ollama).</li>
                    <li><strong>No Public Model Training:</strong> We strictly utilize enterprise API endpoints where third-party model providers commit <strong>not to use your private API inputs or conversation data to train their public foundation models</strong> unless explicitly authorized by enterprise agreements or your voluntary feedback.</li>
                    <li><strong>Encrypted Key Storage:</strong> If you input your own custom API keys in your profile or system configurations, those keys are encrypted at rest using industry-standard AES-256-CBC encryption and are never exposed in plain text.</li>
                </ul>
            </div>
        </div>

        <!-- Section 3: Data Retention & Security -->
        <div class="section-card">
            <div class="section-title">
                <div class="section-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                </div>
                <span>3. Data Retention & System Security</span>
            </div>
            <div class="policy-text">
                <p>We deploy robust, defense-in-depth technical safeguards to shield your personal data from unauthorized access, alteration, or disclosure:</p>
                <ul>
                    <li><strong>Retention Schedule:</strong> Conversations and uploaded attachments remain stored in your account so you can reference them anytime. System logs and temporary diagnostic data are periodically purged according to our retention standards.</li>
                    <li><strong>User-Initiated Deletion:</strong> Whenever you delete a conversation or remove an attachment from your interface, the corresponding records are permanently erased from our active database tables.</li>
                    <li><strong>Infrastructure Protection:</strong> All web traffic is strictly served via HTTPS/TLS encryption. Server databases are protected by restrictive firewall rules, strict role-based access control (RBAC), and continuous monitoring.</li>
                </ul>
            </div>
        </div>

        <!-- Section 4: Cookies & Local Storage -->
        <div class="section-card">
            <div class="section-title">
                <div class="section-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"/><path d="M2 12h20"/></svg>
                </div>
                <span>4. Cookies & Local Browser Storage</span>
            </div>
            <div class="policy-text">
                <p>We do not use invasive advertising trackers or third-party behavioral profiling cookies. We use only essential functional storage:</p>
                <ul>
                    <li><strong>Authentication Cookies:</strong> Required to keep you logged in and verify your session identity securely across requests.</li>
                    <li><strong>Local Storage (`localStorage`):</strong> Used within your web browser to remember your UI preferences, such as whether you prefer Dark Mode or Light Mode and whether your sidebar is collapsed or expanded.</li>
                </ul>
            </div>
        </div>

        <!-- Section 5: Your Rights (GDPR & CCPA) -->
        <div class="section-card">
            <div class="section-title">
                <div class="section-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><polyline points="17 11 19 13 23 9"/></svg>
                </div>
                <span>5. Your Data Rights & Choices</span>
            </div>
            <div class="policy-text">
                <p>Depending on your geographic location (including the European Union under GDPR and California under CCPA), you hold absolute legal rights concerning your personal information:</p>
                <ul>
                    <li><strong>Right to Access & Export:</strong> You can review all your stored conversations directly from your chat sidebar at any time.</li>
                    <li><strong>Right to Erasure ("Right to be Forgotten"):</strong> You have the right to request the complete deletion of your account, API keys, and all historical chat logs. Once requested, your personal data will be permanently wiped from our systems.</li>
                    <li><strong>Right to Rectification:</strong> You can update your profile name and system instruction preferences anytime via the Profile Settings modal.</li>
                </ul>
                <p style="margin-top: 16px;">If you have any questions or wish to exercise your data privacy rights, please reach out to our administration team or contact us via our official support channels.</p>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer-bar">
        <div>&copy; {{ date('Y') }} <strong>{{ \App\Models\SystemSetting::get('general_chatbot_name', 'XrootAI') }}</strong>. All rights reserved. {{ \App\Models\SystemSetting::get('branding_footer_text', '') }}</div>
        <div class="footer-links">
            <a href="{{ route('chat') }}">Home / Chat</a>
            <a href="{{ route('privacy') }}" style="color: #4a88ff;">Privacy Policy</a>
            <a href="{{ route('terms') }}">Terms of Service</a>
        </div>
    </footer>
</body>
</html>
