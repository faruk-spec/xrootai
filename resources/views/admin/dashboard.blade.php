<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - XrootAI</title>
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
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 24px;
            margin-bottom: 40px;
        }
        .stats-card {
            padding: 24px;
            border-radius: 24px;
            text-align: center;
        }
        .stats-num {
            font-size: 2.2rem;
            font-weight: 800;
            margin-top: 8px;
            background: linear-gradient(135deg, #4a88ff 0%, #56ab2f 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .admin-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 32px;
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
                <a href="{{ route('admin.dashboard') }}" class="menu-link active">
                    <svg style="width:20px; height:20px; opacity:0.85;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 3.055A9.003 9.003 0 1020.945 13H11V3.055z"/><path stroke-linecap="round" stroke-linejoin="round" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/></svg>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('admin.users') }}" class="menu-link">
                    <svg style="width:20px; height:20px; opacity:0.85;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span>Users List</span>
                </a>
                <a href="{{ route('admin.settings') }}" class="menu-link">
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
        <main class="admin-main">
            <h1 style="font-weight: 800; font-size: 2.2rem; margin-bottom: 8px;">Admin Dashboard</h1>
            <p style="color: var(--text-muted); margin-bottom: 40px;">System statistics and usage analytics</p>

            <!-- Stats grid -->
            <div class="stats-grid">
                <div class="clay-card stats-card">
                    <div style="font-size: 0.9rem; font-weight: 600; color: var(--text-muted);">Total Users</div>
                    <div class="stats-num">{{ $stats['total_users'] }}</div>
                </div>
                <div class="clay-card stats-card">
                    <div style="font-size: 0.9rem; font-weight: 600; color: var(--text-muted);">Total Chats</div>
                    <div class="stats-num">{{ $stats['total_conversations'] }}</div>
                </div>
                <div class="clay-card stats-card">
                    <div style="font-size: 0.9rem; font-weight: 600; color: var(--text-muted);">Total Messages</div>
                    <div class="stats-num">{{ $stats['total_messages'] }}</div>
                </div>
                <div class="clay-card stats-card">
                    <div style="font-size: 0.9rem; font-weight: 600; color: var(--text-muted);">Active Provider Keys</div>
                    <div class="stats-num">{{ $stats['active_keys'] }}</div>
                </div>
            </div>

            <!-- Dashboard sections -->
            <div class="admin-grid">
                
                <!-- Recent Users -->
                <div class="clay-card" style="padding: 28px; border-radius: 28px;">
                    <h3 style="font-weight: 700; font-size: 1.25rem; margin-bottom: 20px;">Recent Registrations</h3>
                    <div style="display:flex; flex-direction:column; gap:16px;">
                        @foreach($recentUsers as $user)
                            <div style="display:flex; justify-content:space-between; align-items:center; border-bottom: 1px solid var(--clay-card-border); padding-bottom:12px;">
                                <div>
                                    <div style="font-weight:600; font-size:0.95rem;">{{ $user->name }}</div>
                                    <div style="font-size:0.8rem; color:var(--text-muted);">{{ $user->email }}</div>
                                </div>
                                <div class="clay-inset" style="padding: 4px 12px; border-radius: 12px; font-size:0.8rem; font-weight:600; text-transform:uppercase;">
                                    {{ $user->role }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Usage Analytics -->
                <div class="clay-card" style="padding: 28px; border-radius: 28px;">
                    <h3 style="font-weight: 700; font-size: 1.25rem; margin-bottom: 20px;">Top User Activity</h3>
                    <div style="display:flex; flex-direction:column; gap:16px;">
                        @foreach($userStats as $user)
                            <div style="display:flex; justify-content:space-between; align-items:center; border-bottom: 1px solid var(--clay-card-border); padding-bottom:12px;">
                                <div>
                                    <div style="font-weight:600; font-size:0.95rem;">{{ $user->name }}</div>
                                    <div style="font-size:0.8rem; color:var(--text-muted);">{{ $user->email }}</div>
                                </div>
                                <div style="font-weight: 700; font-size:0.95rem; color: var(--accent-blue);">
                                    {{ $user->conversations_count }} chats
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>
        </main>
    </div>
</body>
</html>
