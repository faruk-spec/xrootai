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
                    📊 <span>Dashboard</span>
                </a>
                <a href="{{ route('admin.users') }}" class="menu-link">
                    👥 <span>Users List</span>
                </a>
                <a href="{{ route('admin.settings') }}" class="menu-link">
                    ⚙️ <span>System Settings</span>
                </a>
                <a href="{{ route('chat') }}" class="menu-link">
                    💬 <span>Go to Chat</span>
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
