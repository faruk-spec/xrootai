<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>{{ \App\Models\SystemSetting::get('general_chatbot_name', 'XrootAI') }} Chat</title>
    @if(\App\Models\SystemSetting::get('general_site_icon'))
        <link rel="icon" href="{{ \App\Models\SystemSetting::get('general_site_icon') }}">
    @endif
    
    <!-- Stylesheets -->
    <link rel="stylesheet" href="{{ asset('css/claymorphism.css') }}">
    <link rel="stylesheet" href="{{ asset('css/github-dark.min.css') }}">
    
    <!-- Scripts -->
    <script src="{{ asset('js/alpine.min.js') }}" defer></script>
    <script src="{{ asset('js/markdown-it.min.js') }}"></script>
    <script src="{{ asset('js/highlight.min.js') }}"></script>
    
    <style>
        /* Ambient glows local to chat workspace */
        .glow-sphere {
            position: absolute;
            border-radius: 50%;
            filter: blur(120px);
            z-index: -1;
            opacity: 0.35;
            pointer-events: none;
        }
        .glow-1 {
            top: 20%;
            left: 25%;
            width: 350px;
            height: 350px;
            background: rgba(126, 182, 255, 0.3);
        }
        .glow-2 {
            bottom: 20%;
            right: 20%;
            width: 400px;
            height: 400px;
            background: rgba(168, 224, 99, 0.25);
        }

        /* Custom Scrollbar Styles for a premium UI */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }
        .dark-mode ::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.15);
        }
        ::-webkit-scrollbar-thumb:hover {
            background: rgba(0, 0, 0, 0.2);
        }
        .dark-mode ::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.25);
        }
        * {
            scrollbar-width: thin;
            scrollbar-color: rgba(0, 0, 0, 0.1) transparent;
        }
        .dark-mode * {
            scrollbar-color: rgba(255, 255, 255, 0.15) transparent;
        }

        /* Responsive Layout grid */
        html, body {
            margin: 0 !important;
            padding: 0 !important;
            height: 100% !important;
            height: 100vh !important;
            height: 100dvh !important;
            width: 100% !important;
            overflow: hidden !important;
            max-width: 100vw !important;
            max-height: 100vh !important;
            max-height: 100dvh !important;
            position: fixed !important;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
        }
        .workspace {
            display: grid;
            grid-template-columns: auto 1fr;
            height: 100vh;
            height: 100dvh;
            width: 100%;
            max-width: 100vw;
            overflow: hidden;
            position: relative;
            box-sizing: border-box;
        }

        /* Mobile hamburger button - hidden on desktop, shown on mobile */
        .mobile-menu-btn {
            display: none;
        }
        /* Sidebar close button - hidden on desktop, shown on mobile */
        .sidebar-close-btn {
            display: none;
        }

        @media (max-width: 768px) {
            .workspace {
                grid-template-columns: 1fr;
            }
            .sidebar {
                position: fixed;
                left: 0;
                top: 0;
                bottom: 0;
                z-index: 50;
                transform: translateX(-100%);
                transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
                width: 80vw;
                max-width: 300px;
            }
            .sidebar.open {
                transform: translateX(0);
            }
            /* Remove display:block !important overlay override since Alpine x-show is used */
            .sidebar-overlay {
                /* Display is fully controlled by Alpine JS x-show display state */
            }
            /* Show mobile-only controls */
            .mobile-menu-btn {
                display: flex;
            }
            .sidebar-close-btn {
                display: flex;
            }
            /* Remove textarea placeholder on mobile */
            .chat-textarea::placeholder {
                color: transparent !important;
                opacity: 0 !important;
            }
            .chat-textarea::-webkit-input-placeholder {
                color: transparent !important;
                opacity: 0 !important;
            }
            /* Hide model name on mobile, show logo instead */
            .header-model-label { display: none; }
            .header-mobile-logo { display: flex !important; }
            /* Scale down inputs/selects on mobile */
            select, input, button { font-size: 16px; }
            /* Ensure brightness is proper on mobile */
            body { -webkit-text-size-adjust: 100%; }
            /* Mobile padding reductions to fit narrow screens */
            .chat-messages {
                padding: 16px !important;
            }
            .chat-input-area {
                padding: 12px 16px !important;
            }
            .message-bubble {
                max-width: 95% !important;
            }
            /* Shrink active model select dropdown and reduce header padding on mobile to fit screen */
            .chat-header {
                padding: 0 12px !important;
            }
            select.clay-inset {
                max-width: 120px !important;
                padding: 6px 8px !important;
                font-size: 0.8rem !important;
            }
            /* Speed up slide transition and promote to GPU rendering on mobile */
            .sidebar {
                position: fixed;
                left: 0;
                top: 0;
                bottom: 0;
                z-index: 50;
                transform: translateX(-100%);
                width: 80vw !important;
                min-width: 80vw !important;
                max-width: 320px !important;
                padding: 20px !important;
                transition: transform 0.18s cubic-bezier(0.16, 1, 0.3, 1) !important;
                will-change: transform;
            }
            .sidebar.collapsed {
                width: 80vw !important;
                min-width: 80vw !important;
                padding: 20px !important;
            }
            .sidebar.collapsed .sidebar-text,
            .sidebar.collapsed .sidebar-search,
            .sidebar.collapsed .sidebar-history,
            .sidebar.collapsed .sidebar-footer-text,
            .sidebar.collapsed .btn-new-chat span.new-chat-text {
                display: block !important;
            }
            .sidebar.collapsed .btn-new-chat {
                width: 100% !important;
                height: auto !important;
                padding: 12px 18px !important;
                border-radius: 18px !important;
            }
            .sidebar.collapsed .app-brand span {
                display: inline !important;
            }
            .sidebar.collapsed .app-brand {
                justify-content: flex-start !important;
            }
            /* Remove sidebar collapse button and close button in responsive mode */
            .desktop-collapse-btn,
            .sidebar-close-btn {
                display: none !important;
            }
        }

        /* Safe area support for notch and gesture bar */
        .chat-header {
            padding-top: env(safe-area-inset-top, 0px) !important;
            height: calc(70px + env(safe-area-inset-top, 0px)) !important;
        }
        .chat-input-area {
            padding-bottom: calc(20px + env(safe-area-inset-bottom, 0px)) !important;
        }
        .sidebar {
            padding-top: calc(20px + env(safe-area-inset-top, 0px)) !important;
            padding-bottom: calc(20px + env(safe-area-inset-bottom, 0px)) !important;
        }
        @media (max-width: 768px) {
            .chat-input-area {
                padding-bottom: calc(12px + env(safe-area-inset-bottom, 0px)) !important;
            }
        }

        /* AI Fast Loading Card Animation */
        .ai-fast-loading-card {
            display: flex;
            flex-direction: column;
            gap: 14px;
            width: 100%;
            padding: 18px 22px !important;
            background: var(--clay-card-bg) !important;
            border: 1px solid rgba(74, 136, 255, 0.28) !important;
            box-shadow: 0 8px 32px rgba(74, 136, 255, 0.12), inset 0 0 0 1px rgba(255, 255, 255, 0.1);
            border-radius: 20px 20px 20px 6px;
            animation: cardPulse 2s infinite ease-in-out;
        }
        @keyframes cardPulse {
            0%, 100% { border-color: rgba(74, 136, 255, 0.28); box-shadow: 0 8px 32px rgba(74, 136, 255, 0.12); }
            50% { border-color: rgba(74, 136, 255, 0.58); box-shadow: 0 8px 32px rgba(74, 136, 255, 0.26); }
        }
        .ai-spinner-avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: linear-gradient(135deg, #4a88ff, #6b52ff, #22c55e);
            background-size: 200% 200%;
            animation: gradientSpin 3s infinite linear;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 800;
            font-size: 0.8rem;
            box-shadow: 0 4px 14px rgba(74, 136, 255, 0.4);
            flex-shrink: 0;
        }
        @keyframes gradientSpin {
            0% { background-position: 0% 50%; transform: rotate(0deg); }
            50% { background-position: 100% 50%; transform: rotate(180deg); }
            100% { background-position: 0% 50%; transform: rotate(360deg); }
        }
        .ai-shimmer-line {
            height: 11px;
            border-radius: 6px;
            background: linear-gradient(90deg, rgba(74, 136, 255, 0.08) 25%, rgba(74, 136, 255, 0.28) 50%, rgba(74, 136, 255, 0.08) 75%);
            background-size: 200% 100%;
            animation: shimmerWave 1.5s infinite linear;
        }
        @keyframes shimmerWave {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        /* Responsive scaling using clamp() for typography and spacing */
        .chat-messages {
            font-size: clamp(0.9rem, 2.2vw, 1.02rem) !important;
        }
        .sidebar {
            font-size: clamp(0.85rem, 2vw, 0.95rem) !important;
        }

        /* Sidebar styling — supports collapse on desktop */
        .sidebar {
            background-color: var(--bg-sidebar);
            border-right: 1px solid var(--clay-card-border);
            display: flex;
            flex-direction: column;
            height: 100%;
            overflow: hidden;
            padding: 20px;
            gap: 16px;
            width: 300px;
            min-width: 300px;
            transition: width 0.22s cubic-bezier(0.2, 0, 0, 1),
                        min-width 0.22s cubic-bezier(0.2, 0, 0, 1),
                        padding 0.22s ease;
            will-change: width, min-width;
        }
        /* Collapsed desktop sidebar — icon strip only */
        .sidebar.collapsed {
            width: 72px !important;
            min-width: 72px !important;
            padding: 20px 14px;
        }
        .sidebar.collapsed .sidebar-text,
        .sidebar.collapsed .sidebar-search,
        .sidebar.collapsed .sidebar-history,
        .sidebar.collapsed .sidebar-footer-text {
            display: none !important;
        }
        .sidebar.collapsed .app-brand span {
            display: none;
        }
        .sidebar.collapsed .app-brand {
            justify-content: center;
        }

        /* Custom Puffed Gradient + New Chat Button */
        .btn-new-chat {
            background: linear-gradient(135deg, #4a88ff 0%, #3b5bdb 50%, #6b52ff 100%);
            color: #ffffff !important;
            border: 1px solid rgba(255, 255, 255, 0.25);
            border-radius: 18px;
            padding: 12px 18px;
            font-weight: 700;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            box-shadow: 0 6px 18px rgba(74, 136, 255, 0.35), inset 2px 2px 4px rgba(255, 255, 255, 0.3);
            transition: all 0.22s cubic-bezier(0.2, 0, 0, 1) !important;
            cursor: pointer;
            text-decoration: none;
            width: 100%;
        }
        .btn-new-chat:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(74, 136, 255, 0.45), inset 2px 2px 6px rgba(255, 255, 255, 0.4);
        }
        .btn-new-chat:active {
            transform: translateY(1px);
            box-shadow: 0 3px 10px rgba(74, 136, 255, 0.3), inset 1px 1px 3px rgba(0, 0, 0, 0.2);
        }
        .sidebar.collapsed .btn-new-chat {
            width: 44px !important;
            height: 44px !important;
            padding: 0 !important;
            border-radius: 14px;
        }
        .sidebar.collapsed .btn-new-chat span.new-chat-text {
            display: none !important;
        }

        /* History Item Actions (Pin, Rename, Delete) */
        .history-item-actions {
            display: flex;
            align-items: center;
            gap: 3px;
            opacity: 0.8;
            transition: opacity 0.2s ease;
        }
        .history-item:hover .history-item-actions {
            opacity: 1;
        }
        .action-icon-btn {
            background: none;
            border: none;
            padding: 4px;
            cursor: pointer;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            justify-content: center;
            width: 26px;
            height: 26px;
            border-radius: 8px;
            transition: background-color 0.15s ease, color 0.15s ease, transform 0.15s ease;
        }
        .action-icon-btn:hover {
            background-color: rgba(74, 136, 255, 0.15);
            color: #4a88ff;
            transform: scale(1.08);
        }
        .action-icon-btn.btn-delete:hover {
            background-color: rgba(239, 68, 68, 0.15);
            color: #ef4444;
        }
        /* Header mobile logo (hidden on desktop, shown on mobile) */
        .header-mobile-logo {
            display: none;
            align-items: center;
            gap: 8px;
            font-weight: 700;
            font-size: 1.1rem;
        }
        .header-mobile-logo span {
            background: linear-gradient(135deg, #4a88ff, #56ab2f);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        /* Dark mode toggle button */
        .theme-toggle-btn {
            border-radius: 50%;
            width: 40px;
            height: 40px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        /* Chat view pane */
        .chat-pane {
            display: flex;
            flex-direction: column;
            height: 100%;
            overflow: hidden;
            position: relative;
            min-height: 0;
            min-width: 0; /* Prevents column expansion on responsive grids */
        }

        .chat-header {
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            border-bottom: 1px solid var(--clay-card-border);
            backdrop-filter: blur(10px);
            z-index: 10;
        }

        .chat-messages {
            flex: 1 1 0;
            min-height: 0;
            overflow-y: auto;
            padding: 24px;
            display: flex;
            flex-direction: column;
            gap: 20px;
            scroll-behavior: smooth;
        }

        .chat-input-area {
            padding: 20px 24px;
            border-top: 1px solid var(--clay-card-border);
            backdrop-filter: blur(10px);
        }

        /* Message bubbles */
        .message-bubble {
            max-width: 80%;
            margin-bottom: 4px;
            line-height: 1.6;
            word-break: break-word;
            overflow-wrap: break-word;
            /* Ensure text inside is selectable and visible */
            min-width: 0;
        }
        .message-bubble .msg-content {
            padding: 14px 18px;
        }
        .message-bubble pre {
            max-width: 100%;
            overflow-x: auto;
        }
        /* Collapsible long message */
        .msg-body {
            position: relative;
        }
        .msg-body.collapsed {
            max-height: 300px;
            overflow: hidden;
        }
        .msg-body.collapsed::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 60px;
            pointer-events: none;
        }
        .message-assistant .msg-body.collapsed::after {
            background: linear-gradient(transparent, #ffffff) !important;
        }
        .dark-mode .message-assistant .msg-body.collapsed::after {
            background: linear-gradient(transparent, #1e293b) !important;
        }
        .message-user .msg-body.collapsed::after {
            background: linear-gradient(transparent, #2563eb) !important;
        }
        .dark-mode .message-user .msg-body.collapsed::after {
            background: linear-gradient(transparent, #1d4ed8) !important;
        }
        .expand-btn {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--text-muted);
            background: none;
            border: none;
            cursor: pointer;
            padding: 6px 18px 10px;
            width: 100%;
            justify-content: center;
            transition: color 0.15s;
        }
        .expand-btn:hover {
            color: var(--text-primary);
        }
        /* Sidebar overlay: hidden by default, shown via Alpine x-show on mobile only.
           pointer-events:none when not displayed prevents click blocking. */
        .sidebar-overlay {
            pointer-events: none;
        }
        .sidebar-overlay.is-open {
            pointer-events: auto;
        }

        .message-user {
            align-self: flex-end;
            background: #2563eb;
            color: #ffffff;
            border-radius: 18px 18px 4px 18px;
            padding: 12px 18px !important;
            margin-bottom: 12px;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.08);
            max-width: 85%;
        }
        .dark-mode .message-user {
            background: #1d4ed8;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.25);
        }

        .message-assistant {
            align-self: flex-start;
            background: rgba(255, 255, 255, 0.9) !important;
            color: var(--text-primary);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.02);
            border: 1px solid rgba(0, 0, 0, 0.06) !important;
            border-radius: 18px 18px 18px 4px;
            padding: 12px 18px !important;
            margin-bottom: 16px;
            width: 100%;
            max-width: 85%;
        }
        .dark-mode .message-assistant {
            background: rgba(30, 41, 59, 0.7) !important;
            border: 1px solid rgba(255, 255, 255, 0.05) !important;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
        }

        /* Markdown lists & paragraphs spacing */
        .message-assistant p {
            margin-bottom: 12px;
        }
        .message-assistant p:last-child {
            margin-bottom: 0;
        }
        .message-assistant ul, .message-assistant ol {
            margin-left: 20px;
            margin-bottom: 12px;
        }
        .message-assistant pre {
            margin: 12px 0;
            border-radius: 12px;
            overflow: hidden;
        }
        .message-assistant code {
            font-family: Consolas, Monaco, monospace;
            font-size: 0.9rem;
            background: rgba(0, 0, 0, 0.05);
            padding: 2px 6px;
            border-radius: 6px;
        }
        .dark-mode .message-assistant code {
            background: rgba(255, 255, 255, 0.08);
        }
        .message-assistant pre code {
            display: block;
            padding: 16px;
            overflow-x: auto;
            background: #1e1e2e;
            color: #cdd6f4;
        }

        /* Inset list active indicator */
        .history-item {
            padding: 12px 16px;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            margin-bottom: 8px;
            transition: all 0.2s ease;
            text-decoration: none;
            color: var(--text-primary);
            border: 1px solid transparent;
        }
        .history-item:hover {
            background: rgba(255, 255, 255, 0.4);
            transform: translateX(3px);
        }
        .dark-mode .history-item:hover {
            background: rgba(255, 255, 255, 0.05);
        }
        .history-item.active {
            background: var(--clay-card-bg);
            border: 1px solid var(--clay-card-border);
            box-shadow: var(--clay-outer-shadow);
        }

        /* Modals (Frosted Glass Clay style) */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(15, 18, 29, 0.4);
            backdrop-filter: blur(8px);
            z-index: 100;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .modal-card {
            width: 100%;
            max-width: 550px;
            max-height: 90vh;
            overflow-y: auto;
        }

        /* Attachment chips */
        .attach-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--clay-card-bg);
            border: 1px solid var(--clay-card-border);
            padding: 6px 12px;
            border-radius: 14px;
            font-size: 0.85rem;
            box-shadow: var(--clay-outer-shadow);
        }

        /* Auto-expandable textarea logic helper */
        .chat-textarea {
            resize: none;
            overflow-y: hidden;   /* hides scroll until max-height is hit */
            max-height: 160px;
            line-height: 1.6;
            scrollbar-width: none;        /* Firefox */
            -ms-overflow-style: none;     /* IE/Edge */
        }
        .chat-textarea::-webkit-scrollbar {
            display: none;  /* Chrome/Safari */
        }
        /* When content exceeds max-height JS adds this class to allow scrolling */
        .chat-textarea.overflowed {
            overflow-y: auto;
        }
        /* Spin animation for upload spinner */
        @keyframes spin {
            from { transform: rotate(0deg); }
            to   { transform: rotate(360deg); }
        }

        /* Premium Table Styles */
        .message-assistant table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin: 16px 0;
            border-radius: 14px;
            overflow: hidden;
            border: 1px solid var(--clay-card-border);
            box-shadow: var(--clay-outer-shadow);
        }
        .message-assistant th, .message-assistant td {
            padding: 12px 16px;
            text-align: left;
        }
        .message-assistant th {
            background: rgba(0, 0, 0, 0.03);
            font-weight: 600;
            border-bottom: 1px solid var(--clay-card-border);
            color: var(--text-primary);
        }
        .dark-mode .message-assistant th {
            background: rgba(255, 255, 255, 0.03);
        }
        .message-assistant td {
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            background: rgba(255, 255, 255, 0.2);
            color: var(--text-primary);
        }
        .dark-mode .message-assistant td {
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            background: rgba(0, 0, 0, 0.1);
        }
        .message-assistant tr:last-child td {
            border-bottom: none;
        }
    </style>
</head>
<body x-data="chatApp()" x-init="initApp()" :class="{ 'dark-mode': darkMode }">
    <div class="glow-sphere glow-1"></div>
    <div class="glow-sphere glow-2"></div>

    <!-- Sidebar overlay for Mobile view -->
    <!-- Sits outside .workspace so it covers the full viewport without layout interference -->
    <div
        class="sidebar-overlay"
        :class="{ 'is-open': sidebarOpen }"
        x-show="sidebarOpen"
        @click="sidebarOpen = false"
        style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.5); z-index:40; cursor:pointer;"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    ></div>

    <div class="workspace">
        <!-- Sidebar Container -->
        <aside class="sidebar" :class="{ 'open': sidebarOpen, 'collapsed': sidebarCollapsed }">
            <!-- Sidebar header: logo + collapse + close buttons -->
            <div :style="sidebarCollapsed ? 'display:flex; flex-direction:column; align-items:center; gap:12px; width:100%;' : 'display: flex; align-items: center; justify-content: space-between;'" style="flex-shrink:0;">
                <a href="{{ route('chat') }}" class="app-brand" style="overflow:hidden; white-space:nowrap; padding:0; margin:0; display:flex; align-items:center; gap:8px;">
                    @php 
                        $lightLogo = \App\Models\SystemSetting::get('general_logo_light') ?: \App\Models\SystemSetting::get('general_chatbot_logo'); 
                        $darkLogo = \App\Models\SystemSetting::get('general_logo_dark') ?: \App\Models\SystemSetting::get('general_chatbot_logo'); 
                    @endphp
                    @if($lightLogo || $darkLogo)
                        <img :src="darkMode ? '{{ $darkLogo ?: $lightLogo }}' : '{{ $lightLogo ?: $darkLogo }}'" alt="Logo" style="width:32px; height:32px; border-radius:8px; object-fit:contain; flex-shrink:0;">
                    @else
                        <div class="app-brand-icon" style="flex-shrink:0;">{{ substr(\App\Models\SystemSetting::get('general_chatbot_name', 'XrootAI'), 0, 1) }}</div>
                    @endif
                    <span class="sidebar-text">{{ \App\Models\SystemSetting::get('general_chatbot_name', 'XrootAI') }}</span>
                </a>
                <div :style="sidebarCollapsed ? 'display:flex; justify-content:center;' : 'display:flex; gap:4px;'" style="flex-shrink:0;">
                    <!-- Collapse toggle (desktop only) -->
                    <button @click="sidebarCollapsed = !sidebarCollapsed; localStorage.setItem('sidebarCollapsed', sidebarCollapsed)" class="clay-btn clay-btn-secondary desktop-collapse-btn" style="border-radius:50%; width:32px; height:32px; padding:0; display:flex; align-items:center; justify-content:center; flex-shrink:0;" :title="sidebarCollapsed ? 'Expand sidebar' : 'Collapse sidebar'">
                        <template x-if="!sidebarCollapsed">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                        </template>
                        <template x-if="sidebarCollapsed">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                        </template>
                    </button>
                    <!-- Close button: only on mobile -->
                    <button @click="sidebarOpen = false" class="clay-btn clay-btn-secondary sidebar-close-btn" style="border-radius:50%; width:32px; height:32px; padding:0; align-items: center; justify-content: center;" title="Close sidebar">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                </div>
            </div>

            <!-- New Chat Puffed Gradient Button -->
            <form action="{{ route('chats.store') }}" method="POST" style="display:block; width:100%; flex-shrink:0;">
                @csrf
                <input type="hidden" name="model" :value="activeModel">
                <button type="submit" class="btn-new-chat" :title="sidebarCollapsed ? 'New Chat' : ''">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    <span class="new-chat-text">New Chat</span>
                </button>
            </form>

            <!-- Search + Conversations: only visible to logged-in users -->
            @auth
            <!-- Search bar -->
            <input type="text" class="clay-inset clay-input sidebar-search" placeholder="Search conversations..." x-model="searchQuery" style="width:100%;">

            <!-- Conversations list (Grouped by date) -->
            <div class="sidebar-history" style="flex: 1 1 0; min-height: 0; overflow-y: auto; margin-top: 10px; padding-right: 5px;">
                <template x-for="group in groupedConversations()" :key="group.label">
                    <div style="margin-bottom: 20px;">
                        <!-- Group Header divider -->
                        <div style="font-size: 0.72rem; font-weight: 700; text-transform: uppercase; color: var(--text-muted); margin-bottom: 8px; padding-left: 8px; letter-spacing: 0.05em;" x-text="group.label"></div>
                        
                        <template x-for="chat in group.items" :key="chat.uuid">
                            <div class="history-item" :class="{ 'active': activeUuid === chat.uuid }">
                                <!-- Normal Mode: Title Link -->
                                <template x-if="renamingUuid !== chat.uuid">
                                    <a :href="'/chats/' + chat.uuid" style="text-decoration:none; color:inherit; flex-grow:1; text-overflow:ellipsis; overflow:hidden; white-space:nowrap; font-size:0.95rem; font-weight: 500; display:block; margin-right:6px;">
                                        <span x-text="chat.title"></span>
                                    </a>
                                </template>

                                <!-- Rename Mode: Inline Input -->
                                <template x-if="renamingUuid === chat.uuid">
                                    <div style="display:flex; align-items:center; gap:6px; flex-grow:1; margin-right:6px;" @click.stop>
                                        <input 
                                            :id="'rename-input-' + chat.uuid"
                                            type="text" 
                                            x-model="chat._editTitle" 
                                            @keydown.enter.prevent="saveRename(chat)" 
                                            @keydown.escape.prevent="cancelRename(chat)"
                                            @blur="saveRename(chat)"
                                            class="clay-inset" 
                                            style="width:100%; padding:4px 8px; font-size:0.88rem; border-radius:8px;"
                                        />
                                    </div>
                                </template>
                                
                                <div class="history-item-actions" x-show="renamingUuid !== chat.uuid">
                                    <!-- Rename toggle -->
                                    <button @click.prevent.stop="renameChat(chat)" class="action-icon-btn" title="Rename Chat">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"/></svg>
                                    </button>
                                    <!-- Pin toggle -->
                                    <button @click.prevent.stop="togglePin(chat)" class="action-icon-btn" :title="chat.pinned_at ? 'Unpin' : 'Pin'">
                                        <!-- Pinned: filled pin -->
                                        <template x-if="chat.pinned_at">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M16 3a1 1 0 0 1 .707 1.707L15.414 6l1.293 5.172 2.586 1.121A1 1 0 0 1 20 13.28V14a1 1 0 0 1-1 1h-6v6a1 1 0 0 1-2 0v-6H5a1 1 0 0 1-1-1v-.72a1 1 0 0 1 .707-.987l2.586-1.121L8.586 6 7.293 4.707A1 1 0 0 1 8 3h8z"/></svg>
                                        </template>
                                        <!-- Unpinned: outline pin -->
                                        <template x-if="!chat.pinned_at">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="17" x2="12" y2="22"/><path d="M5 17h14v-1.76a2 2 0 0 0-1.11-1.79l-1.78-.9A2 2 0 0 1 15 10.76V6h1a2 2 0 0 0 0-4H8a2 2 0 0 0 0 4h1v4.76a2 2 0 0 1-1.11 1.79l-1.78.9A2 2 0 0 0 5 15.24V17z"/></svg>
                                        </template>
                                    </button>
                                    <!-- Delete button -->
                                    <button @click.prevent.stop="deleteChat(chat)" class="action-icon-btn btn-delete" title="Delete">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>
            </div>
            @endauth

            {{-- Guest: show nothing in the middle list, footer will show Guest limitations and Login --}}
            @guest
            {{-- Sign-in instructions and duplicate Sign-in buttons completely removed --}}
            @endguest

            <!-- User footer menu -->
            <div :class="sidebarCollapsed ? '' : 'clay-card'" :style="sidebarCollapsed ? 'background:transparent; border:none; box-shadow:none; padding:0; display:flex; justify-content:center; margin-top:auto;' : 'padding: 12px 16px; border-radius: 20px; display: flex; align-items: center; justify-content: space-between; margin-top: auto; flex-shrink: 0; box-shadow: var(--clay-outer-shadow);'">
                @auth
                    <template x-if="sidebarCollapsed">
                        <div style="display:flex; flex-direction:column; align-items:center; gap:12px; width:100%;">
                            <!-- Avatar button opens settings modal -->
                            <button @click="openSettings()" class="clay-btn" style="width: 36px; height: 36px; border-radius: 50%; background: #4a88ff; color: white; display: flex; align-items: center; justify-content: center; font-weight: bold; padding:0; border:none;" title="Profile & Settings">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </button>
                            <!-- Logout -->
                            <form action="{{ route('logout') }}" method="POST" style="margin: 0; display: flex;">
                                @csrf
                                <button type="submit" class="clay-btn" style="background:none; border:none; padding:4px; cursor:pointer; color:var(--text-muted); display:flex; align-items:center; justify-content:center; width:28px; height:28px; border-radius:8px;" title="Logout">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                                </button>
                            </form>
                        </div>
                    </template>
                    <template x-if="!sidebarCollapsed">
                        <div style="display: flex; flex-direction: column; gap: 8px; width: 100%;">
                            <div style="display: flex; align-items: center; justify-content: space-between; gap: 10px; overflow:hidden;">
                                <div style="display: flex; align-items: center; gap: 10px; overflow:hidden; flex-grow: 1;">
                                    <div style="width: 36px; height: 36px; border-radius: 50%; background: #4a88ff; color: white; display: flex; align-items: center; justify-content: center; font-weight: bold; flex-shrink: 0;">
                                        {{ substr(Auth::user()->name, 0, 1) }}
                                    </div>
                                    <div style="overflow:hidden; text-overflow:ellipsis; white-space:nowrap; font-size: 0.9rem; flex-grow: 1;">
                                        <div style="font-weight: 600; text-overflow:ellipsis; overflow:hidden; white-space:nowrap;">{{ Auth::user()->name }}</div>
                                        <div style="font-size: 0.75rem; color: var(--text-muted); text-overflow:ellipsis; overflow:hidden; white-space:nowrap;">{{ Auth::user()->email }}</div>
                                    </div>
                                </div>
                                
                                <div style="display: flex; gap: 4px; flex-shrink: 0;">
                                    <a href="{{ route('profile.security') }}" class="clay-btn clay-btn-secondary" style="border-radius: 50%; width:30px; height:30px; padding:0; display:flex; align-items:center; justify-content:center; text-decoration:none;" title="Account Security & 2FA">
                                        🛡️
                                    </a>
                                    <button @click="openSettings()" class="clay-btn clay-btn-secondary" style="border-radius: 50%; width:30px; height:30px; padding:0; display:flex; align-items:center; justify-content:center;" title="Settings">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                                    </button>
                                    <form action="{{ route('logout') }}" method="POST" style="margin: 0; display: inline-flex;">
                                        @csrf
                                        <button type="submit" class="clay-btn" style="background:none; border:none; padding:4px; cursor:pointer; color:var(--text-muted); display:flex; align-items:center; justify-content:center; width:28px; height:28px; border-radius:8px;" title="Logout">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </template>
                @endauth
                @guest
                    <template x-if="sidebarCollapsed">
                        <a href="{{ route('login') }}" class="clay-btn clay-btn-primary" style="width: 44px; height: 44px; border-radius: 50%; padding:0; display:flex; align-items:center; justify-content:center;" title="Sign In">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                        </a>
                    </template>
                    <template x-if="!sidebarCollapsed">
                        <div style="display: flex; flex-direction: column; gap: 10px; width: 100%;">
                            <div style="font-size: 0.8rem; color: var(--text-muted); text-align: center; font-weight: 600;">Guest Mode ({{ \App\Models\SystemSetting::get('plans_guest_messages_per_session', 5) }} messages limit)</div>
                            <div style="display: flex; gap: 8px;">
                                <a href="{{ route('login') }}" class="clay-btn clay-btn-secondary" style="flex: 1; font-size: 0.82rem; padding: 8px 10px; border-radius: 12px; text-decoration: none; text-align: center;">Login</a>
                                <a href="{{ route('register') }}" class="clay-btn clay-btn-primary" style="flex: 1; font-size: 0.82rem; padding: 8px 10px; border-radius: 12px; text-decoration: none; text-align: center;">Register</a>
                            </div>
                        </div>
                    </template>
                @endguest
            </div>
        </aside>

        <!-- Main Chat Pane -->
        <main class="chat-pane">
            
            <!-- Header -->
            <header class="chat-header">
                <div style="display: flex; align-items: center; gap: 12px;">
                <!-- Hamburger button: only visible on mobile via CSS class -->
                    <button @click="sidebarOpen = true" class="clay-btn clay-btn-secondary mobile-menu-btn" style="border-radius: 12px; height:40px; padding: 0 14px; align-items: center; justify-content: center; gap:4px;" aria-label="Open sidebar">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                    </button>

                    <!-- Mobile: logo instead of model name -->
                    <div class="header-mobile-logo" style="display:flex; align-items:center; gap:8px;">
                        @if($lightLogo || $darkLogo)
                            <img :src="darkMode ? '{{ $darkLogo ?: $lightLogo }}' : '{{ $lightLogo ?: $darkLogo }}'" alt="Logo" style="width:28px; height:28px; border-radius:8px; object-fit:contain;">
                        @else
                            <div style="width:28px; height:28px; border-radius:10px; background:linear-gradient(135deg,#4a88ff,#56ab2f); display:flex; align-items:center; justify-content:center; color:white; font-weight:800; font-size:0.85rem;">{{ substr(\App\Models\SystemSetting::get('general_chatbot_name', 'XrootAI'), 0, 1) }}</div>
                        @endif
                        <span>{{ \App\Models\SystemSetting::get('general_chatbot_name', 'XrootAI') }}</span>
                    </div>

                    <!-- Desktop: active model indicator -->
                    <div class="header-model-label" style="font-weight: 600; font-size: 1.05rem;">
                        <span x-text="getModelName(activeModel)"></span>
                    </div>
                </div>

                <!-- Model selection + theme toggle + settings -->
                <div style="display: flex; flex-shrink: 0; align-items: center; gap: 8px;">
                    <select class="clay-inset" x-model="activeModel" @change="checkModelAccess($event)" style="padding: 8px 16px; border-radius: 16px; font-weight: 500; font-size: 0.9rem; max-width: 260px;">
                        <template x-for="model in availableModels" :key="model.id">
                            <option :value="model.id" x-text="model.name" :selected="model.id === activeModel"></option>
                        </template>
                    </select>

                    <!-- Dark/Light mode toggle -->
                    <button @click="toggleDarkMode()" class="clay-btn clay-btn-secondary theme-toggle-btn" title="Toggle dark/light mode">
                        <template x-if="darkMode">
                            <!-- Sun icon (switch to light) -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>
                        </template>
                        <template x-if="!darkMode">
                            <!-- Moon icon (switch to dark) -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                        </template>
                    </button>

                    @auth
                    {{-- Settings button: only shown to authenticated users --}}
                    <button @click="openSettings()" class="clay-btn clay-btn-secondary" style="border-radius: 50%; width:40px; height:40px; padding:0; display:flex; align-items:center; justify-content:center;" title="Settings">
                        <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                    </button>
                    @endauth
                </div>
            </header>

            <!-- Chat messages box (Phase 5 + Phase 6 attachments) -->
            <div class="chat-messages" id="chat-messages-container" x-ref="messageContainer">
                
                <!-- If no conversation is active -->
                <template x-if="messages.length === 0">
                    <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; text-align: center; max-width: 500px; margin: 0 auto; gap: 20px;">
                        <div style="width:72px; height:72px; border-radius:24px; background:linear-gradient(135deg,#4a88ff,#56ab2f); display:flex; align-items:center; justify-content:center; box-shadow:0 8px 32px rgba(74,136,255,0.3);">
                            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2a5 5 0 1 0 0 10A5 5 0 0 0 12 2z"/><path d="M3 20a9 9 0 0 1 18 0"/><circle cx="12" cy="12" r="10" opacity=".15" fill="white"/></svg>
                        </div>
                        <h2 style="font-weight: 700; font-size: 1.8rem; background: linear-gradient(135deg, #4a88ff 0%, #56ab2f 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">{{ \App\Models\SystemSetting::get('general_welcome_message', 'What can I help you build today?') }}</h2>
                        
                        <div style="display: flex; flex-wrap: wrap; gap: 10px; justify-content: center; margin-top: 10px;">
                            @php
                                $suggestedQs = array_filter(array_map('trim', explode("\n", \App\Models\SystemSetting::get('ux_suggested_questions', ''))));
                            @endphp
                            @foreach($suggestedQs as $q)
                                <button @click="prompt = '{{ addslashes($q) }}'; $nextTick(() => sendMessage())" class="clay-btn clay-btn-secondary" style="border-radius:16px; font-size:0.85rem;">
                                    "{{ $q }}"
                                </button>
                            @endforeach
                        </div>
                    </div>
                </template>

                <!-- Render active messages list -->
                <template x-for="(msg, index) in messages" :key="index">
                    <div class="message-bubble" :class="msg.role === 'user' ? 'message-user' : 'message-assistant'">
                        <template x-if="msg.role !== 'user'">
                            <div style="display: flex; gap: 14px; width: 100%; align-items: flex-start;">
                                <!-- AI Avatar -->
                                <div style="width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, #4a88ff, #56ab2f); color: white; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.8rem; flex-shrink: 0; box-shadow: 0 2px 8px rgba(74,136,255,0.2); overflow: hidden;">
                                    <img src="{{ \App\Models\SystemSetting::get('general_site_icon') ?: \App\Models\SystemSetting::get('general_chatbot_logo') ?: '/favicon.ico' }}" alt="Icon" style="width: 20px; height: 20px; object-fit: contain;" onerror="this.style.display='none'; this.parentNode.innerText='🤖'" />
                                </div>
                                <div style="flex-grow: 1; min-width: 0; display: flex; flex-direction: column; position: relative;">
                                    <!-- Assistant actions at Top Right: copy & regenerate icons only -->
                                    <div style="display: flex; justify-content: flex-end; gap: 6px; margin-bottom: 4px;">
                                        <button
                                            type="button"
                                            @click="copyMessage(msg.content, $event)"
                                            class="clay-btn clay-btn-secondary"
                                            style="padding: 4px 8px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; min-width: 28px; height: 28px;"
                                            title="Copy to clipboard"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                                        </button>

                                        <button
                                            type="button"
                                            @click="regenerateMessage(index)"
                                            class="clay-btn clay-btn-secondary"
                                            style="padding: 4px 8px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; min-width: 28px; height: 28px;"
                                            title="Regenerate response"
                                            :disabled="isStreaming"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
                                        </button>
                                    </div>

                                    <!-- Attachments preview row with visual thumbnails -->
                                    <template x-if="msg.attachments && msg.attachments.length > 0">
                                        <div style="display: flex; flex-wrap: wrap; gap: 8px; padding: 4px 0 8px;">
                                            <template x-for="file in msg.attachments" :key="file.id">
                                                <a :href="'/storage/' + file.file_path" target="_blank" class="attach-pill" style="opacity: 0.95; padding: 6px 12px; display: flex; align-items: center; gap: 8px; text-decoration: none; color: inherit;">
                                                    <template x-if="file.mime_type && file.mime_type.startsWith('image/')">
                                                        <img :src="'/storage/' + file.file_path" style="width: 28px; height: 28px; object-fit: cover; border-radius: 6px;" />
                                                    </template>
                                                    <template x-if="!file.mime_type || !file.mime_type.startsWith('image/')">
                                                        <span style="display:flex; align-items:center; color:var(--text-muted);" x-html="getFileIcon(file.mime_type)"></span>
                                                    </template>
                                                    <span x-text="file.file_name" style="font-weight: 500; font-size: 0.82rem; text-decoration: underline;"></span>
                                                </a>
                                            </template>
                                        </div>
                                    </template>

                                    <!-- Body text: collapsible if tall -->
                                    <div
                                        class="msg-body"
                                        :class="{ 'collapsed': !msg._expanded && isLongMessage(msg.content) }"
                                        x-data="{ ready: false }"
                                        x-init="$nextTick(() => ready = true)"
                                    >
                                        <div class="msg-content" style="padding:0;" x-html="renderMessageContent(msg.content)"></div>
                                    </div>

                                    <!-- Expand / Collapse toggle -->
                                    <template x-if="isLongMessage(msg.content)">
                                        <div style="margin-top: 8px;">
                                            <button
                                                type="button"
                                                @click="msg._expanded = !msg._expanded"
                                                class="clay-btn clay-btn-secondary"
                                                style="padding: 4px 12px; font-size: 0.78rem; border-radius: 12px; display: inline-flex; align-items: center; gap: 6px;"
                                            >
                                                <template x-if="!msg._expanded">
                                                    <span style="display:flex;align-items:center;gap:5px;">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                                                        Show more
                                                    </span>
                                                </template>
                                                <template x-if="msg._expanded">
                                                    <span style="display:flex;align-items:center;gap:5px;">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="18 15 12 9 6 15"/></svg>
                                                        Show less
                                                    </span>
                                                </template>
                                            </button>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>

                        <template x-if="msg.role === 'user'">
                            <div style="display: flex; flex-direction: column; align-items: flex-end; width: 100%;">
                                <!-- User Attachments preview row -->
                                <template x-if="msg.attachments && msg.attachments.length > 0">
                                    <div style="display: flex; flex-wrap: wrap; gap: 8px; justify-content: flex-end; padding: 0 0 8px;">
                                        <template x-for="file in msg.attachments" :key="file.id">
                                            <a :href="'/storage/' + file.file_path" target="_blank" class="attach-pill" style="background: rgba(255,255,255,0.18); border: 1px solid rgba(255,255,255,0.3); color: white; padding: 6px 12px; display: flex; align-items: center; gap: 8px; text-decoration: none;">
                                                <template x-if="file.mime_type && file.mime_type.startsWith('image/')">
                                                    <img :src="'/storage/' + file.file_path" style="width: 28px; height: 28px; object-fit: cover; border-radius: 6px;" />
                                                </template>
                                                <template x-if="!file.mime_type || !file.mime_type.startsWith('image/')">
                                                    <span style="display:flex; align-items:center; color:rgba(255,255,255,0.9);" x-html="getFileIcon(file.mime_type)"></span>
                                                </template>
                                                <span x-text="file.file_name" style="font-weight: 500; font-size: 0.82rem; text-decoration: underline;"></span>
                                            </a>
                                        </template>
                                    </div>
                                </template>

                                <!-- Collapsible User message content -->
                                <div
                                    class="msg-body"
                                    :class="{ 'collapsed': !msg._expanded && isLongMessage(msg.content) }"
                                    style="text-align: right;"
                                    x-data="{ ready: false }"
                                    x-init="$nextTick(() => { ready = true })"
                                    :style="ready ? 'visibility: visible;' : 'visibility: hidden;'"
                                >
                                    <div class="msg-content" style="padding:0; white-space: pre-wrap;" x-text="msg.content"></div>
                                </div>
                                <template x-if="isLongMessage(msg.content)">
                                    <button
                                        @click="msg._expanded = !msg._expanded"
                                        class="clay-btn clay-btn-secondary"
                                        style="justify-content: flex-end; padding: 8px 0 0; color: rgba(255,255,255,0.85);"
                                    >
                                        <template x-if="!msg._expanded">
                                            <span style="display:flex;align-items:center;gap:5px;">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                                                Show more
                                            </span>
                                        </template>
                                        <template x-if="msg._expanded">
                                            <span style="display:flex;align-items:center;gap:5px;">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="18 15 12 9 6 15"/></svg>
                                                Show less
                                            </span>
                                        </template>
                                    </button>
                                </template>
                            </div>
                        </template>
                    </div>
                </template>

                <!-- Streaming active assistant bubble -->
                <template x-if="isStreaming && activeStreamText.length > 0">
                    <div class="message-bubble message-assistant">
                        <div style="display: flex; gap: 14px; width: 100%; align-items: flex-start;">
                            <!-- AI Avatar -->
                            <div style="width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, #4a88ff, #56ab2f); color: white; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.8rem; flex-shrink: 0; box-shadow: 0 2px 8px rgba(74,136,255,0.2); overflow: hidden;">
                                <img src="{{ \App\Models\SystemSetting::get('general_site_icon') ?: \App\Models\SystemSetting::get('general_chatbot_logo') ?: '/favicon.ico' }}" alt="Icon" style="width: 20px; height: 20px; object-fit: contain;" onerror="this.style.display='none'; this.parentNode.innerText='🤖'" />
                            </div>
                            <div style="flex-grow: 1; min-width: 0;">
                                <div class="msg-content" style="padding:0;" x-html="renderMessageContent(activeStreamText)"></div>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- AI Fast Loading Card Animation before reply -->
                <template x-if="isStreaming && activeStreamText.length === 0">
                    <div class="ai-fast-loading-card">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div class="ai-spinner-avatar" style="overflow: hidden;">
                                <img src="{{ \App\Models\SystemSetting::get('general_site_icon') ?: \App\Models\SystemSetting::get('general_chatbot_logo') ?: '/favicon.ico' }}" alt="Icon" style="width: 22px; height: 22px; object-fit: contain;" onerror="this.style.display='none'; this.parentNode.innerText='🤖'" />
                            </div>
                            <div style="display: flex; flex-direction: column; gap: 2px;">
                                <div style="font-weight: 700; font-size: 0.95rem; background: linear-gradient(135deg, #4a88ff, #6b52ff); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Synthesizing response...</div>
                            </div>
                        </div>
                        <div style="display: flex; flex-direction: column; gap: 8px; width: 100%; padding-left: 46px;">
                            <div class="ai-shimmer-line" style="width: 85%;"></div>
                            <div class="ai-shimmer-line" style="width: 65%;"></div>
                            <div class="ai-shimmer-line" style="width: 40%;"></div>
                        </div>
                    </div>
                </template>
                
                <div x-ref="bottomScrollAnchor"></div>
            </div>

            <!-- Input Tray -->
            <footer class="chat-input-area">
                
                <!-- Upload status & preview bar with rich previews -->
                <div style="display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 10px;" x-show="attachments.length > 0 || isUploading">
                    <template x-for="(file, idx) in attachments" :key="idx">
                        <div class="attach-pill" style="padding: 6px 12px; display: flex; align-items: center; gap: 8px;">
                            <template x-if="file.mime_type && file.mime_type.startsWith('image/')">
                                <img :src="'/storage/' + file.file_path" style="width: 24px; height: 24px; object-fit: cover; border-radius: 4px;" />
                            </template>
                            <template x-if="!file.mime_type || !file.mime_type.startsWith('image/')">
                                <span style="display:flex; align-items:center; color:var(--text-muted);" x-html="getFileIcon(file.mime_type)"></span>
                            </template>
                            <span x-text="file.file_name" style="font-weight: 500; font-size: 0.82rem;"></span>
                            <button @click="removeAttachment(idx)" style="background:none; border:none; color:var(--text-muted); cursor:pointer; margin-left:4px; display:flex; align-items:center; justify-content:center; padding:2px;" title="Remove attachment">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                            </button>
                        </div>
                    </template>
                    <div x-show="isUploading" class="attach-pill" style="border-style:dashed; display:flex; align-items:center; gap:6px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="animation:spin 1s linear infinite;"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
                        Uploading...
                    </div>
                </div>

                <!-- Input form bubble -->
                <div class="clay-card" style="padding: 10px 16px; border-radius: 24px; display: flex; align-items: flex-end; gap: 12px;">
                    
                    <!-- Paperclip upload button -->
                    @if(\App\Models\SystemSetting::get('toggle_file_upload', true) && (Auth::check() ? \App\Models\SystemSetting::get('plans_pro_file_upload', true) : \App\Models\SystemSetting::get('plans_guest_file_upload', true)))
                    <div style="position: relative;">
                        <button @click="$refs.fileInput.click()" class="clay-btn clay-btn-secondary" style="border-radius: 50%; width: 44px; height: 44px; padding:0; display:flex; align-items:center; justify-content:center; flex-shrink:0;" title="Attach File">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"/></svg>
                        </button>
                        <input type="file" x-ref="fileInput" @change="handleFileUpload($event)" style="display: none;">
                    </div>
                    @endif

                    <!-- Main auto-expanding text input -->
                    <textarea 
                        class="clay-inset chat-textarea" 
                        placeholder="Ask {{ \App\Models\SystemSetting::get('general_chatbot_name', 'XrootAI') }} anything… (Shift+Enter for new line)"
                        x-model="prompt"
                        @keydown="handleTextareaKeydown($event)"
                        x-ref="promptInput"
                        rows="1"
                        style="flex-grow: 1; border: none; background: transparent; box-shadow: none; padding: 10px 0; font-size: 1.05rem;"
                    ></textarea>

                    <!-- Send button -->
                    <button 
                        @click="sendMessage()" 
                        class="clay-btn clay-btn-primary" 
                        style="border-radius: 50%; width: 44px; height: 44px; padding:0; display:flex; align-items:center; justify-content:center; flex-shrink: 0;"
                        :disabled="isStreaming || (!prompt.trim() && attachments.length === 0)"
                        title="Send (Enter)"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                    </button>
                </div>

                <!-- Legal & Disclaimer Notice -->
                <div style="text-align: center; margin-top: 8px; font-size: 0.76rem; color: var(--text-muted);">
                    <span>AI completions can make mistakes. Verify important info. &bull; <a href="{{ route('privacy') }}" target="_blank" style="color: inherit; text-decoration: underline;">Privacy</a> &bull; <a href="{{ route('terms') }}" target="_blank" style="color: inherit; text-decoration: underline;">Terms</a></span>
                </div>
            </footer>
        </main>

        <!-- Settings Modal: Profile only — App Settings and API Keys removed from user view -->
        {{-- Only rendered/opened for authenticated users; guests cannot reach this --}}
        @auth
        <div class="modal-overlay" x-show="settingsModalOpen" x-transition.opacity style="display: none;">
            <div class="clay-card modal-card" @click.away="settingsModalOpen = false">
                <div style="display:flex; justify-content:between; align-items:center; margin-bottom: 24px; border-bottom: 1px solid var(--clay-card-border); padding-bottom:12px;">
                    <h2 style="font-weight: 700; font-size: 1.4rem;">Profile</h2>
                    <button @click="settingsModalOpen = false" class="clay-btn clay-btn-secondary" style="border-radius:50%; width:32px; height:32px; padding:0; display:flex; align-items:center; justify-content:center; margin-left:auto;" title="Close">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                </div>

                <!-- Logged-in user info -->
                <div style="display:flex; align-items:center; gap:16px; padding:16px; border-radius:16px; background:rgba(74,136,255,0.06); border:1px solid rgba(74,136,255,0.15); margin-bottom:20px;">
                    <div style="width:52px; height:52px; border-radius:50%; background:linear-gradient(135deg,#4a88ff,#56ab2f); color:white; display:flex; align-items:center; justify-content:center; font-weight:800; font-size:1.4rem; flex-shrink:0;">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                    <div>
                        <div style="font-weight:700; font-size:1rem;">{{ Auth::user()->name }}</div>
                        <div style="font-size:0.82rem; color:var(--text-muted);">{{ Auth::user()->email }}</div>
                    </div>
                </div>

                <!-- System Prompt only -->
                <form action="{{ route('settings.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="theme" :value="tempTheme">
                    <input type="hidden" name="default_model" :value="defaultModel">
                    <div class="clay-input-group">
                        <label class="clay-input-label">System Instructions</label>
                        <textarea name="system_prompt" class="clay-inset" rows="4" style="resize:none; width:100%;">{{ $settings->system_prompt }}</textarea>
                    </div>
                    <div style="display:flex; justify-content:flex-end; margin-top: 12px;">
                        <button type="submit" class="clay-btn clay-btn-primary">Save</button>
                    </div>
                </form>

                <!-- Security & 2FA Management -->
                <div style="margin-top:20px; padding-top:16px; border-top:1px solid var(--clay-card-border);">
                    <a href="{{ route('profile.security') }}" class="clay-btn" style="width:100%; background:rgba(59,130,246,0.08); color:#3b82f6; border:1px solid rgba(59,130,246,0.2); display:flex; align-items:center; justify-content:center; gap:8px; text-decoration:none; font-weight:600;">
                        🛡️ Account Security & 2FA
                    </a>
                </div>

                <!-- Logout -->
                <div style="margin-top:24px; padding-top:20px; border-top:1px solid var(--clay-card-border);">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="clay-btn" style="width:100%; background:rgba(239,68,68,0.08); color:#ef4444; border:1px solid rgba(239,68,68,0.2); display:flex; align-items:center; justify-content:center; gap:8px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                            Sign Out
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endauth

        <!-- Delete Confirmation Modal -->
        <div class="modal-overlay" x-show="deleteConfirmOpen" x-transition.opacity style="display:none; z-index:200;">
            <div class="clay-card" style="width:100%; max-width:420px; padding:32px;" @click.away="deleteConfirmOpen = false">
                <!-- Icon -->
                <div style="display:flex; justify-content:center; margin-bottom:20px;">
                    <div style="width:56px; height:56px; border-radius:18px; background:rgba(239,68,68,0.1); display:flex; align-items:center; justify-content:center;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                    </div>
                </div>
                <h3 style="font-weight:700; font-size:1.2rem; text-align:center; margin-bottom:8px;">Delete Conversation?</h3>
                <p style="color:var(--text-muted); font-size:0.9rem; text-align:center; line-height:1.6; margin-bottom:28px;">
                    This will permanently delete
                    <strong x-text="chatToDelete ? '&quot;' + chatToDelete.title + '&quot;' : 'this conversation'"></strong>.
                    This action cannot be undone.
                </p>
                <div style="display:flex; gap:12px;">
                    <button @click="deleteConfirmOpen = false" class="clay-btn clay-btn-secondary" style="flex:1; padding:10px;">
                        Cancel
                    </button>
                    <button @click="confirmDelete()" class="clay-btn" style="flex:1; padding:10px; background:linear-gradient(135deg,#ef4444,#dc2626); color:white; border:none; box-shadow:0 4px 12px rgba(239,68,68,0.3);">
                        Delete
                    </button>
                </div>
            </div>
        </div>

        <!-- Professional Claymorphic Alert / Notification Modal -->
        <div class="modal-overlay" x-show="proAlertModalOpen" x-transition.opacity style="display:none; z-index:300;">
            <div class="clay-card" style="width:100%; max-width:440px; padding:32px; border-radius:24px;" @click.away="proAlertModalOpen = false">
                <!-- Icon based on type -->
                <div style="display:flex; justify-content:center; margin-bottom:20px;">
                    <template x-if="proAlertType === 'warning' || proAlertType === 'upgrade'">
                        <div style="width:60px; height:60px; border-radius:20px; background:linear-gradient(135deg, rgba(245,158,11,0.15), rgba(217,119,6,0.25)); border:1px solid rgba(245,158,11,0.3); display:flex; align-items:center; justify-content:center; box-shadow:0 8px 24px rgba(245,158,11,0.2);">
                            <span style="font-size:1.8rem;">🔒</span>
                        </div>
                    </template>
                    <template x-if="proAlertType === 'error'">
                        <div style="width:60px; height:60px; border-radius:20px; background:linear-gradient(135deg, rgba(239,68,68,0.15), rgba(220,38,38,0.25)); border:1px solid rgba(239,68,68,0.3); display:flex; align-items:center; justify-content:center; box-shadow:0 8px 24px rgba(239,68,68,0.2);">
                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        </div>
                    </template>
                    <template x-if="proAlertType !== 'warning' && proAlertType !== 'upgrade' && proAlertType !== 'error'">
                        <div style="width:60px; height:60px; border-radius:20px; background:linear-gradient(135deg, rgba(74,136,255,0.15), rgba(99,102,241,0.25)); border:1px solid rgba(74,136,255,0.3); display:flex; align-items:center; justify-content:center; box-shadow:0 8px 24px rgba(74,136,255,0.2);">
                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#4a88ff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                        </div>
                    </template>
                </div>

                <h3 style="font-weight:800; font-size:1.3rem; text-align:center; margin-bottom:12px; letter-spacing:-0.01em;" x-text="proAlertTitle"></h3>
                <div style="color:var(--text-muted); font-size:0.92rem; text-align:center; line-height:1.6; margin-bottom:28px;" x-html="proAlertMessage"></div>

                <div style="display:flex; gap:12px;">
                    <button @click="proAlertModalOpen = false" class="clay-btn clay-btn-secondary" style="flex:1; padding:12px; font-weight:600;">
                        Dismiss
                    </button>
                    <template x-if="proAlertType === 'upgrade'">
                        <a href="{{ route('register') }}" class="clay-btn clay-btn-primary" style="flex:1.2; padding:12px; font-weight:700; text-align:center; text-decoration:none; background:linear-gradient(135deg, #4a88ff, #6b52ff); color:white;">
                            Upgrade Now
                        </a>
                    </template>
                </div>
            </div>
        </div>

    </div>

    <!-- Script variables mapped from Blade to client Alpine logic -->
    <script>
        function chatApp() {
            return {
                darkMode: localStorage.getItem('darkMode') === 'true',
                sidebarOpen: false,
                sidebarCollapsed: localStorage.getItem('sidebarCollapsed') === 'true',
                settingsModalOpen: false,
                deleteConfirmOpen: false,
                chatToDelete: null,
                renamingUuid: null,
                proAlertModalOpen: false,
                proAlertTitle: '',
                proAlertMessage: '',
                proAlertType: 'info',
                // Auth flag injected from server — guests cannot open settings
                isAuthenticated: @json(auth()->check()),
                
                // Active chat states
                activeUuid: @json($activeConversation ? $activeConversation->uuid : null),
                activeModel: @json($activeConversation ? $activeConversation->model : ($settings->default_model ?? 'mock')),
                defaultModel: @json($settings->default_model ?? 'mock'),
                tempTheme: @json($settings->theme ?? 'system'),
                
                availableModels: @json($models),
                conversationsList: @json($conversations),
                
                messages: @json($messages),
                prompt: '',
                isStreaming: false,
                activeStreamText: '',
                
                // Attachments
                attachments: [],
                isUploading: false,
                searchQuery: '',

                initApp() {
                    // Initialize expansion properties on loaded messages
                    this.messages = this.messages.map(m => ({ ...m, _expanded: false }));
                    this.conversationsList = (this.conversationsList || []).map(c => ({ ...c, _editTitle: c.title }));

                    // Automatically scroll to bottom on start if we have messages
                    this.scrollToBottom();

                    // Auto-adjust height of textarea — grows until max-height, then scrolls
                    const textarea = this.$refs.promptInput;
                    if (textarea) {
                        const MAX_HEIGHT = 160; // matches .chat-textarea max-height
                        const autoGrow = () => {
                            textarea.style.height = 'auto';
                            const newH = Math.min(textarea.scrollHeight, MAX_HEIGHT);
                            textarea.style.height = newH + 'px';
                            // Toggle scroll visibility class
                            if (textarea.scrollHeight > MAX_HEIGHT) {
                                textarea.classList.add('overflowed');
                            } else {
                                textarea.classList.remove('overflowed');
                            }
                        };
                        textarea.addEventListener('input', autoGrow);
                    }

                    // Toggle system theme if system option is picked
                    if (this.tempTheme === 'system') {
                        this.darkMode = window.matchMedia('(prefers-color-scheme: dark)').matches;
                    }

                    // Check for pending prompt and attachments (captured during new chat creation redirect)
                    const pendingPrompt = sessionStorage.getItem('pending_prompt');
                    const pendingAttachments = sessionStorage.getItem('pending_attachments');

                    if (pendingPrompt && this.activeUuid) {
                        this.prompt = pendingPrompt;
                        if (pendingAttachments) {
                            try {
                                this.attachments = JSON.parse(pendingAttachments);
                            } catch (e) {
                                console.error('Parse pending attachments failed:', e);
                            }
                        }
                        sessionStorage.removeItem('pending_prompt');
                        sessionStorage.removeItem('pending_attachments');
                        
                        this.$nextTick(() => {
                            this.sendMessage();
                        });
                    }
                },

                filteredConversations() {
                    if (!this.searchQuery) return this.conversationsList;
                    return this.conversationsList.filter(chat => 
                        chat.title.toLowerCase().includes(this.searchQuery.toLowerCase())
                    );
                },

                groupedConversations() {
                    const list = this.filteredConversations();
                    const groups = {
                        pinned: [],
                        today: [],
                        yesterday: [],
                        week: [],
                        older: []
                    };

                    const now = new Date();
                    const startOfToday = new Date(now.getFullYear(), now.getMonth(), now.getDate());
                    const startOfYesterday = new Date(startOfToday.getTime() - 24 * 60 * 60 * 1000);
                    const startOfWeek = new Date(startOfToday.getTime() - 7 * 24 * 60 * 60 * 1000);

                    list.forEach(chat => {
                        if (chat.pinned_at) {
                            groups.pinned.push(chat);
                            return;
                        }

                        const date = new Date(chat.updated_at);
                        if (date >= startOfToday) {
                            groups.today.push(chat);
                        } else if (date >= startOfYesterday) {
                            groups.yesterday.push(chat);
                        } else if (date >= startOfWeek) {
                            groups.week.push(chat);
                        } else {
                            groups.older.push(chat);
                        }
                    });

                    return [
                        { label: 'Pinned Chats', items: groups.pinned },
                        { label: 'Today', items: groups.today },
                        { label: 'Yesterday', items: groups.yesterday },
                        { label: 'Previous 7 Days', items: groups.week },
                        { label: 'Older', items: groups.older }
                    ].filter(g => g.items.length > 0);
                },

                getFileIcon(mimeType) {
                    // Returns inline SVG string for file-type icon
                    const icon = (path) => `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">${path}</svg>`;
                    if (!mimeType) return icon('<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/>');
                    if (mimeType.includes('pdf'))           return icon('<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/>');
                    if (mimeType.includes('msword') || mimeType.includes('officedocument')) return icon('<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>');
                    if (mimeType.includes('excel') || mimeType.includes('sheet')) return icon('<rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="3" y1="15" x2="21" y2="15"/><line x1="9" y1="3" x2="9" y2="21"/><line x1="15" y1="3" x2="15" y2="21"/>');
                    if (mimeType.includes('zip') || mimeType.includes('compressed') || mimeType.includes('tar')) return icon('<path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>');
                    if (mimeType.includes('text/') || mimeType.includes('json') || mimeType.includes('javascript') || mimeType.includes('markdown')) return icon('<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><line x1="10" y1="9" x2="8" y2="9"/>');
                    if (mimeType.startsWith('audio/')) return icon('<path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/>');
                    if (mimeType.startsWith('video/')) return icon('<polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2" ry="2"/>');
                    return icon('<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/>');
                },

                getModelName(modelId) {
                    const model = this.availableModels.find(m => m.id === modelId);
                    return model ? model.name.replace(' 🔒 (Upgrade Plan)', '') : modelId;
                },

                showProAlert(title, message, type = 'info') {
                    this.proAlertTitle = title;
                    this.proAlertMessage = message;
                    this.proAlertType = type;
                    this.proAlertModalOpen = true;
                },

                checkModelAccess(event) {
                    const selected = this.availableModels.find(m => m.id === this.activeModel);
                    if (selected && selected.is_allowed === false) {
                        const cleanName = selected.name.replace(' 🔒 (Upgrade Plan)', '');
                        this.showProAlert(
                            'Plan Upgrade Required',
                            `You currently do not have permission to select <strong>"${cleanName}"</strong> under your current membership tier (${this.isAuthenticated ? 'Free/Basic Plan' : 'Guest Mode'}).<br><br>Please upgrade your membership plan or sign in with a higher tier account to unlock this AI model.`,
                            'upgrade'
                        );
                        const allowed = this.availableModels.find(m => m.is_allowed !== false);
                        this.activeModel = allowed ? allowed.id : 'mock';
                    }
                },

                // Toggle dark/light mode and persist
                toggleDarkMode() {
                    this.darkMode = !this.darkMode;
                    localStorage.setItem('darkMode', this.darkMode);
                    this.tempTheme = this.darkMode ? 'dark' : 'light';
                },

                // Open settings — guards against unauthenticated access
                openSettings() {
                    if (!this.isAuthenticated) {
                        window.location.href = '{{ route("login") }}';
                        return;
                    }
                    this.settingsModalOpen = true;
                },

                isLongMessage(content) {
                    // Treat as long if content exceeds ~800 chars or 12 newlines
                    if (!content) return false;
                    return content.length > 800 || (content.match(/\n/g) || []).length > 12;
                },

                renderMessageContent(content) {
                    if (!content) return '';
                    // Initialize markdown-it with highlight.js support
                    const md = window.markdownit({
                        html: false,
                        linkify: true,
                        typographer: true,
                        highlight: function (str, lang) {
                            if (lang && hljs.getLanguage(lang)) {
                                try {
                                    return '<pre class="hljs"><code>' +
                                           hljs.highlight(str, { language: lang, ignoreIllegals: true }).value +
                                           '</code></pre>';
                                } catch (__) {}
                            }
                            return '<pre class="hljs"><code>' + md.utils.escapeHtml(str) + '</code></pre>';
                        }
                    });
                    return md.render(content);
                },

                copyMessage(content, event) {
                    if (!content) return;
                    navigator.clipboard.writeText(content).then(() => {
                        const btn = event?.currentTarget;
                        if (btn) {
                            const originalHTML = btn.innerHTML;
                            btn.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#28a745" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>`;
                            setTimeout(() => {
                                btn.innerHTML = originalHTML;
                            }, 1500);
                        }
                    }).catch(() => {
                        this.showProAlert('Copy Failed', 'Unable to copy message to clipboard.', 'error');
                    });
                },

                regenerateMessage(index) {
                    if (this.isStreaming || !this.activeConversationId) return;
                    // Find the last user message before or at this assistant message index
                    let lastUserMessage = '';
                    for (let i = index - 1; i >= 0; i--) {
                        if (this.messages[i].role === 'user') {
                            lastUserMessage = this.messages[i].content;
                            break;
                        }
                    }
                    if (!lastUserMessage) return;
                    // Remove the assistant message (and any messages after it) to regenerate cleanly
                    this.messages.splice(index);
                    this.activeStreamText = '';
                    this.isStreaming = true;
                    this.scrollToBottom();

                    fetch(`/chats/${this.activeConversationId}/stream`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            model: this.activeModel,
                            message: lastUserMessage,
                            regenerate: true
                        })
                    }).then(response => {
                        if (!response.ok) {
                            throw new Error('Network error during regeneration.');
                        }
                        const reader = response.body.getReader();
                        const decoder = new TextDecoder('utf-8');
                        let buffer = '';

                        const processChunk = ({ done, value }) => {
                            if (done) {
                                this.isStreaming = false;
                                if (this.activeStreamText) {
                                    this.messages.push({
                                        role: 'assistant',
                                        content: this.activeStreamText,
                                        _expanded: false
                                    });
                                    this.activeStreamText = '';
                                }
                                this.scrollToBottom();
                                return;
                            }
                            buffer += decoder.decode(value, { stream: true });
                            let lines = buffer.split('\n');
                            buffer = lines.pop();
                            for (let line of lines) {
                                line = line.trim();
                                if (line.startsWith('data: ')) {
                                    const dataStr = line.substring(6);
                                    if (dataStr === '[DONE]') continue;
                                    try {
                                        const parsed = JSON.parse(dataStr);
                                        if (parsed.text) {
                                            this.activeStreamText += parsed.text;
                                            this.scrollToBottom();
                                        }
                                    } catch(e) {}
                                }
                            }
                            reader.read().then(processChunk);
                        };
                        reader.read().then(processChunk);
                    }).catch(error => {
                        this.isStreaming = false;
                        this.showProAlert('Regeneration Error', 'Could not regenerate response. Please check your connection.', 'error');
                    });
                },

                scrollToBottom() {
                    if (this._scrollRAF) return;
                    this._scrollRAF = requestAnimationFrame(() => {
                        this._scrollRAF = null;
                        const container = this.$refs.messageContainer;
                        if (container) {
                            container.scrollTop = container.scrollHeight;
                        }
                    });
                },

                async handleFileUpload(event) {
                    const file = event.target.files[0];
                    if (!file) return;

                    this.isUploading = true;
                    const formData = new FormData();
                    formData.append('file', file);
                    formData.append('_token', '{{ csrf_token() }}');

                    try {
                        const response = await fetch('{{ route("attachments.upload") }}', {
                            method: 'POST',
                            body: formData
                        });
                        
                        const result = await response.json();
                        if (result.success) {
                            this.attachments.push({
                                file_path: result.file_path,
                                file_name: result.file_name,
                                mime_type: result.mime_type,
                                file_size: result.file_size
                            });
                        } else {
                            this.showProAlert('Upload Failed', result.message || 'The file could not be uploaded. Please verify file format and size restrictions.', 'error');
                        }
                    } catch (error) {
                        console.error('Upload failed:', error);
                        this.showProAlert('Upload Failed', 'An unexpected server error occurred while processing your attachment.', 'error');
                    } finally {
                        this.isUploading = false;
                        event.target.value = ''; // Reset input
                    }
                },

                removeAttachment(index) {
                    this.attachments.splice(index, 1);
                },

                async sendMessage() {
                    const cleanPrompt = this.prompt.trim();
                    if (!cleanPrompt && this.attachments.length === 0) return;
                    if (this.isStreaming) return;

                    // If no conversation exists yet, first create one dynamically via AJAX to avoid page reload
                    if (!this.activeUuid) {
                        try {
                            const res = await fetch('{{ route("chats.store") }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({
                                    model: this.activeModel
                                })
                            });
                            if (!res.ok) throw new Error('Create failed');
                            const data = await res.json();
                            if (data.success && data.uuid) {
                                this.activeUuid = data.uuid;
                                // Update URL history dynamically without a page refresh
                                window.history.pushState({}, '', `/chats/${data.uuid}`);
                                // Add to conversations list dynamically so the leftbar updates without refresh
                                this.conversationsList.unshift({
                                    uuid: data.uuid,
                                    title: cleanPrompt.length > 25 ? cleanPrompt.substring(0, 25) + '...' : cleanPrompt,
                                    model: this.activeModel,
                                    updated_at: new Date().toISOString(),
                                    pinned_at: null
                                });
                            } else {
                                throw new Error('No UUID returned');
                            }
                        } catch (err) {
                            console.error('AJAX chat creation failed, using form redirect fallback:', err);
                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = '{{ route("chats.store") }}';
                            
                            const csrfInput = document.createElement('input');
                            csrfInput.type = 'hidden';
                            csrfInput.name = '_token';
                            csrfInput.value = '{{ csrf_token() }}';
                            form.appendChild(csrfInput);

                            const modelInput = document.createElement('input');
                            modelInput.type = 'hidden';
                            modelInput.name = 'model';
                            modelInput.value = this.activeModel;
                            form.appendChild(modelInput);

                            document.body.appendChild(form);
                            
                            sessionStorage.setItem('pending_prompt', this.prompt);
                            sessionStorage.setItem('pending_attachments', JSON.stringify(this.attachments));
                            
                            form.submit();
                            return;
                        }
                    }

                    // Append user message to active state instantly
                    const userMsgObj = {
                        role: 'user',
                        content: cleanPrompt,
                        attachments: [...this.attachments],
                        _expanded: false
                    };
                    this.messages.push(userMsgObj);
                    
                    // Clear inputs and start streaming mode
                    const currentPrompt = this.prompt;
                    const currentAttachments = [...this.attachments];
                    this.prompt = '';
                    this.attachments = [];
                    
                    // Reset textarea size
                    const textarea = this.$refs.promptInput;
                    if (textarea) textarea.style.height = 'auto';

                    this.isStreaming = true;
                    this.activeStreamText = '';
                    this.scrollToBottom();

                    // Establish POST fetch body stream connection
                    try {
                        const response = await fetch(`/chats/${this.activeUuid}/stream`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                prompt: currentPrompt,
                                attachments: currentAttachments,
                                model: this.activeModel
                            })
                        });

                        if (!response.ok) {
                            throw new Error('Server returned error ' + response.status);
                        }

                        const reader = response.body.getReader();
                        const decoder = new TextDecoder();
                        let buffer = '';

                        while (true) {
                            const { value, done } = await reader.read();
                            if (done) break;

                            buffer += decoder.decode(value, { stream: true });
                            
                            // Process SSE line format
                            const lines = buffer.split('\n');
                            // Keep the last line if it is incomplete
                            buffer = lines.pop();

                            for (const line of lines) {
                                const trimmed = line.trim();
                                if (trimmed.startsWith('data: ')) {
                                    const dataStr = trimmed.substring(6);
                                    if (dataStr === '[DONE]') {
                                        break;
                                    } else {
                                        try {
                                            const parsed = JSON.parse(dataStr);
                                            this.activeStreamText += parsed.text;
                                            this.scrollToBottom();
                                        } catch (e) {}
                                    }
                                }
                            }
                        }

                        // Save streamed response fully to messages stack
                        this.messages.push({
                            role: 'assistant',
                            content: this.activeStreamText,
                            _expanded: false
                        });
                        this.isStreaming = false;
                        this.activeStreamText = '';
                        
                        // Update sidebar conversation list time / refresh items
                        this.refreshConversationsList();

                    } catch (err) {
                        console.error('SSE streaming error:', err);
                        this.showProAlert('Streaming Error', 'Connection to the AI completion stream dropped or timed out. Please verify your connection or switch to another model.', 'error');
                        this.messages.push({
                            role: 'assistant',
                            content: (this.activeStreamText ? this.activeStreamText + '\n\n' : '') + '[Error: Connection lost or stream timed out.]'
                        });
                        this.isStreaming = false;
                        this.activeStreamText = '';
                    }
                },

                async togglePin(chat) {
                    try {
                        const response = await fetch(`/chats/${chat.uuid}/pin`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        });
                        const data = await response.json();
                        if (data.success) {
                            chat.pinned_at = data.pinned ? new Date().toISOString() : null;
                            this.sortConversationsList();
                        }
                    } catch (e) {
                        console.error('Pin failed:', e);
                    }
                },

                renameChat(chat) {
                    chat._editTitle = chat.title;
                    this.renamingUuid = chat.uuid;
                    this.$nextTick(() => {
                        const input = document.getElementById('rename-input-' + chat.uuid);
                        if (input) {
                            input.focus();
                            input.select();
                        }
                    });
                },

                async saveRename(chat) {
                    if (!chat._editTitle || !chat._editTitle.trim()) {
                        this.cancelRename(chat);
                        return;
                    }
                    const newTitle = chat._editTitle.trim();
                    if (newTitle === chat.title) {
                        this.cancelRename(chat);
                        return;
                    }
                    this.renamingUuid = null;
                    const oldTitle = chat.title;
                    chat.title = newTitle; // optimistic update
                    try {
                        const res = await fetch(`/chats/${chat.uuid}/rename`, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ title: newTitle })
                        });
                        const data = await res.json();
                        if (data.success) {
                            chat.title = data.title;
                        } else {
                            chat.title = oldTitle;
                        }
                    } catch (err) {
                        console.error('Rename failed:', err);
                        chat.title = oldTitle;
                    }
                },

                cancelRename(chat) {
                    this.renamingUuid = null;
                },

                async deleteChat(chat) {
                    // Open custom confirmation modal instead of browser confirm()
                    this.chatToDelete = chat;
                    this.deleteConfirmOpen = true;
                },

                async confirmDelete() {
                    const chat = this.chatToDelete;
                    if (!chat) return;
                    this.deleteConfirmOpen = false;
                    this.chatToDelete = null;

                    try {
                        const response = await fetch(`/chats/${chat.uuid}`, {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        });
                        const data = await response.json();
                        if (data.success) {
                            this.conversationsList = this.conversationsList.filter(c => c.uuid !== chat.uuid);
                            if (this.activeUuid === chat.uuid) {
                                window.location.href = '{{ route("chat") }}';
                            }
                        }
                    } catch (e) {
                        console.error('Delete failed:', e);
                    }
                },

                sortConversationsList() {
                    this.conversationsList.sort((a, b) => {
                        if (a.pinned_at && !b.pinned_at) return -1;
                        if (!a.pinned_at && b.pinned_at) return 1;
                        
                        // Sort pinned chats by pinned_at desc, unpinned by updated_at desc
                        if (a.pinned_at && b.pinned_at) {
                            return new Date(b.pinned_at) - new Date(a.pinned_at);
                        }
                        return new Date(b.updated_at) - new Date(a.updated_at);
                    });
                },

                handleTextareaKeydown(event) {
                    // Shift+Enter → insert a real newline (default browser behaviour)
                    // Plain Enter → send message (on desktop only, disabled in responsive mode)
                    if (event.key === 'Enter' && !event.shiftKey) {
                        const isMobile = window.innerWidth <= 768;
                        if (isMobile) {
                            // On mobile/responsive mode, Enter key acts as a regular line break
                            // So let it propagate normally without preventing default
                            return;
                        }

                        event.preventDefault();
                        if (!this.isStreaming) {
                            this.sendMessage();
                            // Reset textarea height after send
                            this.$nextTick(() => {
                                const ta = this.$refs.promptInput;
                                if (ta) {
                                    ta.style.height = 'auto';
                                    ta.classList.remove('overflowed');
                                }
                            });
                        }
                    }
                    // Shift+Enter: do nothing special — let browser insert \n naturally
                },

                async refreshConversationsList() {
                    try {
                        const response = await fetch('{{ route("chat") }}');
                        const html = await response.text();
                        
                        // Simple parser to extract list JSON if we wanted, or we just manually update the active item's update time
                        const current = this.conversationsList.find(c => c.uuid === this.activeUuid);
                        if (current) {
                            current.updated_at = new Date().toISOString();
                            this.sortConversationsList();
                        }
                    } catch (e) {
                        console.error('Refresh failed:', e);
                    }
                }
            }
        }

    </script>
</body>
</html>
