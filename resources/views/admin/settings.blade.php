<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Settings - XrootAI Admin</title>
    <link rel="stylesheet" href="{{ asset('css/claymorphism.css') }}">
    <script src="{{ asset('js/alpine.min.js') }}" defer></script>
    <style>
        .admin-layout {
            display: grid;
            grid-template-columns: 260px 1fr;
            height: 100vh;
            overflow: hidden;
        }
        .admin-sidebar {
            background: var(--bg-sidebar);
            border-right: 1px solid var(--clay-card-border);
            padding: 24px;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .admin-main {
            padding: 40px;
            overflow-y: auto;
            position: relative;
        }
        .menu-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border-radius: 16px;
            color: var(--text-primary);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        .menu-link:hover, .menu-link.active {
            background: rgba(255, 255, 255, 0.4);
            transform: translateX(4px);
        }
        .dark-mode .menu-link:hover, .dark-mode .menu-link.active {
            background: rgba(255, 255, 255, 0.05);
        }
        .settings-grid {
            display: grid;
            grid-template-columns: 280px 1fr;
            gap: 32px;
            align-items: start;
        }
        .settings-sidebar {
            display: flex;
            flex-direction: column;
            gap: 6px;
            max-height: calc(100vh - 180px);
            overflow-y: auto;
            padding-right: 8px;
        }
        .settings-tab-btn {
            background: transparent;
            border: none;
            outline: none;
            text-align: left;
            padding: 10px 14px;
            border-radius: 12px;
            font-size: 0.9rem;
            font-weight: 500;
            color: var(--text-muted);
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .settings-tab-btn:hover {
            color: var(--text-primary);
            background: rgba(0, 0, 0, 0.03);
        }
        .dark-mode .settings-tab-btn:hover {
            background: rgba(255, 255, 255, 0.03);
        }
        .settings-tab-btn.active {
            color: white;
            background: var(--accent-blue);
            box-shadow: var(--accent-blue-shadow);
        }
        .glow-1 {
            position: absolute;
            top: 10%;
            left: 20%;
            width: 400px;
            height: 400px;
            border-radius: 50%;
            background: rgba(126, 182, 255, 0.25);
            filter: blur(120px);
            z-index: -1;
            pointer-events: none;
        }
        .setting-row {
            display: grid;
            grid-template-columns: 1fr;
            gap: 8px;
            margin-bottom: 24px;
        }
        @media(min-width: 768px) {
            .setting-row-split {
                grid-template-columns: 1fr 1fr;
                gap: 20px;
            }
        }
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 52px;
            height: 28px;
        }
        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0; left: 0; right: 0; bottom: 0;
            background-color: var(--clay-input-bg);
            box-shadow: var(--clay-input-shadow);
            border-radius: 34px;
            transition: .4s;
        }
        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 20px;
            width: 20px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            border-radius: 50%;
            transition: .4s;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        input:checked + .toggle-slider {
            background: var(--accent-blue);
        }
        input:checked + .toggle-slider:before {
            transform: translateX(24px);
        }
        .checkbox-container {
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            padding: 10px 0;
        }

        @media (max-width: 768px) {
            .admin-layout {
                grid-template-columns: 1fr;
                height: auto;
                overflow: visible;
            }
            .admin-sidebar {
                padding: 12px 16px;
                flex-direction: row;
                justify-content: space-between;
                align-items: center;
                border-right: none;
                border-bottom: 1px solid var(--clay-card-border);
                position: sticky;
                top: 0;
                background: var(--bg-sidebar);
                z-index: 100;
                height: auto;
                gap: 10px;
            }
            .admin-sidebar .app-brand {
                margin-bottom: 0 !important;
            }
            .admin-sidebar nav {
                flex-direction: row !important;
                gap: 8px !important;
            }
            .admin-sidebar nav a span {
                display: none;
            }
            .admin-sidebar div[style*="margin-top: auto"] {
                margin-top: 0 !important;
                gap: 8px;
            }
            .admin-main {
                padding: 16px;
                height: auto;
                overflow: visible;
            }
            .settings-grid {
                grid-template-columns: 1fr;
                gap: 16px;
            }
            .settings-sidebar {
                flex-direction: row;
                overflow-x: auto;
                overflow-y: hidden;
                max-height: none;
                white-space: nowrap;
                padding: 10px;
                border-radius: 18px;
                gap: 8px;
                scrollbar-width: none;
            }
            .settings-sidebar::-webkit-scrollbar {
                display: none;
            }
            .settings-tab-btn {
                white-space: nowrap;
                flex-shrink: 0;
            }
        }
    </style>
</head>
<body x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" :class="{ 'dark-mode': darkMode }">
    <div class="glow-1"></div>
    <div class="admin-layout">
        
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <a href="{{ route('chat') }}" class="app-brand" style="margin-bottom: 20px;">
                <div class="app-brand-icon">X</div>
                <span>XrootAI Admin</span>
            </a>

            <nav style="display: flex; flex-direction: column; gap: 8px;">
                <a href="{{ route('admin.dashboard') }}" class="menu-link">
                    <svg style="width:20px; height:20px; opacity:0.85;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 3.055A9.003 9.003 0 1020.945 13H11V3.055z"/><path stroke-linecap="round" stroke-linejoin="round" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/></svg>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('admin.users') }}" class="menu-link">
                    <svg style="width:20px; height:20px; opacity:0.85;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span>Users List</span>
                </a>
                <a href="{{ route('admin.settings') }}" class="menu-link active">
                    <svg style="width:20px; height:20px; opacity:0.85;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span>System Settings</span>
                </a>
                <a href="{{ route('chat') }}" class="menu-link">
                    <svg style="width:20px; height:20px; opacity:0.85;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    <span>Go to Chat</span>
                </a>
            </nav>

            <div style="margin-top: auto; display: flex; align-items: center; justify-content: space-between;">
                <button @click="darkMode = !darkMode; localStorage.setItem('darkMode', darkMode)" class="clay-btn" style="border-radius: 50%; width: 40px; height: 40px; padding:0; display:flex; align-items:center; justify-content:center;">
                    <span x-show="!darkMode">🌙</span>
                    <span x-show="darkMode">☀️</span>
                </button>

                <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" class="clay-btn clay-btn-danger" style="border-radius: 12px; height: 40px; padding: 0 16px;">
                        Logout
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="admin-main" x-data="{ activeTab: 'general' }">
            <h1 style="font-weight: 800; font-size: 2.2rem; margin-bottom: 8px;">System Settings</h1>
            <p style="color: var(--text-muted); margin-bottom: 40px;">Configure the dynamic behaviors, security guidelines, UI elements, and API options of XrootAI.</p>

            @if(session('success'))
                <div class="clay-card" style="padding: 16px 24px; background: rgba(86, 171, 47, 0.15); border: 1px solid rgba(86, 171, 47, 0.3); border-radius: 16px; color: #56ab2f; margin-bottom: 24px; font-weight: 600;">
                    {{ session('success') }}
                </div>
            @endif

            <div class="settings-grid">
                
                <!-- Settings navigation sidebar -->
                <div class="clay-card settings-sidebar">
                    <button @click="activeTab = 'general'" :class="{ 'active': activeTab === 'general' }" class="settings-tab-btn">
                        <svg style="width:16px; height:16px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span>General Settings</span>
                    </button>
                    <button @click="activeTab = 'plans'" :class="{ 'active': activeTab === 'plans' }" class="settings-tab-btn">
                        <svg style="width:16px; height:16px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <span>User Plans & Limits</span>
                    </button>
                    <button @click="activeTab = 'behavior'" :class="{ 'active': activeTab === 'behavior' }" class="settings-tab-btn">
                        <svg style="width:16px; height:16px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 113.536 0V21h2v-2.7a5.002 5.002 0 01-3.536 0zM12 7a5 5 0 010 10V7z"/></svg>
                        <span>AI Behavior</span>
                    </button>
                    <button @click="activeTab = 'model'" :class="{ 'active': activeTab === 'model' }" class="settings-tab-btn">
                        <svg style="width:16px; height:16px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/></svg>
                        <span>AI Model Config</span>
                    </button>
                    <button @click="activeTab = 'prompt'" :class="{ 'active': activeTab === 'prompt' }" class="settings-tab-btn">
                        <svg style="width:16px; height:16px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                        <span>System Prompt</span>
                    </button>
                    <button @click="activeTab = 'kb'" :class="{ 'active': activeTab === 'kb' }" class="settings-tab-btn">
                        <svg style="width:16px; height:16px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        <span>Knowledge Base</span>
                    </button>
                    <button @click="activeTab = 'conv'" :class="{ 'active': activeTab === 'conv' }" class="settings-tab-btn">
                        <svg style="width:16px; height:16px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                        <span>Conversation Settings</span>
                    </button>
                    <button @click="activeTab = 'handoff'" :class="{ 'active': activeTab === 'handoff' }" class="settings-tab-btn">
                        <svg style="width:16px; height:16px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                        <span>Human Handoff</span>
                    </button>
                    <button @click="activeTab = 'ux'" :class="{ 'active': activeTab === 'ux' }" class="settings-tab-btn">
                        <svg style="width:16px; height:16px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                        <span>User Experience</span>
                    </button>
                    <button @click="activeTab = 'lang'" :class="{ 'active': activeTab === 'lang' }" class="settings-tab-btn">
                        <svg style="width:16px; height:16px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                        <span>Language Settings</span>
                    </button>
                    <button @click="activeTab = 'notif'" :class="{ 'active': activeTab === 'notif' }" class="settings-tab-btn">
                        <svg style="width:16px; height:16px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        <span>Alerts & Notifications</span>
                    </button>
                    <button @click="activeTab = 'security'" :class="{ 'active': activeTab === 'security' }" class="settings-tab-btn">
                        <svg style="width:16px; height:16px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        <span>Security & Moderation</span>
                    </button>
                    <button @click="activeTab = 'roles'" :class="{ 'active': activeTab === 'roles' }" class="settings-tab-btn">
                        <svg style="width:16px; height:16px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <span>Roles & Permissions</span>
                    </button>
                    <button @click="activeTab = 'privacy'" :class="{ 'active': activeTab === 'privacy' }" class="settings-tab-btn">
                        <svg style="width:16px; height:16px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <span>Data & Privacy</span>
                    </button>
                    <button @click="activeTab = 'integrations'" :class="{ 'active': activeTab === 'integrations' }" class="settings-tab-btn">
                        <svg style="width:16px; height:16px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                        <span>Integrations</span>
                    </button>
                    <button @click="activeTab = 'billing'" :class="{ 'active': activeTab === 'billing' }" class="settings-tab-btn">
                        <svg style="width:16px; height:16px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        <span>Usage & Billing</span>
                    </button>
                    <button @click="activeTab = 'analytics'" :class="{ 'active': activeTab === 'analytics' }" class="settings-tab-btn">
                        <svg style="width:16px; height:16px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 12l3-3 3 3 4-4M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        <span>Analytics Config</span>
                    </button>
                    <button @click="activeTab = 'moderation'" :class="{ 'active': activeTab === 'moderation' }" class="settings-tab-btn">
                        <svg style="width:16px; height:16px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span>Content Moderation</span>
                    </button>
                    <button @click="activeTab = 'logging'" :class="{ 'active': activeTab === 'logging' }" class="settings-tab-btn">
                        <svg style="width:16px; height:16px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Logs & Monitoring</span>
                    </button>
                    <button @click="activeTab = 'backup'" :class="{ 'active': activeTab === 'backup' }" class="settings-tab-btn">
                        <svg style="width:16px; height:16px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                        <span>Backup & Recovery</span>
                    </button>
                    <button @click="activeTab = 'developer'" :class="{ 'active': activeTab === 'developer' }" class="settings-tab-btn">
                        <svg style="width:16px; height:16px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                        <span>Developer Settings</span>
                    </button>
                    <button @click="activeTab = 'branding'" :class="{ 'active': activeTab === 'branding' }" class="settings-tab-btn">
                        <svg style="width:16px; height:16px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span>Appearance & Branding</span>
                    </button>
                    <button @click="activeTab = 'toggle'" :class="{ 'active': activeTab === 'toggle' }" class="settings-tab-btn">
                        <svg style="width:16px; height:16px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                        <span>Feature Toggles</span>
                    </button>
                </div>

                <!-- Settings Forms Area -->
                <div class="clay-card" style="padding: 32px; border-radius: 28px; min-height: 500px;">
                    
                    <!-- 1. GENERAL SETTINGS -->
                    <div x-show="activeTab === 'general'">
                        <form action="{{ route('admin.settings.update') }}" method="POST">
                            @csrf
                            <input type="hidden" name="_group" value="general">
                            <h3 style="font-size: 1.4rem; font-weight: 700; margin-bottom: 24px;">General Settings</h3>
                            
                            <div class="setting-row">
                                <label class="clay-input-label">Chatbot Name</label>
                                <input type="text" name="general_chatbot_name" value="{{ $settings['general_chatbot_name'] }}" class="clay-inset">
                            </div>
                            <div class="setting-row">
                                <label class="clay-input-label">Description</label>
                                <input type="text" name="general_chatbot_description" value="{{ $settings['general_chatbot_description'] }}" class="clay-inset">
                            </div>
                            <div class="setting-row setting-row-split" style="display: grid;">
                                <div>
                                    <label class="clay-input-label">Default Language</label>
                                    <select name="general_default_language" class="clay-inset" style="width:100%;">
                                        <option value="en" {{ $settings['general_default_language'] === 'en' ? 'selected' : '' }}>English (en)</option>
                                        <option value="es" {{ $settings['general_default_language'] === 'es' ? 'selected' : '' }}>Spanish (es)</option>
                                        <option value="fr" {{ $settings['general_default_language'] === 'fr' ? 'selected' : '' }}>French (fr)</option>
                                        <option value="de" {{ $settings['general_default_language'] === 'de' ? 'selected' : '' }}>German (de)</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="clay-input-label">Time Zone</label>
                                    <input type="text" name="general_timezone" value="{{ $settings['general_timezone'] }}" class="clay-inset">
                                </div>
                            </div>
                            <div class="setting-row setting-row-split" style="display: grid; margin-top:20px;">
                                <div>
                                    <label class="clay-input-label">Date Format</label>
                                    <input type="text" name="general_date_format" value="{{ $settings['general_date_format'] }}" class="clay-inset">
                                </div>
                                <div>
                                    <label class="clay-input-label">Time Format</label>
                                    <input type="text" name="general_time_format" value="{{ $settings['general_time_format'] }}" class="clay-inset">
                                </div>
                            </div>
                            <div class="setting-row" style="margin-top:20px;">
                                <label class="clay-input-label">Welcome Message</label>
                                <textarea name="general_welcome_message" class="clay-inset" rows="3" style="width:100%; border:none; resize:none;">{{ $settings['general_welcome_message'] }}</textarea>
                            </div>
                            <div class="setting-row">
                                <label class="clay-input-label">Maintenance Message</label>
                                <textarea name="general_maintenance_message" class="clay-inset" rows="3" style="width:100%; border:none; resize:none;">{{ $settings['general_maintenance_message'] }}</textarea>
                            </div>
                            <div style="display:flex; flex-direction:column; gap:12px; margin: 24px 0;">
                                <label class="checkbox-container">
                                    <span class="toggle-switch">
                                        <input type="checkbox" name="general_enable_chatbot" {{ $settings['general_enable_chatbot'] ? 'checked' : '' }}>
                                        <span class="toggle-slider"></span>
                                    </span>
                                    <span style="font-weight:600; font-size:0.95rem;">Enable Chatbot</span>
                                </label>
                                <label class="checkbox-container">
                                    <span class="toggle-switch">
                                        <input type="checkbox" name="general_maintenance_mode" {{ $settings['general_maintenance_mode'] ? 'checked' : '' }}>
                                        <span class="toggle-slider"></span>
                                    </span>
                                    <span style="font-weight:600; font-size:0.95rem; color:#dc3545;">Enable Maintenance Mode</span>
                                </label>
                            </div>
                            
                            <button type="submit" class="clay-btn clay-btn-primary">Save General Settings</button>
                        </form>
                    </div>

                    <!-- 2. USER PLANS & LIMITS -->
                    <div x-show="activeTab === 'plans'">
                        <form action="{{ route('admin.settings.update') }}" method="POST">
                            @csrf
                            <input type="hidden" name="_group" value="plans">
                            <h3 style="font-size: 1.4rem; font-weight: 700; margin-bottom: 24px;">User Plans & Limits</h3>
                            
                            <h4 style="margin:20px 0 10px 0; font-weight:700;">Guest Limits</h4>
                            <div class="setting-row setting-row-split" style="display: grid;">
                                <div>
                                    <label class="clay-input-label">Daily Chat Limit</label>
                                    <input type="number" name="plans_guest_chat_limit" value="{{ $settings['plans_guest_chat_limit'] }}" class="clay-inset" style="width:100%;">
                                </div>
                                <div>
                                    <label class="clay-input-label">Messages per Session</label>
                                    <input type="number" name="plans_guest_messages_per_session" value="{{ $settings['plans_guest_messages_per_session'] }}" class="clay-inset" style="width:100%;">
                                </div>
                            </div>
                            <div style="display:flex; flex-direction:column; gap:12px; margin: 16px 0;">
                                <label class="checkbox-container">
                                    <span class="toggle-switch">
                                        <input type="checkbox" name="plans_guest_file_upload" {{ $settings['plans_guest_file_upload'] ? 'checked' : '' }}>
                                        <span class="toggle-slider"></span>
                                    </span>
                                    <span style="font-weight:600; font-size:0.95rem;">Guest File Upload Permission</span>
                                </label>
                                <label class="checkbox-container">
                                    <span class="toggle-switch">
                                        <input type="checkbox" name="plans_guest_image_gen" {{ $settings['plans_guest_image_gen'] ? 'checked' : '' }}>
                                        <span class="toggle-slider"></span>
                                    </span>
                                    <span style="font-weight:600; font-size:0.95rem;">Guest Image Generation Permission</span>
                                </label>
                            </div>

                            <h4 style="margin:30px 0 10px 0; font-weight:700; border-top:1px solid var(--clay-card-border); padding-top:20px;">Free User Limits</h4>
                            <div class="setting-row setting-row-split" style="display: grid;">
                                <div>
                                    <label class="clay-input-label">Daily Message Limit</label>
                                    <input type="number" name="plans_free_message_limit" value="{{ $settings['plans_free_message_limit'] }}" class="clay-inset" style="width:100%;">
                                </div>
                                <div>
                                    <label class="clay-input-label">Monthly Token Limit</label>
                                    <input type="number" name="plans_free_token_limit" value="{{ $settings['plans_free_token_limit'] }}" class="clay-inset" style="width:100%;">
                                </div>
                            </div>
                            <div class="setting-row" style="margin-top:16px;">
                                <label class="clay-input-label">Maximum Conversation History</label>
                                <input type="number" name="plans_free_max_history" value="{{ $settings['plans_free_max_history'] }}" class="clay-inset">
                            </div>

                            <h4 style="margin:30px 0 10px 0; font-weight:700; border-top:1px solid var(--clay-card-border); padding-top:20px;">Pro User Limits</h4>
                            <div class="setting-row setting-row-split" style="display: grid;">
                                <div>
                                    <label class="clay-input-label">Daily Chats (-1 for Unlimited)</label>
                                    <input type="number" name="plans_pro_chat_limit" value="{{ $settings['plans_pro_chat_limit'] }}" class="clay-inset" style="width:100%;">
                                </div>
                                <div>
                                    <label class="clay-input-label">Larger Context Window (tokens)</label>
                                    <input type="number" name="plans_pro_context_window" value="{{ $settings['plans_pro_context_window'] }}" class="clay-inset" style="width:100%;">
                                </div>
                            </div>
                            <div style="display:flex; flex-direction:column; gap:12px; margin: 16px 0;">
                                <label class="checkbox-container">
                                    <span class="toggle-switch">
                                        <input type="checkbox" name="plans_pro_priority_processing" {{ $settings['plans_pro_priority_processing'] ? 'checked' : '' }}>
                                        <span class="toggle-slider"></span>
                                    </span>
                                    <span style="font-weight:600; font-size:0.95rem;">Priority Processing</span>
                                </label>
                                <label class="checkbox-container">
                                    <span class="toggle-switch">
                                        <input type="checkbox" name="plans_pro_vision_support" {{ $settings['plans_pro_vision_support'] ? 'checked' : '' }}>
                                        <span class="toggle-slider"></span>
                                    </span>
                                    <span style="font-weight:600; font-size:0.95rem;">Vision Support</span>
                                </label>
                                <label class="checkbox-container">
                                    <span class="toggle-switch">
                                        <input type="checkbox" name="plans_pro_file_upload" {{ $settings['plans_pro_file_upload'] ? 'checked' : '' }}>
                                        <span class="toggle-slider"></span>
                                    </span>
                                    <span style="font-weight:600; font-size:0.95rem;">File Upload Permission</span>
                                </label>
                            </div>

                            <button type="submit" class="clay-btn clay-btn-primary">Save Plans & Limits</button>
                        </form>
                    </div>

                    <!-- 3. AI BEHAVIOR -->
                    <div x-show="activeTab === 'behavior'">
                        <form action="{{ route('admin.settings.update') }}" method="POST">
                            @csrf
                            <input type="hidden" name="_group" value="behavior">
                            <h3 style="font-size: 1.4rem; font-weight: 700; margin-bottom: 24px;">AI Behavior</h3>
                            
                            <div class="setting-row setting-row-split" style="display: grid;">
                                <div>
                                    <label class="clay-input-label">Personality</label>
                                    <select name="behavior_personality" class="clay-inset" style="width:100%;">
                                        <option value="Friendly" {{ $settings['behavior_personality'] === 'Friendly' ? 'selected' : '' }}>Friendly</option>
                                        <option value="Professional" {{ $settings['behavior_personality'] === 'Professional' ? 'selected' : '' }}>Professional</option>
                                        <option value="Formal" {{ $settings['behavior_personality'] === 'Formal' ? 'selected' : '' }}>Formal</option>
                                        <option value="Casual" {{ $settings['behavior_personality'] === 'Casual' ? 'selected' : '' }}>Casual</option>
                                        <option value="Custom" {{ $settings['behavior_personality'] === 'Custom' ? 'selected' : '' }}>Custom</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="clay-input-label">Response Style</label>
                                    <select name="behavior_response_style" class="clay-inset" style="width:100%;">
                                        <option value="Short" {{ $settings['behavior_response_style'] === 'Short' ? 'selected' : '' }}>Short</option>
                                        <option value="Detailed" {{ $settings['behavior_response_style'] === 'Detailed' ? 'selected' : '' }}>Detailed</option>
                                        <option value="Bullet Points" {{ $settings['behavior_response_style'] === 'Bullet Points' ? 'selected' : '' }}>Bullet Points</option>
                                        <option value="Step-by-step" {{ $settings['behavior_response_style'] === 'Step-by-step' ? 'selected' : '' }}>Step-by-step</option>
                                    </select>
                                </div>
                            </div>
                            <div class="setting-row setting-row-split" style="display: grid; margin-top:20px;">
                                <div>
                                    <label class="clay-input-label">Creativity</label>
                                    <select name="behavior_creativity" class="clay-inset" style="width:100%;">
                                        <option value="Low" {{ $settings['behavior_creativity'] === 'Low' ? 'selected' : '' }}>Low</option>
                                        <option value="Medium" {{ $settings['behavior_creativity'] === 'Medium' ? 'selected' : '' }}>Medium</option>
                                        <option value="High" {{ $settings['behavior_creativity'] === 'High' ? 'selected' : '' }}>High</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="clay-input-label">Greeting Behavior</label>
                                    <input type="text" name="behavior_greeting_behavior" value="{{ $settings['behavior_greeting_behavior'] }}" class="clay-inset" style="width:100%;">
                                </div>
                            </div>
                            <div class="setting-row setting-row-split" style="display: grid; margin-top:20px;">
                                <div>
                                    <label class="clay-input-label">Temperature (0.0 - 2.0)</label>
                                    <input type="number" step="0.1" name="behavior_temperature" value="{{ $settings['behavior_temperature'] }}" class="clay-inset" style="width:100%;">
                                </div>
                                <div>
                                    <label class="clay-input-label">Top P (0.0 - 1.0)</label>
                                    <input type="number" step="0.1" name="behavior_top_p" value="{{ $settings['behavior_top_p'] }}" class="clay-inset" style="width:100%;">
                                </div>
                            </div>
                            <div class="setting-row setting-row-split" style="display: grid; margin-top:20px;">
                                <div>
                                    <label class="clay-input-label">Frequency Penalty</label>
                                    <input type="number" step="0.1" name="behavior_frequency_penalty" value="{{ $settings['behavior_frequency_penalty'] }}" class="clay-inset" style="width:100%;">
                                </div>
                                <div>
                                    <label class="clay-input-label">Presence Penalty</label>
                                    <input type="number" step="0.1" name="behavior_presence_penalty" value="{{ $settings['behavior_presence_penalty'] }}" class="clay-inset" style="width:100%;">
                                </div>
                            </div>
                            <div class="setting-row setting-row-split" style="display: grid; margin-top:20px;">
                                <div>
                                    <label class="clay-input-label">Response Length Limit (tokens)</label>
                                    <input type="number" name="behavior_max_response_length" value="{{ $settings['behavior_max_response_length'] }}" class="clay-inset" style="width:100%;">
                                </div>
                                <div>
                                    <label class="clay-input-label">Typing Delay (ms)</label>
                                    <input type="number" name="behavior_typing_delay" value="{{ $settings['behavior_typing_delay'] }}" class="clay-inset" style="width:100%;">
                                </div>
                            </div>

                            <h4 style="margin:24px 0 10px 0; font-weight:700;">Conversation Memory</h4>
                            <div style="display:flex; flex-direction:column; gap:12px; margin-bottom: 24px;">
                                <label class="checkbox-container">
                                    <span class="toggle-switch">
                                        <input type="checkbox" name="behavior_conversation_memory" {{ $settings['behavior_conversation_memory'] ? 'checked' : '' }}>
                                        <span class="toggle-slider"></span>
                                    </span>
                                    <span style="font-weight:600; font-size:0.95rem;">Enable Memory</span>
                                </label>
                                <label class="checkbox-container">
                                    <span class="toggle-switch">
                                        <input type="checkbox" name="behavior_remember_username" {{ $settings['behavior_remember_username'] ? 'checked' : '' }}>
                                        <span class="toggle-slider"></span>
                                    </span>
                                    <span style="font-weight:600; font-size:0.95rem;">Remember User Name</span>
                                </label>
                                <label class="checkbox-container">
                                    <span class="toggle-switch">
                                        <input type="checkbox" name="behavior_remember_preferences" {{ $settings['behavior_remember_preferences'] ? 'checked' : '' }}>
                                        <span class="toggle-slider"></span>
                                    </span>
                                    <span style="font-weight:600; font-size:0.95rem;">Remember User Preferences</span>
                                </label>
                                <label class="checkbox-container">
                                    <span class="toggle-switch">
                                        <input type="checkbox" name="behavior_remember_topics" {{ $settings['behavior_remember_topics'] ? 'checked' : '' }}>
                                        <span class="toggle-slider"></span>
                                    </span>
                                    <span style="font-weight:600; font-size:0.95rem;">Remember Previous Topics</span>
                                </label>
                            </div>
                            <div class="setting-row">
                                <label class="clay-input-label">Custom Memory Rules / Guidelines</label>
                                <textarea name="behavior_custom_memory_rules" class="clay-inset" rows="3" style="width:100%; border:none; resize:none;">{{ $settings['behavior_custom_memory_rules'] }}</textarea>
                            </div>

                            <button type="submit" class="clay-btn clay-btn-primary">Save Behavior Settings</button>
                        </form>
                    </div>

                    <!-- 4. AI MODEL CONFIGURATION -->
                    <div x-show="activeTab === 'model'">
                        <form action="{{ route('admin.settings.update') }}" method="POST">
                            @csrf
                            <input type="hidden" name="_group" value="model">
                            <h3 style="font-size: 1.4rem; font-weight: 700; margin-bottom: 24px;">AI Model Configuration</h3>
                            
                            <div class="setting-row setting-row-split" style="display: grid;">
                                <div>
                                    <label class="clay-input-label">Default Model</label>
                                    <select name="model_default" class="clay-inset" style="width:100%;">
                                        @foreach($settings['model_available'] as $model)
                                            <option value="{{ $model }}" {{ $settings['model_default'] === $model ? 'selected' : '' }}>{{ $model }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="clay-input-label">Backup Model</label>
                                    <select name="model_backup" class="clay-inset" style="width:100%;">
                                        @foreach($settings['model_available'] as $model)
                                            <option value="{{ $model }}" {{ $settings['model_backup'] === $model ? 'selected' : '' }}>{{ $model }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="setting-row" style="margin-top:20px;">
                                <label class="clay-input-label">Model Fallback Logic Description</label>
                                <input type="text" name="model_fallback_logic" value="{{ $settings['model_fallback_logic'] }}" class="clay-inset">
                            </div>
                            <div class="setting-row">
                                <label class="clay-input-label">Available Models (comma separated)</label>
                                <input type="text" name="model_available" value="{{ is_array($settings['model_available']) ? implode(', ', $settings['model_available']) : $settings['model_available'] }}" class="clay-inset">
                            </div>
                            <div class="setting-row setting-row-split" style="display: grid;">
                                <div>
                                    <label class="clay-input-label">Max Tokens per request</label>
                                    <input type="number" name="model_max_tokens" value="{{ $settings['model_max_tokens'] }}" class="clay-inset" style="width:100%;">
                                </div>
                                <div>
                                    <label class="clay-input-label">Context Window (max history)</label>
                                    <input type="number" name="model_context_window" value="{{ $settings['model_context_window'] }}" class="clay-inset" style="width:100%;">
                                </div>
                            </div>
                            
                            <h4 style="margin:24px 0 10px 0; font-weight:700;">Parameters & Modalities</h4>
                            <div style="display:flex; flex-direction:column; gap:12px; margin-bottom: 24px;">
                                <label class="checkbox-container">
                                    <span class="toggle-switch">
                                        <input type="checkbox" name="model_vision_toggle" {{ $settings['model_vision_toggle'] ? 'checked' : '' }}>
                                        <span class="toggle-slider"></span>
                                    </span>
                                    <span style="font-weight:600; font-size:0.95rem;">Enable Vision Support</span>
                                </label>
                                <label class="checkbox-container">
                                    <span class="toggle-switch">
                                        <input type="checkbox" name="model_image_gen_toggle" {{ $settings['model_image_gen_toggle'] ? 'checked' : '' }}>
                                        <span class="toggle-slider"></span>
                                    </span>
                                    <span style="font-weight:600; font-size:0.95rem;">Enable Image Generation</span>
                                </label>
                                <label class="checkbox-container">
                                    <span class="toggle-switch">
                                        <input type="checkbox" name="model_streaming_responses" {{ $settings['model_streaming_responses'] ? 'checked' : '' }}>
                                        <span class="toggle-slider"></span>
                                    </span>
                                    <span style="font-weight:600; font-size:0.95rem;">Stream Responses</span>
                                </label>
                                <label class="checkbox-container">
                                    <span class="toggle-switch">
                                        <input type="checkbox" name="model_json_mode" {{ $settings['model_json_mode'] ? 'checked' : '' }}>
                                        <span class="toggle-slider"></span>
                                    </span>
                                    <span style="font-weight:600; font-size:0.95rem;">Enforce JSON Mode</span>
                                </label>
                            <h4 style="margin:24px 0 10px 0; font-weight:700;">Global Provider API Keys</h4>
                            <div class="setting-row" style="margin-bottom:16px;">
                                <label class="clay-input-label">OpenAI API Key</label>
                                <input type="password" name="model_openai_key" value="{{ $settings['model_openai_key'] }}" placeholder="sk-..." class="clay-inset" style="width:100%;">
                            </div>
                            <div class="setting-row" style="margin-bottom:16px;">
                                <label class="clay-input-label">Gemini API Key</label>
                                <input type="password" name="model_gemini_key" value="{{ $settings['model_gemini_key'] }}" placeholder="AIzaSy..." class="clay-inset" style="width:100%;">
                            </div>
                            <div class="setting-row" style="margin-bottom:16px;">
                                <label class="clay-input-label">Claude API Key</label>
                                <input type="password" name="model_claude_key" value="{{ $settings['model_claude_key'] }}" placeholder="sk-ant-..." class="clay-inset" style="width:100%;">
                            </div>
                            <div class="setting-row" style="margin-bottom:16px;">
                                <label class="clay-input-label">DeepSeek API Key</label>
                                <input type="password" name="model_deepseek_key" value="{{ $settings['model_deepseek_key'] }}" placeholder="sk-..." class="clay-inset" style="width:100%;">
                            </div>
                            <div class="setting-row" style="margin-bottom:24px;">
                                <label class="clay-input-label">Ollama Endpoint URL</label>
                                <input type="text" name="model_ollama_url" value="{{ $settings['model_ollama_url'] }}" placeholder="http://localhost:11434" class="clay-inset" style="width:100%;">
                            </div>

                            <button type="submit" class="clay-btn clay-btn-primary">Save Model Configuration</button>
                        </form>
                    </div>

                    <!-- 5. SYSTEM PROMPT -->
                    <div x-show="activeTab === 'prompt'">
                        <form action="{{ route('admin.settings.update') }}" method="POST">
                            @csrf
                            <input type="hidden" name="_group" value="prompt">
                            <h3 style="font-size: 1.4rem; font-weight: 700; margin-bottom: 24px;">System Prompt Editor</h3>
                            
                            <div class="setting-row">
                                <label class="clay-input-label">Role Definition</label>
                                <textarea name="prompt_role_definition" class="clay-inset" rows="3" style="width:100%; border:none; resize:none;">{{ $settings['prompt_role_definition'] }}</textarea>
                            </div>
                            <div class="setting-row">
                                <label class="clay-input-label">Business Rules</label>
                                <textarea name="prompt_business_rules" class="clay-inset" rows="3" style="width:100%; border:none; resize:none;">{{ $settings['prompt_business_rules'] }}</textarea>
                            </div>
                            <div class="setting-row">
                                <label class="clay-input-label">Restricted Behaviors</label>
                                <textarea name="prompt_restricted_behaviors" class="clay-inset" rows="3" style="width:100%; border:none; resize:none;">{{ $settings['prompt_restricted_behaviors'] }}</textarea>
                            </div>
                            <div class="setting-row">
                                <label class="clay-input-label">Allowed Behaviors</label>
                                <textarea name="prompt_allowed_behaviors" class="clay-inset" rows="3" style="width:100%; border:none; resize:none;">{{ $settings['prompt_allowed_behaviors'] }}</textarea>
                            </div>
                            <div class="setting-row">
                                <label class="clay-input-label">Brand Voice Guidelines</label>
                                <input type="text" name="prompt_brand_voice" value="{{ $settings['prompt_brand_voice'] }}" class="clay-inset">
                            </div>
                            <div class="setting-row">
                                <label class="clay-input-label">Variables (comma separated)</label>
                                <input type="text" name="prompt_variables" value="{{ $settings['prompt_variables'] }}" class="clay-inset">
                            </div>
                            <div class="setting-row">
                                <label class="clay-input-label">Compiled System Prompt (Active)</label>
                                <textarea name="prompt_default" class="clay-inset" rows="5" style="width:100%; border:none; resize:none; font-family: monospace; font-size: 0.85rem;">{{ $settings['prompt_default'] }}</textarea>
                            </div>

                            <button type="submit" class="clay-btn clay-btn-primary">Save & Apply Prompt</button>
                        </form>
                    </div>

                    <!-- 6. KNOWLEDGE BASE -->
                    <div x-show="activeTab === 'kb'">
                        <form action="{{ route('admin.settings.update') }}" method="POST">
                            @csrf
                            <input type="hidden" name="_group" value="kb">
                            <h3 style="font-size: 1.4rem; font-weight: 700; margin-bottom: 24px;">Knowledge Base Settings</h3>
                            
                            <div class="setting-row">
                                <label class="clay-input-label">Website URLs (CSV)</label>
                                <textarea name="kb_website_urls" class="clay-inset" rows="2" style="width:100%; border:none; resize:none;" placeholder="https://example.com, https://docs.example.com">{{ $settings['kb_website_urls'] }}</textarea>
                            </div>
                            <div class="setting-row">
                                <label class="clay-input-label">Sync Settings & FAQs</label>
                                <textarea name="kb_faqs" class="clay-inset" rows="3" style="width:100%; border:none; resize:none; font-family:monospace; font-size:0.85rem;" placeholder="[{&quot;question&quot;:&quot;Hi&quot;,&quot;answer&quot;:&quot;Hello!&quot;}]">{{ $settings['kb_faqs'] }}</textarea>
                            </div>
                            <div class="setting-row setting-row-split" style="display: grid;">
                                <div>
                                    <label class="clay-input-label">Chunk Size</label>
                                    <input type="number" name="kb_chunk_size" value="{{ $settings['kb_chunk_size'] }}" class="clay-inset" style="width:100%;">
                                </div>
                                <div>
                                    <label class="clay-input-label">Chunk Overlap</label>
                                    <input type="number" name="kb_chunk_overlap" value="{{ $settings['kb_chunk_overlap'] }}" class="clay-inset" style="width:100%;">
                                </div>
                            </div>
                            <div class="setting-row setting-row-split" style="display: grid; margin-top:20px;">
                                <div>
                                    <label class="clay-input-label">Embedding Model</label>
                                    <input type="text" name="kb_embedding_model" value="{{ $settings['kb_embedding_model'] }}" class="clay-inset" style="width:100%;">
                                </div>
                                <div>
                                    <label class="clay-input-label">Max Documents to Retrieve</label>
                                    <input type="number" name="kb_max_documents" value="{{ $settings['kb_max_documents'] }}" class="clay-inset" style="width:100%;">
                                </div>
                            </div>
                            
                            <div style="display:flex; flex-direction:column; gap:12px; margin: 24px 0;">
                                <label class="checkbox-container">
                                    <span class="toggle-switch">
                                        <input type="checkbox" name="kb_auto_sync" {{ $settings['kb_auto_sync'] ? 'checked' : '' }}>
                                        <span class="toggle-slider"></span>
                                    </span>
                                    <span style="font-weight:600; font-size:0.95rem;">Auto Sync Integrations</span>
                                </label>
                                <label class="checkbox-container">
                                    <span class="toggle-switch">
                                        <input type="checkbox" name="kb_citation_display" {{ $settings['kb_citation_display'] ? 'checked' : '' }}>
                                        <span class="toggle-slider"></span>
                                    </span>
                                    <span style="font-weight:600; font-size:0.95rem;">Display Citations in Response</span>
                                </label>
                            </div>

                            <button type="submit" class="clay-btn clay-btn-primary">Save Knowledge Base Settings</button>
                        </form>
                    </div>

                    <!-- 7. CONVERSATION SETTINGS -->
                    <div x-show="activeTab === 'conv'">
                        <form action="{{ route('admin.settings.update') }}" method="POST">
                            @csrf
                            <input type="hidden" name="_group" value="conv">
                            <h3 style="font-size: 1.4rem; font-weight: 700; margin-bottom: 24px;">Conversation Settings</h3>
                            
                            <div class="setting-row setting-row-split" style="display: grid;">
                                <div>
                                    <label class="clay-input-label">Session Timeout (minutes)</label>
                                    <input type="number" name="conv_session_timeout" value="{{ $settings['conv_session_timeout'] }}" class="clay-inset" style="width:100%;">
                                </div>
                                <div>
                                    <label class="clay-input-label">Conversation Length Limit (messages)</label>
                                    <input type="number" name="conv_max_length" value="{{ $settings['conv_max_length'] }}" class="clay-inset" style="width:100%;">
                                </div>
                            </div>
                            <div class="setting-row" style="margin-top:20px;">
                                <label class="clay-input-label">Conversation Categories (comma separated)</label>
                                <input type="text" name="conv_categories" value="{{ $settings['conv_categories'] }}" class="clay-inset">
                            </div>
                            <div style="display:flex; flex-direction:column; gap:12px; margin: 24px 0;">
                                <label class="checkbox-container">
                                    <span class="toggle-switch">
                                        <input type="checkbox" name="conv_store_history" {{ $settings['conv_store_history'] ? 'checked' : '' }}>
                                        <span class="toggle-slider"></span>
                                    </span>
                                    <span style="font-weight:600; font-size:0.95rem;">Store Conversation History</span>
                                </label>
                                <label class="checkbox-container">
                                    <span class="toggle-switch">
                                        <input type="checkbox" name="conv_auto_summary" {{ $settings['conv_auto_summary'] ? 'checked' : '' }}>
                                        <span class="toggle-slider"></span>
                                    </span>
                                    <span style="font-weight:600; font-size:0.95rem;">Auto Summary Conversations</span>
                                </label>
                            </div>

                            <button type="submit" class="clay-btn clay-btn-primary">Save Conversation Settings</button>
                        </form>
                    </div>

                    <!-- 8. HUMAN HANDOFF -->
                    <div x-show="activeTab === 'handoff'">
                        <form action="{{ route('admin.settings.update') }}" method="POST">
                            @csrf
                            <input type="hidden" name="_group" value="handoff">
                            <h3 style="font-size: 1.4rem; font-weight: 700; margin-bottom: 24px;">Human Handoff</h3>
                            
                            <label class="checkbox-container" style="margin-bottom:20px;">
                                <span class="toggle-switch">
                                    <input type="checkbox" name="handoff_enable" {{ $settings['handoff_enable'] ? 'checked' : '' }}>
                                    <span class="toggle-slider"></span>
                                </span>
                                <span style="font-weight:600; font-size:0.95rem; color:var(--accent-blue);">Enable Human Takeover Support</span>
                            </label>
                            <div class="setting-row">
                                <label class="clay-input-label">Triggers (comma separated)</label>
                                <input type="text" name="handoff_triggers" value="{{ $settings['handoff_triggers'] }}" class="clay-inset">
                            </div>
                            <div class="setting-row">
                                <label class="clay-input-label">Escalation Keywords (comma separated)</label>
                                <input type="text" name="handoff_keywords" value="{{ $settings['handoff_keywords'] }}" class="clay-inset">
                            </div>
                            <div class="setting-row setting-row-split" style="display: grid;">
                                <div>
                                    <label class="clay-input-label">Support Team Email</label>
                                    <input type="email" name="handoff_support_team" value="{{ $settings['handoff_support_team'] }}" class="clay-inset" style="width:100%;">
                                </div>
                                <div>
                                    <label class="clay-input-label">Business Hours</label>
                                    <input type="text" name="handoff_business_hours" value="{{ $settings['handoff_business_hours'] }}" class="clay-inset" style="width:100%;">
                                </div>
                            </div>
                            <div class="setting-row" style="margin-top:20px;">
                                <label class="clay-input-label">Queue Message</label>
                                <input type="text" name="handoff_queue_message" value="{{ $settings['handoff_queue_message'] }}" class="clay-inset">
                            </div>

                            <button type="submit" class="clay-btn clay-btn-primary">Save Handoff Settings</button>
                        </form>
                    </div>

                    <!-- 9. USER EXPERIENCE -->
                    <div x-show="activeTab === 'ux'">
                        <form action="{{ route('admin.settings.update') }}" method="POST">
                            @csrf
                            <input type="hidden" name="_group" value="ux">
                            <h3 style="font-size: 1.4rem; font-weight: 700; margin-bottom: 24px;">User Experience</h3>
                            
                            <div class="setting-row setting-row-split" style="display: grid;">
                                <div>
                                    <label class="clay-input-label">Widget Position</label>
                                    <input type="text" name="ux_widget_position" value="{{ $settings['ux_widget_position'] }}" class="clay-inset" style="width:100%;">
                                </div>
                                <div>
                                    <label class="clay-input-label">Widget Size</label>
                                    <input type="text" name="ux_widget_size" value="{{ $settings['ux_widget_size'] }}" class="clay-inset" style="width:100%;">
                                </div>
                            </div>
                            <div class="setting-row" style="margin-top:20px;">
                                <label class="clay-input-label">Suggested Questions (one per line)</label>
                                <textarea name="ux_suggested_questions" class="clay-inset" rows="4" style="width:100%; border:none; resize:none;">{{ $settings['ux_suggested_questions'] }}</textarea>
                            </div>
                            <div style="display:flex; flex-direction:column; gap:12px; margin: 24px 0;">
                                <label class="checkbox-container">
                                    <span class="toggle-switch">
                                        <input type="checkbox" name="ux_typing_indicator" {{ $settings['ux_typing_indicator'] ? 'checked' : '' }}>
                                        <span class="toggle-slider"></span>
                                    </span>
                                    <span style="font-weight:600; font-size:0.95rem;">Show Typing Indicator</span>
                                </label>
                                <label class="checkbox-container">
                                    <span class="toggle-switch">
                                        <input type="checkbox" name="ux_feedback_thumbs" {{ $settings['ux_feedback_thumbs'] ? 'checked' : '' }}>
                                        <span class="toggle-slider"></span>
                                    </span>
                                    <span style="font-weight:600; font-size:0.95rem;">Enable Rating & Comments (Feedback)</span>
                                </label>
                            </div>

                            <button type="submit" class="clay-btn clay-btn-primary">Save UX Settings</button>
                        </form>
                    </div>

                    <!-- 10. LANGUAGE SETTINGS -->
                    <div x-show="activeTab === 'lang'">
                        <form action="{{ route('admin.settings.update') }}" method="POST">
                            @csrf
                            <input type="hidden" name="_group" value="lang">
                            <h3 style="font-size: 1.4rem; font-weight: 700; margin-bottom: 24px;">Language Settings</h3>
                            
                            <div class="setting-row">
                                <label class="clay-input-label">Supported Languages (comma separated)</label>
                                <input type="text" name="lang_supported" value="{{ $settings['lang_supported'] }}" class="clay-inset">
                            </div>
                            <div class="setting-row">
                                <label class="clay-input-label">Fallback Language</label>
                                <input type="text" name="lang_fallback" value="{{ $settings['lang_fallback'] }}" class="clay-inset">
                            </div>
                            <div style="display:flex; flex-direction:column; gap:12px; margin: 24px 0;">
                                <label class="checkbox-container">
                                    <span class="toggle-switch">
                                        <input type="checkbox" name="lang_auto_detect" {{ $settings['lang_auto_detect'] ? 'checked' : '' }}>
                                        <span class="toggle-slider"></span>
                                    </span>
                                    <span style="font-weight:600; font-size:0.95rem;">Auto Detect User Locale</span>
                                </label>
                                <label class="checkbox-container">
                                    <span class="toggle-switch">
                                        <input type="checkbox" name="lang_rtl_support" {{ $settings['lang_rtl_support'] ? 'checked' : '' }}>
                                        <span class="toggle-slider"></span>
                                    </span>
                                    <span style="font-weight:600; font-size:0.95rem;">Enable RTL Support</span>
                                </label>
                            </div>

                            <button type="submit" class="clay-btn clay-btn-primary">Save Language Settings</button>
                        </form>
                    </div>

                    <!-- 11. ALERTS & NOTIFICATIONS -->
                    <div x-show="activeTab === 'notif'">
                        <form action="{{ route('admin.settings.update') }}" method="POST">
                            @csrf
                            <input type="hidden" name="_group" value="notif">
                            <h3 style="font-size: 1.4rem; font-weight: 700; margin-bottom: 24px;">Alerts & Notifications</h3>
                            
                            <h4 style="margin-bottom:12px; font-weight:700;">Admin Alerts Trigger</h4>
                            <div style="display:flex; flex-direction:column; gap:12px; margin-bottom: 24px;">
                                <label class="checkbox-container">
                                    <span class="toggle-switch">
                                        <input type="checkbox" name="notif_admin_ai_failures" {{ $settings['notif_admin_ai_failures'] ? 'checked' : '' }}>
                                        <span class="toggle-slider"></span>
                                    </span>
                                    <span style="font-weight:600; font-size:0.95rem;">AI Call Failures</span>
                                </label>
                                <label class="checkbox-container">
                                    <span class="toggle-switch">
                                        <input type="checkbox" name="notif_admin_security_events" {{ $settings['notif_admin_security_events'] ? 'checked' : '' }}>
                                        <span class="toggle-slider"></span>
                                    </span>
                                    <span style="font-weight:600; font-size:0.95rem;">Security Events / Prompt Injections</span>
                                </label>
                                <label class="checkbox-container">
                                    <span class="toggle-switch">
                                        <input type="checkbox" name="notif_admin_negative_reviews" {{ $settings['notif_admin_negative_reviews'] ? 'checked' : '' }}>
                                        <span class="toggle-slider"></span>
                                    </span>
                                    <span style="font-weight:600; font-size:0.95rem;">Negative Reviews & Feedback</span>
                                </label>
                            </div>
                            <div class="setting-row">
                                <label class="clay-input-label">Slack Webhook URL</label>
                                <input type="text" name="notif_slack_webhook" value="{{ $settings['notif_slack_webhook'] }}" class="clay-inset">
                            </div>

                            <button type="submit" class="clay-btn clay-btn-primary">Save Notification Settings</button>
                        </form>
                    </div>

                    <!-- 12. SECURITY & MODERATION -->
                    <div x-show="activeTab === 'security'">
                        <form action="{{ route('admin.settings.update') }}" method="POST">
                            @csrf
                            <input type="hidden" name="_group" value="security">
                            <h3 style="font-size: 1.4rem; font-weight: 700; margin-bottom: 24px;">Security & Policy Settings</h3>
                            
                            <div class="setting-row">
                                <label class="clay-input-label">Allowed Auth Methods (comma separated)</label>
                                <input type="text" name="security_auth_methods" value="{{ $settings['security_auth_methods'] }}" class="clay-inset">
                            </div>
                            <div class="setting-row">
                                <label class="clay-input-label">IP Whitelist (comma separated IPs)</label>
                                <input type="text" name="security_ip_whitelist" value="{{ $settings['security_ip_whitelist'] }}" class="clay-inset">
                            </div>
                            <div style="display:flex; flex-direction:column; gap:12px; margin: 24px 0;">
                                <label class="checkbox-container">
                                    <span class="toggle-switch">
                                        <input type="checkbox" name="security_prompt_injection" {{ $settings['security_prompt_injection'] ? 'checked' : '' }}>
                                        <span class="toggle-slider"></span>
                                    </span>
                                    <span style="font-weight:600; font-size:0.95rem;">Enable Prompt Injection Protection</span>
                                </label>
                                <label class="checkbox-container">
                                    <span class="toggle-switch">
                                        <input type="checkbox" name="security_jailbreak_protection" {{ $settings['security_jailbreak_protection'] ? 'checked' : '' }}>
                                        <span class="toggle-slider"></span>
                                    </span>
                                    <span style="font-weight:600; font-size:0.95rem;">Enable Jailbreak Protection</span>
                                </label>
                                <label class="checkbox-container">
                                    <span class="toggle-switch">
                                        <input type="checkbox" name="security_sensitive_data" {{ $settings['security_sensitive_data'] ? 'checked' : '' }}>
                                        <span class="toggle-slider"></span>
                                    </span>
                                    <span style="font-weight:600; font-size:0.95rem;">Detect Sensitive Data (PII, API Keys)</span>
                                </label>
                            </div>

                            <button type="submit" class="clay-btn clay-btn-primary">Save Security Settings</button>
                        </form>
                    </div>

                    <!-- 13. ROLES & PERMISSIONS -->
                    <div x-show="activeTab === 'roles'">
                        <form action="{{ route('admin.settings.update') }}" method="POST">
                            @csrf
                            <input type="hidden" name="_group" value="roles">
                            <h3 style="font-size: 1.4rem; font-weight: 700; margin-bottom: 24px;">Roles & Permissions</h3>
                            
                            <div class="setting-row">
                                <label class="clay-input-label">Roles (comma separated)</label>
                                <textarea name="roles_list" class="clay-inset" rows="3" style="width:100%; border:none; resize:none;">{{ $settings['roles_list'] }}</textarea>
                            </div>
                            <div class="setting-row">
                                <label class="clay-input-label">Permissions (comma separated)</label>
                                <textarea name="permissions_list" class="clay-inset" rows="3" style="width:100%; border:none; resize:none;">{{ $settings['permissions_list'] }}</textarea>
                            </div>

                            <button type="submit" class="clay-btn clay-btn-primary">Save Roles & Permissions</button>
                        </form>
                    </div>

                    <!-- 14. DATA & PRIVACY -->
                    <div x-show="activeTab === 'privacy'">
                        <form action="{{ route('admin.settings.update') }}" method="POST">
                            @csrf
                            <input type="hidden" name="_group" value="privacy">
                            <h3 style="font-size: 1.4rem; font-weight: 700; margin-bottom: 24px;">Data & Privacy</h3>
                            
                            <div class="setting-row">
                                <label class="clay-input-label">Data Retention (days)</label>
                                <input type="number" name="privacy_retention_days" value="{{ $settings['privacy_retention_days'] }}" class="clay-inset">
                            </div>
                            <div style="display:flex; flex-direction:column; gap:12px; margin: 24px 0;">
                                <label class="checkbox-container">
                                    <span class="toggle-switch">
                                        <input type="checkbox" name="privacy_gdpr_compliance" {{ $settings['privacy_gdpr_compliance'] ? 'checked' : '' }}>
                                        <span class="toggle-slider"></span>
                                    </span>
                                    <span style="font-weight:600; font-size:0.95rem;">GDPR Compliance Controls</span>
                                </label>
                                <label class="checkbox-container">
                                    <span class="toggle-switch">
                                        <input type="checkbox" name="privacy_consent_banner" {{ $settings['privacy_consent_banner'] ? 'checked' : '' }}>
                                        <span class="toggle-slider"></span>
                                    </span>
                                    <span style="font-weight:600; font-size:0.95rem;">Show Cookie Consent Banner</span>
                                </label>
                            </div>

                            <button type="submit" class="clay-btn clay-btn-primary">Save Privacy Settings</button>
                        </form>
                    </div>

                    <!-- 15. INTEGRATIONS -->
                    <div x-show="activeTab === 'integrations'">
                        <form action="{{ route('admin.settings.update') }}" method="POST">
                            @csrf
                            <input type="hidden" name="_group" value="integrations">
                            <h3 style="font-size: 1.4rem; font-weight: 700; margin-bottom: 24px;">Integrations</h3>
                            
                            <div class="setting-row">
                                <label class="clay-input-label">Auth Providers</label>
                                <input type="text" name="integrations_auth_providers" value="{{ $settings['integrations_auth_providers'] }}" class="clay-inset">
                            </div>
                            <div class="setting-row">
                                <label class="clay-input-label">Communication Channels</label>
                                <input type="text" name="integrations_comm_channels" value="{{ $settings['integrations_comm_channels'] }}" class="clay-inset">
                            </div>
                            <div class="setting-row">
                                <label class="clay-input-label">Payments Gateway</label>
                                <input type="text" name="integrations_payments" value="{{ $settings['integrations_payments'] }}" class="clay-inset">
                            </div>

                            <button type="submit" class="clay-btn clay-btn-primary">Save Integrations</button>
                        </form>
                    </div>

                    <!-- 16. USAGE & BILLING -->
                    <div x-show="activeTab === 'billing'">
                        <form action="{{ route('admin.settings.update') }}" method="POST">
                            @csrf
                            <input type="hidden" name="_group" value="billing">
                            <h3 style="font-size: 1.4rem; font-weight: 700; margin-bottom: 24px;">Usage & Billing</h3>
                            
                            <div class="setting-row setting-row-split" style="display: grid;">
                                <div>
                                    <label class="clay-input-label">Global Cost Budget Limit ($)</label>
                                    <input type="number" step="0.1" name="billing_cost_budget" value="{{ $settings['billing_cost_budget'] }}" class="clay-inset" style="width:100%;">
                                </div>
                                <div>
                                    <label class="clay-input-label">Tax Rate (%)</label>
                                    <input type="number" step="0.1" name="billing_taxes" value="{{ $settings['billing_taxes'] }}" class="clay-inset" style="width:100%;">
                                </div>
                            </div>

                            <button type="submit" class="clay-btn clay-btn-primary">Save Billing Details</button>
                        </form>
                    </div>

                    <!-- 17. ANALYTICS CONFIG -->
                    <div x-show="activeTab === 'analytics'">
                        <form action="{{ route('admin.settings.update') }}" method="POST">
                            @csrf
                            <input type="hidden" name="_group" value="analytics">
                            <h3 style="font-size: 1.4rem; font-weight: 700; margin-bottom: 24px;">Analytics Simulation Details</h3>
                            <p style="color:var(--text-muted); margin-bottom:16px;">These details populate the admin dashboard analytical figures.</p>
                            
                            <div class="setting-row setting-row-split" style="display: grid;">
                                <div>
                                    <label class="clay-input-label">Average Response Time (seconds)</label>
                                    <input type="number" step="0.1" name="analytics_avg_response_time" value="{{ $settings['analytics_avg_response_time'] }}" class="clay-inset" style="width:100%;">
                                </div>
                                <div>
                                    <label class="clay-input-label">AI Accuracy Rate (%)</label>
                                    <input type="number" step="0.1" name="analytics_ai_accuracy" value="{{ $settings['analytics_ai_accuracy'] }}" class="clay-inset" style="width:100%;">
                                </div>
                            </div>

                            <button type="submit" class="clay-btn clay-btn-primary">Save Analytics Config</button>
                        </form>
                    </div>

                    <!-- 18. CONTENT MODERATION -->
                    <div x-show="activeTab === 'moderation'">
                        <form action="{{ route('admin.settings.update') }}" method="POST">
                            @csrf
                            <input type="hidden" name="_group" value="moderation">
                            <h3 style="font-size: 1.4rem; font-weight: 700; margin-bottom: 24px;">Content Moderation</h3>
                            
                            <div style="display:flex; flex-direction:column; gap:12px; margin-bottom: 24px;">
                                <label class="checkbox-container">
                                    <span class="toggle-switch">
                                        <input type="checkbox" name="moderation_profanity_filter" {{ $settings['moderation_profanity_filter'] ? 'checked' : '' }}>
                                        <span class="toggle-slider"></span>
                                    </span>
                                    <span style="font-weight:600; font-size:0.95rem;">Filter Profanity</span>
                                </label>
                                <label class="checkbox-container">
                                    <span class="toggle-switch">
                                        <input type="checkbox" name="moderation_hate_speech" {{ $settings['moderation_hate_speech'] ? 'checked' : '' }}>
                                        <span class="toggle-slider"></span>
                                    </span>
                                    <span style="font-weight:600; font-size:0.95rem;">Detect Hate Speech & Violence</span>
                                </label>
                            </div>
                            <div class="setting-row">
                                <label class="clay-input-label">Blocked Keywords (comma separated)</label>
                                <textarea name="moderation_blocked_keywords" class="clay-inset" rows="3" style="width:100%; border:none; resize:none;">{{ $settings['moderation_blocked_keywords'] }}</textarea>
                            </div>

                            <button type="submit" class="clay-btn clay-btn-primary">Save Moderation Rules</button>
                        </form>
                    </div>

                    <!-- 19. LOGS & MONITOR -->
                    <div x-show="activeTab === 'logging'">
                        <form action="{{ route('admin.settings.update') }}" method="POST">
                            @csrf
                            <input type="hidden" name="_group" value="logging">
                            <h3 style="font-size: 1.4rem; font-weight: 700; margin-bottom: 24px;">Logs & Monitoring</h3>
                            
                            <div style="display:flex; flex-direction:column; gap:12px; margin-bottom: 24px;">
                                <label class="checkbox-container">
                                    <span class="toggle-switch">
                                        <input type="checkbox" name="logging_system" {{ $settings['logging_system'] ? 'checked' : '' }}>
                                        <span class="toggle-slider"></span>
                                    </span>
                                    <span style="font-weight:600; font-size:0.95rem;">Log System Actions</span>
                                </label>
                                <label class="checkbox-container">
                                    <span class="toggle-switch">
                                        <input type="checkbox" name="logging_ai_request" {{ $settings['logging_ai_request'] ? 'checked' : '' }}>
                                        <span class="toggle-slider"></span>
                                    </span>
                                    <span style="font-weight:600; font-size:0.95rem;">Log AI Request Payload</span>
                                </label>
                            </div>

                            <button type="submit" class="clay-btn clay-btn-primary">Save Logging Settings</button>
                        </form>
                    </div>

                    <!-- 20. BACKUP & RECOVERY -->
                    <div x-show="activeTab === 'backup'">
                        <form action="{{ route('admin.settings.update') }}" method="POST">
                            @csrf
                            <input type="hidden" name="_group" value="backup">
                            <h3 style="font-size: 1.4rem; font-weight: 700; margin-bottom: 24px;">Backup & Recovery</h3>
                            
                            <div class="setting-row setting-row-split" style="display: grid;">
                                <div>
                                    <label class="clay-input-label">Backup Schedule</label>
                                    <input type="text" name="backup_frequency" value="{{ $settings['backup_frequency'] }}" class="clay-inset" style="width:100%;">
                                </div>
                                <div>
                                    <label class="clay-input-label">Retained Backup Copies</label>
                                    <input type="number" name="backup_retention_count" value="{{ $settings['backup_retention_count'] }}" class="clay-inset" style="width:100%;">
                                </div>
                            </div>
                            <div style="margin:24px 0;">
                                <label class="checkbox-container">
                                    <span class="toggle-switch">
                                        <input type="checkbox" name="backup_auto" {{ $settings['backup_auto'] ? 'checked' : '' }}>
                                        <span class="toggle-slider"></span>
                                    </span>
                                    <span style="font-weight:600; font-size:0.95rem;">Enable Automatic Backup</span>
                                </label>
                            </div>

                            <button type="submit" class="clay-btn clay-btn-primary">Save Backup Configuration</button>
                        </form>
                    </div>

                    <!-- 21. DEVELOPER SETTINGS -->
                    <div x-show="activeTab === 'developer'">
                        <form action="{{ route('admin.settings.update') }}" method="POST">
                            @csrf
                            <input type="hidden" name="_group" value="developer">
                            <h3 style="font-size: 1.4rem; font-weight: 700; margin-bottom: 24px;">Developer Settings</h3>
                            
                            <div class="setting-row">
                                <label class="clay-input-label">API Rate Limits (req/min)</label>
                                <input type="number" name="developer_rate_limits" value="{{ $settings['developer_rate_limits'] }}" class="clay-inset">
                            </div>
                            <div style="display:flex; flex-direction:column; gap:12px; margin: 24px 0;">
                                <label class="checkbox-container">
                                    <span class="toggle-switch">
                                        <input type="checkbox" name="developer_api_playground" {{ $settings['developer_api_playground'] ? 'checked' : '' }}>
                                        <span class="toggle-slider"></span>
                                    </span>
                                    <span style="font-weight:600; font-size:0.95rem;">Enable API Playground</span>
                                </label>
                                <label class="checkbox-container">
                                    <span class="toggle-switch">
                                        <input type="checkbox" name="developer_debug_mode" {{ $settings['developer_debug_mode'] ? 'checked' : '' }}>
                                        <span class="toggle-slider"></span>
                                    </span>
                                    <span style="font-weight:600; font-size:0.95rem; color:#dc3545;">Enable Debug Mode</span>
                                </label>
                            </div>

                            <button type="submit" class="clay-btn clay-btn-primary">Save Developer Settings</button>
                        </form>
                    </div>

                    <!-- 22. APPEARANCE & BRANDING -->
                    <div x-show="activeTab === 'branding'">
                        <form action="{{ route('admin.settings.update') }}" method="POST">
                            @csrf
                            <input type="hidden" name="_group" value="branding">
                            <h3 style="font-size: 1.4rem; font-weight: 700; margin-bottom: 24px;">Appearance & Branding</h3>
                            
                            <div class="setting-row">
                                <label class="clay-input-label">Footer Text</label>
                                <input type="text" name="branding_footer_text" value="{{ $settings['branding_footer_text'] }}" class="clay-inset">
                            </div>
                            <div class="setting-row">
                                <label class="clay-input-label">Custom Domain</label>
                                <input type="text" name="branding_custom_domain" value="{{ $settings['branding_custom_domain'] }}" class="clay-inset" placeholder="chat.yourdomain.com">
                            </div>
                            <div class="setting-row">
                                <label class="clay-input-label">Custom CSS Override</label>
                                <textarea name="branding_custom_css" class="clay-inset" rows="3" style="width:100%; border:none; resize:none; font-family:monospace; font-size:0.85rem;" placeholder="body { background: red; }">{{ $settings['branding_custom_css'] }}</textarea>
                            </div>

                            <button type="submit" class="clay-btn clay-btn-primary">Save Branding Settings</button>
                        </form>
                    </div>

                    <!-- 23. FEATURE TOGGLES -->
                    <div x-show="activeTab === 'toggle'">
                        <form action="{{ route('admin.settings.update') }}" method="POST">
                            @csrf
                            <input type="hidden" name="_group" value="toggle">
                            <h3 style="font-size: 1.4rem; font-weight: 700; margin-bottom: 24px;">Feature Toggles</h3>
                            
                            <div style="display:flex; flex-direction:column; gap:12px; margin-bottom: 24px;">
                                <label class="checkbox-container">
                                    <span class="toggle-switch">
                                        <input type="checkbox" name="toggle_vision" {{ $settings['toggle_vision'] ? 'checked' : '' }}>
                                        <span class="toggle-slider"></span>
                                    </span>
                                    <span style="font-weight:600; font-size:0.95rem;">Enable Vision Capabilities</span>
                                </label>
                                <label class="checkbox-container">
                                    <span class="toggle-switch">
                                        <input type="checkbox" name="toggle_images" {{ $settings['toggle_images'] ? 'checked' : '' }}>
                                        <span class="toggle-slider"></span>
                                    </span>
                                    <span style="font-weight:600; font-size:0.95rem;">Enable Image Generation</span>
                                </label>
                                <label class="checkbox-container">
                                    <span class="toggle-switch">
                                        <input type="checkbox" name="toggle_file_upload" {{ $settings['toggle_file_upload'] ? 'checked' : '' }}>
                                        <span class="toggle-slider"></span>
                                    </span>
                                    <span style="font-weight:600; font-size:0.95rem;">Enable File Uploads</span>
                                </label>
                                <label class="checkbox-container">
                                    <span class="toggle-switch">
                                        <input type="checkbox" name="toggle_knowledge_base" {{ $settings['toggle_knowledge_base'] ? 'checked' : '' }}>
                                        <span class="toggle-slider"></span>
                                    </span>
                                    <span style="font-weight:600; font-size:0.95rem;">Enable Knowledge Base (RAG)</span>
                                </label>
                                <label class="checkbox-container">
                                    <span class="toggle-switch">
                                        <input type="checkbox" name="toggle_human_handoff" {{ $settings['toggle_human_handoff'] ? 'checked' : '' }}>
                                        <span class="toggle-slider"></span>
                                    </span>
                                    <span style="font-weight:600; font-size:0.95rem;">Enable Human Handoff</span>
                                </label>
                                <label class="checkbox-container">
                                    <span class="toggle-switch">
                                        <input type="checkbox" name="toggle_feedback" {{ $settings['toggle_feedback'] ? 'checked' : '' }}>
                                        <span class="toggle-slider"></span>
                                    </span>
                                    <span style="font-weight:600; font-size:0.95rem;">Enable User Feedback (Rating)</span>
                                </label>
                                <label class="checkbox-container">
                                    <span class="toggle-switch">
                                        <input type="checkbox" name="toggle_analytics" {{ $settings['toggle_analytics'] ? 'checked' : '' }}>
                                        <span class="toggle-slider"></span>
                                    </span>
                                    <span style="font-weight:600; font-size:0.95rem;">Enable System Analytics</span>
                                </label>
                                <label class="checkbox-container">
                                    <span class="toggle-switch">
                                        <input type="checkbox" name="toggle_api_access" {{ $settings['toggle_api_access'] ? 'checked' : '' }}>
                                        <span class="toggle-slider"></span>
                                    </span>
                                    <span style="font-weight:600; font-size:0.95rem;">Enable API Keys Access</span>
                                </label>
                            </div>

                            <button type="submit" class="clay-btn clay-btn-primary">Save Feature Toggles</button>
                        </form>
                    </div>

                </div>
            </div>
        </main>
    </div>
</body>
</html>
