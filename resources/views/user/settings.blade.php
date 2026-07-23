<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Settings – {{ \App\Models\SystemSetting::get('general_chatbot_name', 'App') }}</title>
    <meta name="description" content="Manage your AI chat preferences, appearance, API keys, and account security settings.">
    @if(\App\Models\SystemSetting::get('general_site_icon'))
        <link rel="icon" href="{{ \App\Models\SystemSetting::get('general_site_icon') }}">
    @endif
    <link rel="stylesheet" href="{{ asset('css/claymorphism.css') }}">
    <script src="{{ asset('js/alpine.min.js') }}" defer></script>
    <style>
        /* ─── Page Shell ───────────────────────────────────────────── */
        html, body { height: 100%; }
        body {
            background-color: var(--bg-main);
            font-family: var(--font-sans);
            color: var(--text-primary);
            min-height: 100vh;
            overflow-x: hidden;
            transition: background-color .25s ease, color .25s ease;
        }

        /* ─── Ambient Bubbles ───────────────────────────────────────── */
        .ambient-bubble {
            position: fixed; border-radius: 50%;
            filter: blur(110px); z-index: 0;
            opacity: .28; pointer-events: none;
        }
        .bubble-1 { top: 3%; left: 8%; width: 420px; height: 420px; background: rgba(74,136,255,.35); }
        .bubble-2 { bottom: 8%; right: 6%; width: 460px; height: 460px; background: rgba(34,197,94,.22); }

        /* ─── Layout Shell (Full Extreme Leftbar + Scrollable Main) ─── */
        .page-shell {
            display: flex; width: 100%; height: 100vh; overflow: hidden;
            position: relative; z-index: 1;
        }

        /* ─── Extreme Left Sidebar ──────────────────────────────────── */
        .settings-sidebar {
            width: 264px; height: 100vh; flex-shrink: 0;
            background: var(--clay-card-bg);
            backdrop-filter: blur(20px);
            border-right: 1px solid var(--clay-card-border);
            padding: 24px 18px;
            display: flex; flex-direction: column;
            overflow-y: auto; z-index: 50;
            transition: transform .25s cubic-bezier(.2,0,0,1);
        }

        /* Sidebar Header Logo */
        .sidebar-header {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 24px; padding: 0 4px;
        }
        .sidebar-brand { display: flex; align-items: center; gap: 12px; text-decoration: none; }
        .app-logo-badge {
            width: 42px; height: 42px; border-radius: 14px; flex-shrink: 0;
            background: linear-gradient(135deg, #4a88ff, #56ab2f);
            color: #fff; font-weight: 800; font-size: 1.25rem;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 4px 14px rgba(74,136,255,.3);
        }
        .sidebar-brand-text { font-size: 1.1rem; font-weight: 700; color: var(--text-primary); line-height: 1.2; }

        /* User chip inside sidebar */
        .user-chip {
            display: flex; align-items: center; gap: 12px;
            padding: 12px; border-radius: 16px;
            background: var(--clay-input-bg);
            border: 1px solid var(--clay-card-border);
            box-shadow: var(--clay-input-shadow);
            margin-bottom: 24px;
        }
        .user-avatar {
            width: 38px; height: 38px; border-radius: 50%;
            background: linear-gradient(135deg, #4a88ff, #3b5bdb);
            color: #fff; font-weight: 700; font-size: 1rem;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .user-name  { font-size: .9rem; font-weight: 700; color: var(--text-primary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .user-email { font-size: .76rem; color: var(--text-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

        .nav-section-label {
            font-size: .68rem; font-weight: 700; letter-spacing: .08em;
            text-transform: uppercase; color: var(--text-muted);
            padding: 0 8px; margin-bottom: 8px;
        }

        .settings-nav { display: flex; flex-direction: column; gap: 4px; flex: 1; }
        .nav-item {
            display: flex; align-items: center; gap: 12px;
            width: 100%; padding: 11px 14px;
            border-radius: 14px; border: none; cursor: pointer;
            font-size: .9rem; font-weight: 600; text-align: left;
            color: var(--text-muted); background: transparent;
            transition: all .2s cubic-bezier(.2,0,0,1);
            text-decoration: none;
        }
        .nav-item:hover  { color: var(--text-primary); background: rgba(126,182,255,.12); }
        .nav-item.active {
            color: #ffffff !important;
            background: linear-gradient(135deg, #4a88ff 0%, #3b5bdb 100%);
            box-shadow: 0 6px 18px rgba(74,136,255,.38);
        }
        .nav-item svg { flex-shrink: 0; }

        .nav-divider { border: none; border-top: 1px solid var(--clay-card-border); margin: 16px 0; }

        /* ─── Main Scrollable Area ──────────────────────────────────── */
        .settings-main {
            flex: 1; height: 100vh; overflow-y: auto;
            padding: 36px 48px 80px; position: relative;
        }

        /* Mobile Header */
        .mobile-header {
            display: none; align-items: center; justify-content: space-between;
            padding: 16px 20px; margin-bottom: 24px;
            background: var(--clay-card-bg);
            border: 1px solid var(--clay-card-border);
            border-radius: 18px; box-shadow: var(--clay-outer-shadow);
        }

        /* Sidebar Backdrop on Mobile */
        .sidebar-backdrop {
            display: none; position: fixed; inset: 0;
            background: rgba(15, 23, 42, 0.45); backdrop-filter: blur(4px);
            z-index: 45;
        }

        /* ─── Mobile Responsiveness (< 900px) ───────────────────────── */
        @media (max-width: 900px) {
            .settings-main { padding: 20px 18px 60px; }
            .mobile-header { display: flex; }
            .settings-sidebar {
                position: fixed; top: 0; left: 0; height: 100vh;
                transform: translateX(-100%); z-index: 50;
                box-shadow: 20px 0 40px rgba(0,0,0,.15);
            }
            .settings-sidebar.open { transform: translateX(0); }
            .sidebar-backdrop.open { display: block; }
        }

        /* ─── Content Panel ─────────────────────────────────────────── */
        .settings-panel {
            background: var(--clay-card-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--clay-card-border);
            border-radius: 24px;
            padding: 36px;
            box-shadow: var(--clay-outer-shadow);
            max-width: 1000px; margin: 0 auto;
        }
        @media (max-width: 640px) { .settings-panel { padding: 22px 16px; border-radius: 20px; } }

        /* Panel section header */
        .section-header {
            display: flex; align-items: flex-start; justify-content: space-between;
            flex-wrap: wrap; gap: 12px;
            padding-bottom: 22px; margin-bottom: 28px;
            border-bottom: 1px solid var(--clay-card-border);
        }
        .section-title {
            display: flex; align-items: center; gap: 10px;
            font-size: 1.08rem; font-weight: 700; color: var(--text-primary);
        }
        .section-title svg { color: var(--accent); }
        .section-desc { font-size: .84rem; color: var(--text-muted); margin-top: 4px; }

        /* ─── Form Primitives ───────────────────────────────────────── */
        .field-group { display: flex; flex-direction: column; gap: 8px; margin-bottom: 24px; }
        .field-label { font-size: .88rem; font-weight: 600; color: var(--text-primary); }
        .field-hint  { font-size: .78rem; color: var(--text-muted); margin-top: 4px; }

        .field-input, .field-select, .field-textarea {
            width: 100%;
            background: var(--clay-input-bg) !important;
            border: 1px solid var(--clay-card-border) !important;
            border-radius: 14px;
            padding: 12px 16px;
            font-size: .93rem;
            color: var(--text-primary) !important;
            box-shadow: var(--clay-input-shadow) !important;
            transition: border-color .2s, box-shadow .2s;
            font-family: var(--font-sans);
            outline: none;
        }
        .field-input:focus, .field-select:focus, .field-textarea:focus {
            border-color: var(--accent) !important;
            box-shadow: 0 0 0 3px rgba(74,136,255,.22), var(--clay-input-shadow) !important;
        }
        .field-textarea { resize: vertical; min-height: 130px; }
        .field-select   { appearance: none; cursor: pointer; }

        /* Prefix input group */
        .input-group {
            display: flex;
            border-radius: 14px;
            overflow: hidden;
            border: 1px solid var(--clay-card-border) !important;
            box-shadow: var(--clay-input-shadow);
        }
        .input-group-prefix {
            display: flex; align-items: center; justify-content: center;
            padding: 0 14px;
            background: var(--clay-card-bg);
            border-right: 1px solid var(--clay-card-border);
            color: var(--text-muted);
            flex-shrink: 0;
        }
        .input-group .field-input {
            border: none !important; border-radius: 0 !important;
            box-shadow: none !important;
            flex: 1;
        }
        .input-group .field-input:focus {
            box-shadow: none !important;
        }

        /* Form footer */
        .form-footer {
            display: flex; justify-content: flex-end;
            padding-top: 22px; margin-top: 8px;
            border-top: 1px solid var(--clay-card-border);
        }

        /* ─── Alerts (#2 Fix - High contrast readability in light/dark) */
        .alert {
            display: flex; align-items: flex-start; gap: 12px;
            padding: 14px 18px; border-radius: 16px;
            font-size: .88rem; margin-bottom: 20px;
            font-weight: 600;
        }
        .alert-success { background: rgba(16,185,129,.16); color: #065f46; border: 1px solid rgba(16,185,129,.35); }
        .dark-mode .alert-success { color: #34d399; }
        .alert-danger  { background: rgba(239, 68, 68,.16); color: #991b1b; border: 1px solid rgba(239,68,68,.35); }
        .dark-mode .alert-danger  { color: #f87171; }
        .alert svg     { flex-shrink: 0; margin-top: 1px; }
        .alert ul      { margin: 0; padding-left: 18px; }

        /* ─── Theme Cards ───────────────────────────────────────────── */
        .theme-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(165px,1fr)); gap: 16px; margin-bottom: 28px; }
        .theme-card {
            border: 2px solid var(--clay-card-border);
            border-radius: 20px; padding: 20px 16px; cursor: pointer;
            background: var(--clay-card-bg);
            box-shadow: var(--clay-outer-shadow);
            display: flex; flex-direction: column; align-items: center; gap: 12px;
            text-align: center;
            transition: border-color .2s, transform .2s, box-shadow .2s;
        }
        .theme-card:hover { border-color: rgba(74,136,255,.45); transform: translateY(-3px); }
        .theme-card.selected { border-color: var(--accent); background: rgba(74,136,255,.10); box-shadow: 0 8px 24px rgba(74,136,255,.22); }
        .theme-icon {
            width: 52px; height: 52px; border-radius: 14px;
            display: flex; align-items: center; justify-content: center; font-size: 1.5rem;
        }
        .theme-name { font-size: .9rem; font-weight: 700; color: var(--text-primary); }
        .theme-desc { font-size: .76rem; color: var(--text-muted); }

        /* ─── Security Cards ────────────────────────────────────────── */
        .security-status-card {
            display: flex; align-items: center; justify-content: space-between;
            flex-wrap: wrap; gap: 20px;
            padding: 22px; border-radius: 18px;
            background: var(--clay-input-bg);
            border: 1px solid var(--clay-card-border);
            box-shadow: var(--clay-input-shadow);
            margin-bottom: 22px;
        }
        .security-status-left { display: flex; align-items: center; gap: 16px; }
        .shield-icon {
            width: 56px; height: 56px; border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
        }
        .shield-enabled  { background: rgba(16,185,129,.18); color: var(--success); }
        .shield-disabled { background: rgba(245,158,11,.18); color: var(--warning); }
        .security-title { font-size: 1rem; font-weight: 700; color: var(--text-primary); }
        .security-desc  { font-size: .82rem; color: var(--text-muted); margin-top: 4px; }

        .badge-pill {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 4px 12px; border-radius: 50px;
            font-size: .78rem; font-weight: 700;
        }
        .badge-active   { background: rgba(52,211,153,.15); color: var(--success); border: 1px solid rgba(52,211,153,.3); }
        .badge-inactive { background: rgba(251,191,36,.15);  color: var(--warning); border: 1px solid rgba(251,191,36,.3); }

        /* ─── Recovery Codes ────────────────────────────────────────── */
        .recovery-grid {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(170px,1fr));
            gap: 12px; margin-top: 16px;
        }
        .recovery-code {
            font-family: monospace; font-size: 1rem; font-weight: 700;
            letter-spacing: 3px; text-align: center;
            padding: 12px; border-radius: 12px;
            background: var(--clay-card-bg);
            border: 1px dashed var(--clay-card-border);
            color: #38bdf8;
            box-shadow: var(--clay-input-shadow);
        }

        /* ─── QR Setup Panel ────────────────────────────────────────── */
        .qr-panel {
            border: 1px solid rgba(16,185,129,.35);
            background: rgba(16,185,129,.07);
            border-radius: 18px; padding: 24px; margin-bottom: 24px;
        }
        .qr-wrap {
            display: inline-block; background: #fff;
            border-radius: 16px; padding: 16px;
            box-shadow: 0 8px 24px rgba(0,0,0,.12);
            border: 2px solid rgba(0,0,0,.06);
        }

        /* ─── Modal ─────────────────────────────────────────────────── */
        .modal-overlay {
            position: fixed; inset: 0;
            background: rgba(0,0,0,.55); backdrop-filter: blur(4px);
            z-index: 200;
            display: flex; align-items: center; justify-content: center; padding: 20px;
        }
        .modal-box {
            background: var(--clay-card-bg);
            border: 1px solid var(--clay-card-border);
            border-radius: 22px;
            box-shadow: var(--clay-outer-shadow);
            width: 100%; max-width: 460px;
            overflow: hidden;
        }
        .modal-header {
            display: flex; align-items: center; justify-content: space-between;
            padding: 18px 22px;
            border-bottom: 1px solid var(--clay-card-border);
            background: rgba(239,68,68,.08);
        }
        .modal-title { font-size: 1rem; font-weight: 700; color: var(--danger); display: flex; align-items: center; gap: 8px; }
        .modal-close {
            background: none; border: none; cursor: pointer;
            color: var(--text-muted); padding: 4px; border-radius: 8px;
            transition: color .2s, background .2s;
        }
        .modal-close:hover { color: var(--text-primary); background: var(--clay-input-bg); }
        .modal-body { padding: 22px; }
        .modal-footer {
            display: flex; justify-content: flex-end; gap: 10px;
            padding: 16px 22px;
            border-top: 1px solid var(--clay-card-border);
            background: var(--clay-input-bg);
        }

        /* ─── High Contrast Buttons (#2 Fix) ────────────────────────── */
        .btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 8px;
            padding: 10px 20px; border-radius: 14px;
            font-size: .88rem; font-weight: 700; cursor: pointer;
            border: 1px solid transparent; transition: all .2s ease;
            text-decoration: none;
        }
        .btn-danger-outline {
            color: #b91c1c !important;
            border-color: rgba(239,68,68,.4);
            background: rgba(239,68,68,.1);
        }
        .dark-mode .btn-danger-outline { color: #f87171 !important; }
        .btn-danger-outline:hover { background: rgba(239,68,68,.18); }
        .btn-danger {
            background: #dc2626 !important; color: #ffffff !important;
            box-shadow: 0 4px 12px rgba(239,68,68,.3);
        }
        .btn-danger:hover { background: #b91c1c !important; }
        .btn-secondary {
            color: #0f172a !important;
            background: var(--clay-input-bg);
            border-color: var(--clay-card-border);
        }
        .dark-mode .btn-secondary { color: #f1f5f9 !important; }
        .btn-secondary:hover { background: var(--clay-card-bg); }
        .btn-success {
            background: #059669 !important; color: #ffffff !important;
            box-shadow: 0 4px 12px rgba(16,185,129,.3);
        }
        .btn-success:hover { background: #047857 !important; }
        .btn-primary {
            background: var(--accent) !important; color: #ffffff !important;
            box-shadow: 0 4px 12px rgba(74,136,255,.3);
        }
        .btn-primary:hover { background: var(--accent-hover) !important; }

        /* ─── Key grid & Inner Grids Responsiveness ─────────────────── */
        .keys-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px,1fr)); gap: 20px; margin-bottom: 28px; }
        @media (max-width: 640px) {
            .keys-grid { grid-template-columns: 1fr; gap: 14px; }
            .theme-grid { grid-template-columns: repeat(2, 1fr) !important; gap: 10px !important; }
            .security-status-card { flex-direction: column; align-items: flex-start; gap: 14px; padding: 18px 16px; }
            .security-status-card .clay-btn, .security-status-card .btn { width: 100%; justify-content: center; }
            .recovery-grid { grid-template-columns: repeat(2, 1fr); gap: 8px; }
            .qr-panel { padding: 18px 14px; }
            .qr-panel .field-group { flex-direction: column !important; align-items: stretch !important; gap: 10px !important; }
            .qr-panel .clay-btn, .qr-panel .btn { width: 100%; justify-content: center; }
        }
        @media (max-width: 400px) {
            .theme-grid { grid-template-columns: 1fr !important; }
            .recovery-grid { grid-template-columns: 1fr; }
        }

        /* ─── Transitions ───────────────────────────────────────────── */
        [x-cloak] { display: none !important; }
    </style>
</head>

<body
    x-data="{
        darkMode: localStorage.getItem('darkMode') === 'true',
        activeTab: '{{ $activeTab }}',
        showDisableModal: false,
        showLogoutModal: false,
        sidebarOpen: false
    }"
    :class="{ 'dark-mode': darkMode }"
>
    <div class="ambient-bubble bubble-1"></div>
    <div class="ambient-bubble bubble-2"></div>

    {{-- Sidebar Backdrop for Mobile --}}
    <div class="sidebar-backdrop" :class="{ 'open': sidebarOpen }" @click="sidebarOpen = false"></div>

    <div class="page-shell">

        {{-- ─── EXTREME LEFT SIDEBAR ─────────────────────────────────── --}}
        <aside class="settings-sidebar" :class="{ 'open': sidebarOpen }">
            <div class="sidebar-header">
                <a href="{{ route('chat') }}" class="sidebar-brand">
                    @php 
                        $lightLogo = \App\Models\SystemSetting::get('general_logo_light') ?: \App\Models\SystemSetting::get('general_chatbot_logo'); 
                        $darkLogo = \App\Models\SystemSetting::get('general_logo_dark') ?: \App\Models\SystemSetting::get('general_chatbot_logo'); 
                    @endphp
                    @if($lightLogo || $darkLogo)
                        <img :src="darkMode ? '{{ $darkLogo ?: $lightLogo }}' : '{{ $lightLogo ?: $darkLogo }}'" alt="Logo" style="width:42px; height:42px; border-radius:14px; object-fit:contain; flex-shrink:0;">
                    @else
                        <div class="app-logo-badge">
                            {{ substr(\App\Models\SystemSetting::get('general_chatbot_name', 'App'), 0, 1) }}
                        </div>
                    @endif
                    <span class="sidebar-brand-text">{{ \App\Models\SystemSetting::get('general_chatbot_name', 'App') }}</span>
                </a>
                <button type="button" class="clay-btn clay-btn-secondary" @click="sidebarOpen = false" style="padding: 6px; border-radius: 10px; display: none;" :style="window.innerWidth <= 900 ? 'display: inline-flex;' : ''">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- User chip --}}
            <div class="user-chip">
                <div class="user-avatar">{{ substr(Auth::user()->name, 0, 1) }}</div>
                <div style="overflow:hidden;">
                    <div class="user-name">{{ Auth::user()->name }}</div>
                    <div class="user-email">{{ Auth::user()->email }}</div>
                </div>
            </div>

            <div class="nav-section-label">Navigation</div>

            <nav class="settings-nav">
                <button type="button" id="nav-general" class="nav-item" :class="{ active: activeTab === 'general' }" @click="activeTab = 'general'; sidebarOpen = false;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    General &amp; AI
                </button>

                <button type="button" id="nav-theme" class="nav-item" :class="{ active: activeTab === 'theme' }" @click="activeTab = 'theme'; sidebarOpen = false;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>
                    Appearance
                </button>

                <button type="button" id="nav-keys" class="nav-item" :class="{ active: activeTab === 'keys' }" @click="activeTab = 'keys'; sidebarOpen = false;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                    API Keys (BYOK)
                </button>

                <button type="button" id="nav-security" class="nav-item" :class="{ active: activeTab === 'security' }" @click="activeTab = 'security'; sidebarOpen = false;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    Security &amp; 2FA
                </button>

                <button type="button" id="nav-memory" class="nav-item" :class="{ active: activeTab === 'memory' }" @click="activeTab = 'memory'; sidebarOpen = false;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" /></svg>
                    Memory
                </button>
            </nav>

            <hr class="nav-divider">

            {{-- Sign Out with Confirmation Modal Trigger (#3 Fix) --}}
            <button type="button" @click="showLogoutModal = true" class="nav-item" style="color: #dc2626 !important; width:100%;">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                Sign Out
            </button>
        </aside>

        {{-- ─── MAIN SCROLLABLE AREA ─────────────────────────────────── --}}
        <div class="settings-main">

            {{-- Mobile Header with Hamburger --}}
            <div class="mobile-header">
                <button type="button" class="btn btn-secondary" @click="sidebarOpen = true" style="padding: 8px 12px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    Menu
                </button>
                <span style="font-weight: 700; font-size: 1rem; color: var(--text-primary);">Account Settings</span>
                <a href="{{ route('chat') }}" class="btn btn-secondary" style="padding: 8px 12px;">
                    Back
                </a>
            </div>

            {{-- Top Bar for Desktop --}}
            <header class="top-bar">
                <div>
                    <div class="top-bar-title">Account &amp; Settings</div>
                    <div class="top-bar-sub">Manage your AI preferences, appearance, API keys &amp; security.</div>
                </div>
                <a href="{{ route('chat') }}" class="btn btn-secondary" style="padding: 10px 20px; font-size: .88rem;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Back to Chat
                </a>
            </header>

            {{-- ─── GLOBAL ALERTS ────────────────────────────────────── --}}
            @if(session('success'))
                <div class="alert alert-success">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div>{{ session('success') }}</div>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <div>{{ session('error') }}</div>
                </div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <div><ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
                </div>
            @endif

            {{-- MAIN CONTENT PANEL --}}
            <main class="settings-panel">

                {{-- ── TAB 1: GENERAL & AI ──────────────────────────────── --}}
                <div x-show="activeTab === 'general'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform -translate-y-1" x-transition:enter-end="opacity-100 transform translate-y-0">
                    <div class="section-header">
                        <div>
                            <div class="section-title">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                General &amp; AI Preferences
                            </div>
                            <div class="section-desc">Configure default assistant behaviours, models, and personal instructions.</div>
                        </div>
                    </div>

                    <form action="{{ route('settings.update') }}" method="POST">
                        @csrf
                        <input type="hidden" name="theme" value="{{ $settings->theme ?? 'system' }}">

                        <div class="field-group">
                            <label class="field-label" for="default_model">Default AI Model</label>
                            <select id="default_model" name="default_model" class="field-select">
                                @if(!empty($models))
                                    @foreach($models as $model)
                                        <option value="{{ $model['id'] }}" {{ ($settings->default_model ?? '') === $model['id'] ? 'selected' : '' }}>
                                            {{ $model['name'] }}
                                        </option>
                                    @endforeach
                                @else
                                    <option value="mock">Default Model</option>
                                @endif
                            </select>
                            <div class="field-hint">Automatically selected when starting new conversations.</div>
                        </div>

                        <div class="field-group">
                            <label class="field-label" for="system_prompt">Custom System Instructions</label>
                            <textarea id="system_prompt" name="system_prompt" class="field-textarea" placeholder="Example: Always answer concisely, format code blocks cleanly, and prefer Python for examples...">{{ $settings->system_prompt }}</textarea>
                            <div class="field-hint">These instructions guide the AI's tone, formatting, and approach for every response.</div>
                        </div>

                        <div class="form-footer">
                            <button type="submit" id="save-general-btn" class="clay-btn clay-btn-primary" style="padding: 11px 24px; font-size: .9rem;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                                Save Preferences
                            </button>
                        </div>
                    </form>
                </div>

                {{-- ── TAB 2: APPEARANCE ────────────────────────────────── --}}
                <div x-show="activeTab === 'theme'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                    <div class="section-header">
                        <div>
                            <div class="section-title">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>
                                Interface Appearance
                            </div>
                            <div class="section-desc">Choose your preferred lighting theme for the chat workspace.</div>
                        </div>
                    </div>

                    <form action="{{ route('settings.update') }}" method="POST" x-data="{ selectedTheme: '{{ $settings->theme ?? 'system' }}' }">
                        @csrf
                        <input type="hidden" name="default_model" value="{{ $settings->default_model ?? 'mock' }}">
                        <input type="hidden" name="system_prompt" value="{{ $settings->system_prompt ?? '' }}">
                        <input type="hidden" name="theme" :value="selectedTheme">

                        <div class="theme-grid">
                            {{-- Dark --}}
                            <div id="theme-dark" class="theme-card" :class="{ selected: selectedTheme === 'dark' }"
                                 @click="selectedTheme = 'dark'; darkMode = true; localStorage.setItem('darkMode','true')">
                                <div class="theme-icon" style="background:#1e293b; color:#60a5fa; border:1px solid rgba(255,255,255,.1);">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                                </div>
                                <div>
                                    <div class="theme-name">Dark Mode</div>
                                    <div class="theme-desc">Sleek, low-light interface.</div>
                                </div>
                            </div>

                            {{-- Light --}}
                            <div id="theme-light" class="theme-card" :class="{ selected: selectedTheme === 'light' }"
                                 @click="selectedTheme = 'light'; darkMode = false; localStorage.setItem('darkMode','false')">
                                <div class="theme-icon" style="background:#f0f6ff; color:#f59e0b; border:1px solid rgba(0,0,0,.08);">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M12 8a4 4 0 100 8 4 4 0 000-8z"/></svg>
                                </div>
                                <div>
                                    <div class="theme-name">Light Mode</div>
                                    <div class="theme-desc">Clean, bright &amp; readable.</div>
                                </div>
                            </div>

                            {{-- System --}}
                            <div id="theme-system" class="theme-card" :class="{ selected: selectedTheme === 'system' }"
                                 @click="selectedTheme = 'system'; darkMode = window.matchMedia('(prefers-color-scheme: dark)').matches; localStorage.setItem('darkMode', darkMode)">
                                <div class="theme-icon" style="background:linear-gradient(135deg,#1e293b 50%,#f0f6ff 50%); color:#a855f7; border:1px solid rgba(255,255,255,.1);">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                </div>
                                <div>
                                    <div class="theme-name">System Default</div>
                                    <div class="theme-desc">Follows your OS preference.</div>
                                </div>
                            </div>
                        </div>

                        <div class="form-footer">
                            <button type="submit" id="apply-theme-btn" class="clay-btn clay-btn-primary" style="padding: 11px 24px; font-size: .9rem;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Apply Theme
                            </button>
                        </div>
                    </form>
                </div>

                {{-- ── TAB 3: API KEYS ──────────────────────────────────── --}}
                <div x-show="activeTab === 'keys'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                    <div class="section-header">
                        <div>
                            <div class="section-title">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                                API Keys <span style="font-size:.8rem; font-weight:500; color:var(--text-muted);">(Bring Your Own Key)</span>
                            </div>
                            <div class="section-desc">Use your personal API keys to bypass rate limits or access restricted models.</div>
                        </div>
                    </div>

                    <form action="{{ route('settings.keys') }}" method="POST">
                        @csrf
                        @php
                            $providers = [
                                'openai'    => ['label' => 'OpenAI',           'placeholder' => 'sk-...'],
                                'anthropic' => ['label' => 'Anthropic / Claude','placeholder' => 'sk-ant-...'],
                                'gemini'    => ['label' => 'Google Gemini',    'placeholder' => 'AIza...'],
                                'deepseek'  => ['label' => 'DeepSeek',         'placeholder' => 'sk-...'],
                            ];
                        @endphp

                        <div class="keys-grid">
                            @foreach($providers as $slug => $meta)
                                <div>
                                    <div class="field-group" style="margin-bottom:0;">
                                        <label class="field-label" for="key-{{ $slug }}">{{ $meta['label'] }}</label>
                                        <div class="input-group">
                                            <span class="input-group-prefix">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                                            </span>
                                            <input type="password"
                                                   id="key-{{ $slug }}"
                                                   name="keys[{{ $slug }}]"
                                                   class="field-input"
                                                   placeholder="{{ $meta['placeholder'] }}"
                                                   autocomplete="off">
                                        </div>
                                        <div class="field-hint">Leave blank to keep your existing encrypted key.</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="form-footer">
                            <button type="submit" id="save-keys-btn" class="clay-btn clay-btn-primary" style="padding: 11px 24px; font-size: .9rem;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                Save Encrypted Keys
                            </button>
                        </div>
                    </form>
                </div>

                {{-- ── TAB 4: SECURITY & 2FA ────────────────────────────── --}}
                <div x-show="activeTab === 'security'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                    <div class="section-header">
                        <div>
                            <div class="section-title">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                Security &amp; Two-Factor Authentication
                            </div>
                            <div class="section-desc">Protect your account with authenticator apps, email OTP, and backup codes.</div>
                        </div>
                    </div>

                    {{-- 2FA Status Card --}}
                    <div class="security-status-card">
                        <div class="security-status-left">
                            <div class="shield-icon {{ $user->hasTwoFactorEnabled() ? 'shield-enabled' : 'shield-disabled' }}">
                                @if($user->hasTwoFactorEnabled())
                                    <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20.618 5.984A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016zM12 9v2m0 4h.01"/></svg>
                                @endif
                            </div>
                            <div>
                                <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap; margin-bottom:4px;">
                                    <div class="security-title">Two-Factor Authentication</div>
                                    @if($user->hasTwoFactorEnabled())
                                        <span class="badge-pill badge-active">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                            Active
                                        </span>
                                    @else
                                        <span class="badge-pill badge-inactive">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                            Disabled
                                        </span>
                                    @endif
                                </div>
                                <div class="security-desc">
                                    @if($user->hasTwoFactorEnabled())
                                        Protected via <strong style="color:var(--text-primary);">{{ $user->two_factor_type === 'totp' ? 'Authenticator App (TOTP)' : 'Email OTP' }}</strong>
                                        since {{ $user->two_factor_confirmed_at?->format('M d, Y') ?? 'recently' }}.
                                    @else
                                        Add an extra layer of security — a 6-digit code will be required on every sign-in.
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div>
                            @if($user->hasTwoFactorEnabled())
                                <button type="button" id="open-disable-2fa" class="btn btn-danger-outline" @click="showDisableModal = true">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                    Disable 2FA
                                </button>
                            @else
                                <div style="display:flex; flex-wrap:wrap; gap:10px;">
                                    <form method="POST" action="{{ route('profile.security.update') }}" style="margin:0;">
                                        @csrf
                                        <input type="hidden" name="action" value="enable_email">
                                        <button type="submit" id="enable-email-otp-btn" class="btn btn-primary">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                            Enable Email OTP
                                        </button>
                                    </form>
                                    <a href="{{ route('user.settings', ['tab' => 'security', 'setup' => 'totp']) }}" id="setup-totp-btn" class="btn btn-success">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                                        Setup Authenticator App
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Email OTP Verification Panel (#4 Fix - Require OTP code entry to activate Email 2FA) --}}
                    @if(request()->query('setup') === 'email' || session('email_otp_pending_setup'))
                        <div class="qr-panel" style="border-color: rgba(74,136,255,.35); background: rgba(74,136,255,.07);">
                            <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:8px; margin-bottom:14px;">
                                <span style="display:inline-flex; align-items:center; gap:8px; font-size:.82rem; font-weight:700; color:var(--accent); background:rgba(74,136,255,.12); padding:6px 14px; border-radius:50px; border:1px solid rgba(74,136,255,.25);">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    Email OTP Verification Required
                                </span>
                                <a href="{{ route('user.settings', ['tab' => 'security']) }}" style="font-size:.82rem; color:var(--text-muted); text-decoration:none; display:flex; align-items:center; gap:4px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                    Close
                                </a>
                            </div>
                            <h5 style="font-size:.98rem; font-weight:700; color:var(--text-primary); margin-bottom:6px;">Verify Email One-Time Password (OTP)</h5>
                            <p style="font-size:.82rem; color:var(--text-muted); margin-bottom:20px;">We have sent a 6-digit verification code to <strong style="color:var(--text-primary);">{{ Auth::user()->email }}</strong>. Please enter the code below to verify your email and activate Two-Factor Authentication.</p>
                            
                            <form method="POST" action="{{ route('profile.security.email-confirm') }}">
                                @csrf
                                <label class="field-label" style="display:block; margin-bottom:12px;">Enter the 6-digit code sent to your email:</label>
                                <div style="display:flex; gap:12px; align-items:center; flex-wrap:wrap;">
                                    <input type="text" id="email-otp-input" name="otp_code" class="field-input" style="max-width:180px; text-align:center; font-size:1.4rem; font-weight:700; letter-spacing:8px;" placeholder="000000" maxlength="6" required autofocus autocomplete="one-time-code">
                                    <button type="submit" id="confirm-email-otp-btn" class="btn btn-primary">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        Verify &amp; Activate Email OTP
                                    </button>
                                    <a href="{{ route('user.settings', ['tab' => 'security']) }}" class="btn btn-secondary">Cancel</a>
                                </div>
                            </form>
                        </div>
                    @endif

                    {{-- TOTP Setup Panel --}}
                    @if($qrCodeUrl)
                        <div class="qr-panel">
                            <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:8px; margin-bottom:14px;">
                                <span style="display:inline-flex; align-items:center; gap:8px; font-size:.82rem; font-weight:700; color:var(--success); background:rgba(16,185,129,.12); padding:6px 14px; border-radius:50px; border:1px solid rgba(16,185,129,.25);">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                                    Authenticator App Setup
                                </span>
                                <a href="{{ route('user.settings', ['tab' => 'security']) }}" style="font-size:.82rem; color:var(--text-muted); text-decoration:none; display:flex; align-items:center; gap:4px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                    Close
                                </a>
                            </div>
                            <h5 style="font-size:.98rem; font-weight:700; color:var(--text-primary); margin-bottom:6px;">Scan QR Code with your Authenticator App</h5>
                            <p style="font-size:.82rem; color:var(--text-muted); margin-bottom:20px;">Open Google Authenticator, Authy, 1Password, or Microsoft Authenticator and scan the QR code below. Use the manual key if your camera is unavailable.</p>
                            <div style="text-align:center; margin-bottom:20px;">
                                <div class="qr-wrap"><img src="{{ $qrCodeUrl }}" alt="2FA QR Code" width="200" height="200"></div>
                                <div style="margin-top:12px;">
                                    <small style="display:block; font-size:.76rem; font-weight:600; color:var(--text-muted); margin-bottom:6px;">Manual Secret Key:</small>
                                    <code style="font-size:1.05rem; font-weight:700; color:#38bdf8; background:var(--clay-input-bg); padding:8px 18px; border-radius:10px; border:1px solid var(--clay-card-border); display:inline-block; letter-spacing:4px;">{{ implode(' ', str_split($secretKey, 4)) }}</code>
                                </div>
                            </div>
                            <hr style="border:none; border-top:1px solid var(--clay-card-border); margin:18px 0;">
                            <form method="POST" action="{{ route('profile.security.totp-confirm') }}">
                                @csrf
                                <label class="field-label" style="display:block; margin-bottom:12px;">Enter the 6-digit code from your app to confirm activation:</label>
                                <div style="display:flex; gap:12px; align-items:center; flex-wrap:wrap;">
                                    <input type="text" id="totp-code-input" name="totp_code" class="field-input" style="max-width:180px; text-align:center; font-size:1.4rem; font-weight:700; letter-spacing:8px;" placeholder="000000" maxlength="6" required autofocus autocomplete="one-time-code">
                                    <button type="submit" id="confirm-totp-btn" class="btn btn-success">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        Confirm &amp; Activate
                                    </button>
                                    <a href="{{ route('user.settings', ['tab' => 'security']) }}" class="btn btn-secondary">Cancel</a>
                                </div>
                            </form>
                        </div>
                    @endif

                    {{-- Recovery Codes --}}
                    @if($user->hasTwoFactorEnabled())
                        <div style="background:var(--clay-input-bg); border:1px solid var(--clay-card-border); border-radius:18px; padding:22px; box-shadow:var(--clay-input-shadow);">
                            <div style="display:flex; align-items:flex-start; justify-content:space-between; flex-wrap:wrap; gap:14px; margin-bottom:16px;">
                                <div>
                                    <div style="display:flex; align-items:center; gap:8px; font-size:.96rem; font-weight:700; color:var(--text-primary);">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="color:var(--warning);"><path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                                        Emergency Recovery Codes
                                    </div>
                                    <div style="font-size:.78rem; color:var(--text-muted); margin-top:4px;">Store these in a password manager. Each code can only be used once.</div>
                                </div>
                                <form method="POST" action="{{ route('profile.security.update') }}" onsubmit="return confirm('Regenerating will invalidate all existing codes. Continue?');" style="margin:0;">
                                    @csrf
                                    <input type="hidden" name="action" value="regenerate_codes">
                                    <button type="submit" id="regenerate-codes-btn" class="btn btn-secondary" style="font-size:.82rem;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                        Regenerate
                                    </button>
                                </form>
                            </div>

                            @if(!empty($recoveryCodes))
                                <div class="recovery-grid">
                                    @foreach($recoveryCodes as $code)
                                        <div class="recovery-code">{{ $code }}</div>
                                    @endforeach
                                </div>
                                <div style="font-size:.78rem; color:var(--text-muted); margin-top:12px; display:flex; align-items:center; gap:6px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="color:var(--success);"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                    {{ count($recoveryCodes) }} emergency codes available.
                                </div>
                            @else
                                <div style="display:flex; align-items:center; gap:12px; padding:14px; border-radius:14px; background:rgba(245,158,11,.1); border:1px solid rgba(245,158,11,.25); color:var(--warning); font-size:.85rem; margin-top:8px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    No emergency codes generated. Click "Regenerate" above to create a set.
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                {{-- ── TAB 5: MEMORY ────────────────────────────── --}}
                <div x-show="activeTab === 'memory'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                    @include('user.settings.sections.memory')
                </div>

            </main>
        </div>
    </div>

    {{-- ─── DISABLE 2FA MODAL ─────────────────────────────────────────── --}}
    <div class="modal-overlay" x-show="showDisableModal" x-cloak @click.self="showDisableModal = false"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="modal-box" @click.stop x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
            <div class="modal-header">
                <div class="modal-title">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    Disable Two-Factor Authentication?
                </div>
                <button type="button" class="modal-close" @click="showDisableModal = false" aria-label="Close">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form method="POST" action="{{ route('profile.security.update') }}">
                @csrf
                <input type="hidden" name="action" value="disable_2fa">
                <div class="modal-body">
                    <p style="font-size:.88rem; color:var(--text-muted); margin-bottom:0;">Disabling 2FA will remove the extra login protection from your account and invalidate all current emergency recovery codes.</p>
                    @if($user->password)
                        <div style="margin-top:18px;">
                            <label class="field-label" for="disable-2fa-password" style="margin-bottom:8px; display:block;">Current Password</label>
                            <input type="password" id="disable-2fa-password" name="current_password" class="field-input" placeholder="Enter your password to confirm">
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" id="cancel-disable-2fa" class="btn btn-secondary" @click="showDisableModal = false">Cancel</button>
                    <button type="submit" id="confirm-disable-2fa" class="btn btn-danger">Turn Off 2FA</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ─── LOGOUT CONFIRMATION MODAL (#3 Fix) ─────────────────────────── --}}
    <div class="modal-overlay" x-show="showLogoutModal" x-cloak @click.self="showLogoutModal = false"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="modal-box" @click.stop x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
            <div class="modal-header">
                <div class="modal-title">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Confirm Sign Out
                </div>
                <button type="button" class="modal-close" @click="showLogoutModal = false" aria-label="Close">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form action="{{ route('logout') }}" method="POST" style="margin:0;">
                @csrf
                <div class="modal-body">
                    <p style="font-size:.9rem; color:var(--text-primary); margin-bottom:0;">Are you sure you want to sign out of your account on this device?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" @click="showLogoutModal = false">Cancel</button>
                    <button type="submit" class="btn btn-danger" style="background:#dc2626 !important; color:#fff !important;">Yes, Sign Out</button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>
