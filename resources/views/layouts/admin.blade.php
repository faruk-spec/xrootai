<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SaaS Admin Dashboard') - {{ \App\Models\SystemSetting::get('general_chatbot_name', 'XrootAI') }}</title>
    @if(\App\Models\SystemSetting::get('general_site_icon'))
        <link rel="icon" href="{{ \App\Models\SystemSetting::get('general_site_icon') }}">
    @endif
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts Outfit & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --sidebar-width: 280px;
            --sidebar-collapsed-width: 80px;
            --font-family-outfit: 'Outfit', 'Inter', sans-serif;
            --bg-body-light: #f8f9fc;
            --bg-card-light: #ffffff;
            --text-primary-light: #1e293b;
            --text-secondary-light: #64748b;
            
            --bg-body-dark: #0b0f19;
            --bg-card-dark: #151b2c;
            --text-primary-dark: #f8fafc;
            --text-secondary-dark: #94a3b8;
            
            --primary-accent: #3b82f6;
            --primary-accent-shadow: rgba(59, 130, 246, 0.25);
            --border-color-light: #e2e8f0;
            --border-color-dark: #222d45;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-body-light);
            color: var(--text-primary-light);
            transition: all 0.25s ease;
            overflow-x: hidden;
        }

        body.dark-mode {
            background-color: var(--bg-body-dark);
            color: var(--text-primary-dark);
        }

        h1, h2, h3, h4, h5, h6, .brand-text {
            font-family: var(--font-family-outfit);
        }

        /* Sidebar Styling */
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            background-color: #ffffff;
            border-right: 1px solid var(--border-color-light);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow-y: auto;
            display: flex;
            flex-direction: column;
        }

        body.dark-mode .sidebar {
            background-color: #111827;
            border-right: 1px solid var(--border-color-dark);
        }

        .sidebar.collapsed {
            width: var(--sidebar-collapsed-width);
        }

        .sidebar.collapsed .brand-text, 
        .sidebar.collapsed .nav-text, 
        .sidebar.collapsed .dropdown-toggle::after,
        .sidebar.collapsed .submenu {
            display: none !important;
        }

        .sidebar.collapsed .nav-link {
            justify-content: center;
            padding: 12px 0;
        }

        .sidebar-brand {
            padding: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid var(--border-color-light);
        }

        body.dark-mode .sidebar-brand {
            border-bottom: 1px solid var(--border-color-dark);
        }

        .brand-logo {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, #3b82f6 0%, #10b981 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 800;
            font-size: 1.25rem;
        }

        .brand-text {
            font-weight: 700;
            font-size: 1.15rem;
            color: var(--text-primary-light);
        }

        body.dark-mode .brand-text {
            color: var(--text-primary-dark);
        }

        .sidebar-nav {
            padding: 20px 12px;
            flex-grow: 1;
        }

        .nav-category {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05rem;
            color: var(--text-secondary-light);
            margin: 16px 12px 8px 12px;
            font-weight: 700;
        }

        body.dark-mode .nav-category {
            color: var(--text-secondary-dark);
        }

        .sidebar.collapsed .nav-category {
            display: none;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 16px;
            color: var(--text-secondary-light);
            border-radius: 12px;
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.2s ease;
            text-decoration: none;
            margin-bottom: 4px;
        }

        body.dark-mode .nav-link {
            color: var(--text-secondary-dark);
        }

        .nav-link:hover, .nav-link.active {
            background-color: rgba(59, 130, 246, 0.08);
            color: var(--primary-accent);
        }

        .nav-link.active {
            font-weight: 600;
        }

        .submenu {
            padding-left: 28px;
            margin-bottom: 8px;
        }

        .submenu-link {
            display: block;
            padding: 6px 16px;
            font-size: 0.85rem;
            color: var(--text-secondary-light);
            text-decoration: none;
            border-left: 2px solid var(--border-color-light);
            transition: all 0.2s ease;
        }

        body.dark-mode .submenu-link {
            color: var(--text-secondary-dark);
            border-left: 2px solid var(--border-color-dark);
        }

        .submenu-link:hover, .submenu-link.active {
            color: var(--primary-accent);
            border-left-color: var(--primary-accent);
        }

        /* Main Content Panel */
        .wrapper {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
        }

        .wrapper.collapsed {
            margin-left: var(--sidebar-collapsed-width);
        }

        /* Header / Navbar */
        .topbar {
            height: 70px;
            background-color: #ffffff;
            border-bottom: 1px solid var(--border-color-light);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
            position: sticky;
            top: 0;
            z-index: 999;
        }

        body.dark-mode .topbar {
            background-color: #111827;
            border-bottom: 1px solid var(--border-color-dark);
        }

        /* SaaS Layout Cards */
        .card {
            background-color: var(--bg-card-light);
            border: 1px solid var(--border-color-light);
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02), 0 2px 4px -1px rgba(0,0,0,0.01);
            transition: transform 0.2s, box-shadow 0.2s;
            margin-bottom: 24px;
        }

        body.dark-mode .card {
            background-color: var(--bg-card-dark);
            border: 1px solid var(--border-color-dark);
        }

        .form-control, .form-select {
            border-radius: 10px;
            border: 1px solid var(--border-color-light);
            background-color: #ffffff;
            color: var(--text-primary-light);
            padding: 10px 14px;
            font-size: 0.9rem;
            transition: all 0.2s ease;
        }

        body.dark-mode .form-control, body.dark-mode .form-select {
            border: 1px solid var(--border-color-dark);
            background-color: #0b0f19;
            color: var(--text-primary-dark);
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-accent);
            box-shadow: 0 0 0 3px var(--primary-accent-shadow);
        }

        .btn {
            border-radius: 10px;
            font-weight: 500;
            padding: 10px 18px;
            font-size: 0.9rem;
            transition: all 0.2s ease;
        }

        .btn-primary {
            background-color: var(--primary-accent);
            border-color: var(--primary-accent);
        }

        .btn-primary:hover {
            background-color: #2563eb;
            border-color: #2563eb;
        }

        .badge {
            border-radius: 6px;
            padding: 5px 8px;
            font-weight: 600;
        }

        /* Glassmorphic Glows */
        .glass-glow {
            position: absolute;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            background: rgba(59, 130, 246, 0.15);
            filter: blur(100px);
            z-index: -1;
            pointer-events: none;
        }

        .table-responsive {
            border-radius: 12px;
            overflow: hidden;
        }

        .table th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05rem;
            background-color: #f1f5f9;
            color: var(--text-secondary-light);
            border-bottom: 1px solid var(--border-color-light);
            padding: 14px 20px;
        }

        body.dark-mode .table th {
            background-color: #1f2937;
            color: var(--text-secondary-dark);
            border-bottom: 1px solid var(--border-color-dark);
        }

        .table td {
            padding: 14px 20px;
            vertical-align: middle;
            border-bottom: 1px solid var(--border-color-light);
            font-size: 0.875rem;
        }

        body.dark-mode .table td {
            background-color: transparent !important;
            color: var(--text-primary-dark);
            border-bottom: 1px solid var(--border-color-dark);
        }

        body.dark-mode .modal-content, body.dark-mode .dropdown-menu {
            background-color: #151b2c !important;
            border: 1px solid var(--border-color-dark) !important;
            color: var(--text-primary-dark) !important;
        }

        body.dark-mode .dropdown-item {
            color: var(--text-primary-dark) !important;
        }

        body.dark-mode .dropdown-item:hover {
            background-color: rgba(59, 130, 246, 0.15) !important;
            color: #ffffff !important;
        }

        body.dark-mode .text-muted, body.dark-mode .text-secondary {
            color: #94a3b8 !important;
        }

        body.dark-mode .bg-light, body.dark-mode .btn-light {
            background-color: #1f2937 !important;
            border-color: var(--border-color-dark) !important;
            color: var(--text-primary-dark) !important;
        }

        body.dark-mode .border-light, body.dark-mode .border, body.dark-mode .border-light-subtle {
            border-color: var(--border-color-dark) !important;
        }

        body.dark-mode .nav-tabs .nav-link {
            color: var(--text-secondary-dark);
        }

        body.dark-mode .nav-tabs .nav-link.active {
            background-color: var(--bg-card-dark);
            border-color: var(--border-color-dark) var(--border-color-dark) transparent;
            color: #3b82f6;
        }

        .toast-container {
            z-index: 1060;
        }
    </style>
</head>
<body>
    <script>
        if (localStorage.getItem('darkMode') === 'true') {
            document.body.classList.add('dark-mode');
            document.documentElement.setAttribute('data-bs-theme', 'dark');
        } else {
            document.documentElement.setAttribute('data-bs-theme', 'light');
        }
    </script>
    <style>
        body.dark-mode .admin-logo-light { display: none !important; }
        body.dark-mode .admin-logo-dark { display: block !important; }
    </style>
    <div class="glass-glow" style="top: 10%; right: 10%;"></div>
    <div class="glass-glow" style="bottom: 20%; left: 5%;"></div>

    <!-- Sidebar Layout -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand" style="display:flex; align-items:center; gap:10px;">
            @php 
                $lightLogo = \App\Models\SystemSetting::get('general_logo_light') ?: \App\Models\SystemSetting::get('general_chatbot_logo'); 
                $darkLogo = \App\Models\SystemSetting::get('general_logo_dark') ?: \App\Models\SystemSetting::get('general_chatbot_logo'); 
            @endphp
            @if($lightLogo || $darkLogo)
                @if($lightLogo && $darkLogo && $lightLogo !== $darkLogo)
                    <img src="{{ $lightLogo }}" class="admin-logo-light" alt="Logo" style="width:36px; height:36px; border-radius:10px; object-fit:contain; flex-shrink:0;">
                    <img src="{{ $darkLogo }}" class="admin-logo-dark" alt="Logo" style="width:36px; height:36px; border-radius:10px; object-fit:contain; flex-shrink:0; display:none;">
                @else
                    <img src="{{ $lightLogo ?: $darkLogo }}" alt="Logo" style="width:36px; height:36px; border-radius:10px; object-fit:contain; flex-shrink:0;">
                @endif
            @else
                <div class="brand-logo" style="flex-shrink:0;">{{ substr(\App\Models\SystemSetting::get('general_chatbot_name', 'XrootAI'), 0, 1) }}</div>
            @endif
            <span class="brand-text">{{ \App\Models\SystemSetting::get('general_chatbot_name', 'XrootAI') }} Admin</span>
        </div>

        <div class="sidebar-nav">
            <!-- GENERAL -->
            <div class="nav-category">Dashboard</div>
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ Route::is('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-fill fs-5"></i>
                <span class="nav-text">Dashboard</span>
            </a>

            <!-- USERS & PLANS -->
            <div class="nav-category">Access Control</div>
            <a href="{{ route('admin.oauth.index') }}" class="nav-link {{ Request::is('admin/oauth*') ? 'active' : '' }}">
                <i class="bi bi-shield-lock-fill fs-5"></i>
                <span class="nav-text">Social Login & OAuth</span>
            </a>
            <a href="#usersMenu" class="nav-link collapsed" data-bs-toggle="collapse">
                <i class="bi bi-people-fill fs-5"></i>
                <span class="nav-text">User Directory</span>
                <i class="bi bi-chevron-down ms-auto nav-text fs-7"></i>
            </a>
            <div class="collapse {{ Request::is('admin/users*') || Request::is('admin/roles*') || Request::is('admin/permissions*') || Request::is('admin/auth-settings*') || Request::is('admin/settings/plans*') || Request::is('admin/settings/privacy*') ? 'show' : '' }}" id="usersMenu">
                <div class="submenu">
                    <a href="{{ route('admin.users') }}" class="submenu-link {{ Request::is('admin/users*') ? 'active' : '' }}">Users List</a>
                    <a href="{{ route('admin.roles.index') }}" class="submenu-link {{ Request::is('admin/roles*') ? 'active' : '' }}">Roles & RBAC</a>
                    <a href="{{ route('admin.permissions.index') }}" class="submenu-link {{ Request::is('admin/permissions*') ? 'active' : '' }}">System Permissions</a>
                    <a href="{{ route('admin.oauth.index') }}" class="submenu-link {{ Request::is('admin/oauth*') ? 'active' : '' }}">Social Login & OAuth</a>
                    <a href="{{ route('admin.auth-settings.index') }}" class="submenu-link {{ Request::is('admin/auth-settings*') ? 'active' : '' }}">Auth & Security Policies</a>
                    <a href="{{ route('admin.settings') }}?tab=plans" class="submenu-link">Plans & Limits</a>
                    <a href="{{ route('admin.settings') }}?tab=privacy" class="submenu-link">Data & Privacy</a>
                </div>
            </div>

            <!-- AI CONFIG -->
            <div class="nav-category">AI Engine Config</div>
            <a href="#aiMenu" class="nav-link collapsed" data-bs-toggle="collapse">
                <i class="bi bi-cpu-fill fs-5"></i>
                <span class="nav-text">AI Orchestration</span>
                <i class="bi bi-chevron-down ms-auto nav-text fs-7"></i>
            </a>
            <div class="collapse {{ Request::is('admin/providers*') || Request::is('admin/models*') || Request::is('admin/routing*') || Request::is('admin/settings/model*') || Request::is('admin/settings/behavior*') ? 'show' : '' }}" id="aiMenu">
                <div class="submenu">
                    <a href="{{ route('admin.providers.index') }}" class="submenu-link {{ Request::is('admin/providers*') ? 'active' : '' }}">AI Providers</a>
                    <a href="{{ route('admin.models.index') }}" class="submenu-link {{ Request::is('admin/models*') ? 'active' : '' }}">AI Models</a>
                    <a href="{{ route('admin.routing.index') }}" class="submenu-link {{ Request::is('admin/routing*') ? 'active' : '' }}">AI Routing Rules</a>
                    <a href="{{ route('admin.settings') }}?tab=behavior" class="submenu-link">Model Parameters</a>
                    <a href="{{ route('admin.settings') }}?tab=prompt" class="submenu-link">System Prompts</a>
                    <a href="{{ route('admin.prompts.index') }}" class="submenu-link {{ Request::is('admin/prompts*') ? 'active' : '' }}">Prompt Templates</a>
                </div>
            </div>

            <!-- KNOWLEDGE BASE -->
            <div class="nav-category">Knowledge & Data</div>
            <a href="#kbMenu" class="nav-link collapsed" data-bs-toggle="collapse">
                <i class="bi bi-database-fill fs-5"></i>
                <span class="nav-text">Data Base</span>
                <i class="bi bi-chevron-down ms-auto nav-text fs-7"></i>
            </a>
            <div class="collapse {{ Request::is('admin/kb*') || Request::is('admin/settings/kb*') ? 'show' : '' }}" id="kbMenu">
                <div class="submenu">
                    <a href="{{ route('admin.kb.index') }}" class="submenu-link {{ Request::is('admin/kb*') ? 'active' : '' }}">Knowledge Sources</a>
                    <a href="{{ route('admin.settings') }}?tab=kb" class="submenu-link">RAG Configurations</a>
                </div>
            </div>

            <!-- OPERATIONS -->
            <div class="nav-category">Operations</div>
            <a href="#opsMenu" class="nav-link collapsed" data-bs-toggle="collapse">
                <i class="bi bi-gear-wide-connected fs-5"></i>
                <span class="nav-text">System Configurations</span>
                <i class="bi bi-chevron-down ms-auto nav-text fs-7"></i>
            </a>
            <div class="collapse {{ Request::is('admin/settings*') && !Request::is('admin/settings/plans*') || Request::is('admin/email-config*') || Request::is('admin/email-templates*') || Request::is('admin/auth-settings*') ? 'show' : '' }}" id="opsMenu">
                <div class="submenu">
                    <a href="{{ route('admin.settings') }}?tab=general" class="submenu-link">General Settings</a>
                    <a href="{{ route('admin.email-config.index') }}" class="submenu-link {{ Request::is('admin/email-config*') ? 'active' : '' }}">Email Configuration</a>
                    <a href="{{ route('admin.email-templates.index') }}" class="submenu-link {{ Request::is('admin/email-templates*') ? 'active' : '' }}">Email Templates</a>
                    <a href="{{ route('admin.auth-settings.index') }}" class="submenu-link {{ Request::is('admin/auth-settings*') ? 'active' : '' }}">Authentication & Security</a>
                    <a href="{{ route('admin.settings') }}?tab=lang" class="submenu-link">Language Settings</a>
                    <a href="{{ route('admin.settings') }}?tab=notif" class="submenu-link">Alerts & Notifications</a>
                    <a href="{{ route('admin.settings') }}?tab=security" class="submenu-link">Security & Moderation</a>
                    <a href="{{ route('admin.settings') }}?tab=integrations" class="submenu-link">Integrations</a>
                    <a href="{{ route('admin.settings') }}?tab=toggle" class="submenu-link">Feature Toggles</a>
                </div>
            </div>

            <div class="nav-category">Audit & Ops</div>
            <a href="#auditMenu" class="nav-link collapsed" data-bs-toggle="collapse">
                <i class="bi bi-activity fs-5"></i>
                <span class="nav-text">Logs & Performance</span>
                <i class="bi bi-chevron-down ms-auto nav-text fs-7"></i>
            </a>
            <div class="collapse {{ Request::is('admin/logs*') || Request::is('admin/settings/billing*') || Request::is('admin/settings/developer*') || Request::is('admin/settings/backup*') ? 'show' : '' }}" id="auditMenu">
                <div class="submenu">
                    <a href="{{ route('admin.logs.index') }}" class="submenu-link {{ Request::is('admin/logs*') ? 'active' : '' }}">Audit Logs</a>
                    <a href="{{ route('admin.settings') }}?tab=billing" class="submenu-link">Billing & Cost Usage</a>
                    <a href="{{ route('admin.settings') }}?tab=backup" class="submenu-link">Backup & Recovery</a>
                    <a href="{{ route('admin.settings') }}?tab=developer" class="submenu-link">Developer Settings</a>
                </div>
            </div>

            <div class="nav-category">Design</div>
            <a href="{{ route('admin.settings') }}?tab=branding" class="nav-link">
                <i class="bi bi-palette-fill fs-5"></i>
                <span class="nav-text">Appearance</span>
            </a>
        </div>
        
        <!-- Sidebar Bottom -->
        <div class="p-3 border-top border-light-subtle d-flex justify-content-between align-items-center">
            <button onclick="toggleDarkMode()" class="btn btn-sm btn-outline-secondary rounded-circle" style="width:36px; height:36px; padding:0; display:flex; align-items:center; justify-content:center;">
                <i class="bi bi-moon-stars-fill" id="themeIcon"></i>
            </button>
            <form action="{{ route('logout') }}" method="POST" class="m-0">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-danger">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Wrapper -->
    <div class="wrapper" id="wrapper">
        <!-- Top Navbar -->
        <header class="topbar">
            <div class="d-flex align-items-center gap-3">
                <button onclick="toggleSidebar()" class="btn btn-sm btn-light border" id="sidebarCollapseBtn">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Admin</a></li>
                        @yield('breadcrumbs')
                    </ol>
                </nav>
            </div>
            
            <div class="d-flex align-items-center gap-3">
                <!-- Notifications Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-sm btn-light border position-relative" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-bell-fill fs-6 text-secondary"></i>
                        <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border border-light-subtle" style="width:280px; font-size:0.85rem;">
                        <li class="p-2 border-bottom fw-semibold">Recent Operations Logs</li>
                        <li><a class="dropdown-item py-2" href="{{ route('admin.logs.index') }}"><i class="bi bi-info-circle text-primary me-2"></i> Settings modified successfully</a></li>
                        <li><a class="dropdown-item py-2" href="{{ route('admin.logs.index') }}"><i class="bi bi-check-circle text-success me-2"></i> OpenAI Connection verified</a></li>
                        <li><a class="dropdown-item py-2 border-top text-center text-primary fw-medium" href="{{ route('admin.logs.index') }}">View All Logs</a></li>
                    </ul>
                </div>

                <!-- Go To App -->
                <a href="{{ route('chat') }}" class="btn btn-sm btn-outline-primary d-flex align-items-center gap-2">
                    <i class="bi bi-chat-left-text-fill"></i>
                    <span>App Interface</span>
                </a>
            </div>
        </header>

        <!-- Main Body Content -->
        <main class="container-fluid p-4" style="flex-grow:1;">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4" role="alert" style="border-radius:12px;">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-check-circle-fill fs-5"></i>
                        <div>{{ session('success') }}</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4" role="alert" style="border-radius:12px;">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                        <div>{{ session('error') }}</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Bootstrap Bundle with Popper JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Sidebar & Theme Switcher Logic -->
    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('collapsed');
            document.getElementById('wrapper').classList.toggle('collapsed');
            localStorage.setItem('sidebarCollapsed', document.getElementById('sidebar').classList.contains('collapsed'));
        }

        function toggleDarkMode() {
            const body = document.body;
            body.classList.toggle('dark-mode');
            const isDark = body.classList.contains('dark-mode');
            localStorage.setItem('darkMode', isDark);
            document.documentElement.setAttribute('data-bs-theme', isDark ? 'dark' : 'light');
            
            const themeIcon = document.getElementById('themeIcon');
            if (isDark) {
                themeIcon.className = 'bi bi-sun-fill';
            } else {
                themeIcon.className = 'bi bi-moon-stars-fill';
            }
        }

        // Restore States
        if (localStorage.getItem('sidebarCollapsed') === 'true') {
            document.getElementById('sidebar').classList.add('collapsed');
            document.getElementById('wrapper').classList.add('collapsed');
        }

        const isDark = localStorage.getItem('darkMode') === 'true';
        const themeIcon = document.getElementById('themeIcon');
        if (isDark) {
            document.body.classList.add('dark-mode');
            document.documentElement.setAttribute('data-bs-theme', 'dark');
            themeIcon.className = 'bi bi-sun-fill';
        } else {
            document.body.classList.remove('dark-mode');
            document.documentElement.setAttribute('data-bs-theme', 'light');
            themeIcon.className = 'bi bi-moon-stars-fill';
        }

        // Universal Professional Admin Confirmation Modal System
        let adminConfirmCallback = null;
        function showAdminConfirmModal(message, onConfirm) {
            adminConfirmCallback = onConfirm;
            const modalEl = document.getElementById('globalAdminConfirmModal');
            document.getElementById('globalAdminConfirmMessage').innerText = message;
            const bsModal = new bootstrap.Modal(modalEl);
            bsModal.show();
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Automatically upgrade all forms with onsubmit="return confirm('...')" across admin views
            document.querySelectorAll('form[onsubmit*="confirm("]').forEach(form => {
                const attr = form.getAttribute('onsubmit');
                const match = attr.match(/confirm\(['"`](.*?)['"`]\)/);
                if (match) {
                    const msg = match[1];
                    form.removeAttribute('onsubmit');
                    form.addEventListener('submit', function(e) {
                        e.preventDefault();
                        showAdminConfirmModal(msg, () => form.submit());
                    });
                }
            });

            // Handle confirmation click inside modal
            document.getElementById('globalAdminConfirmBtn').addEventListener('click', function() {
                if (adminConfirmCallback) {
                    adminConfirmCallback();
                }
                const modalEl = document.getElementById('globalAdminConfirmModal');
                const bsModal = bootstrap.Modal.getInstance(modalEl);
                if (bsModal) bsModal.hide();
            });
        });
    </script>

    <!-- Global Professional Admin Confirmation Modal -->
    <div class="modal fade" id="globalAdminConfirmModal" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden; background: var(--card-bg, #ffffff);">
                <div class="modal-body p-4 text-center">
                    <div class="d-flex justify-content-center mb-3">
                        <div style="width: 64px; height: 64px; border-radius: 20px; background: rgba(239, 68, 68, 0.12); color: #ef4444; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 24px rgba(239, 68, 68, 0.18);">
                            <i class="bi bi-exclamation-triangle-fill fs-2"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold mb-2" style="font-size: 1.25rem;">Are you sure?</h4>
                    <p class="text-muted mb-4" id="globalAdminConfirmMessage" style="font-size: 0.95rem; line-height: 1.6;"></p>
                    <div class="d-flex gap-2 justify-content-center">
                        <button type="button" class="btn btn-secondary px-4 py-2 rounded-3 fw-medium flex-fill" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger px-4 py-2 rounded-3 fw-bold flex-fill shadow-sm" id="globalAdminConfirmBtn" style="background: linear-gradient(135deg, #ef4444, #dc2626); border: none;">Confirm Action</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @yield('scripts')
</body>
</html>
