<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users List - XrootAI Admin</title>
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
        .user-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid var(--clay-card-border);
        }
        .user-table th, .user-table td {
            padding: 16px 20px;
            text-align: left;
        }
        .user-table th {
            background: rgba(0, 0, 0, 0.02);
            font-weight: 700;
            border-bottom: 1px solid var(--clay-card-border);
        }
        .dark-mode .user-table th {
            background: rgba(255, 255, 255, 0.02);
        }
        .user-table td {
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            background: rgba(255, 255, 255, 0.1);
        }
        .dark-mode .user-table td {
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            background: rgba(0, 0, 0, 0.05);
        }
        .user-table tr:last-child td {
            border-bottom: none;
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
                <a href="{{ route('admin.dashboard') }}" class="menu-link">
                    <svg style="width:20px; height:20px; opacity:0.85;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 3.055A9.003 9.003 0 1020.945 13H11V3.055z"/><path stroke-linecap="round" stroke-linejoin="round" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/></svg>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('admin.users') }}" class="menu-link active">
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
            <h1 style="font-weight: 800; font-size: 2.2rem; margin-bottom: 8px;">User Accounts</h1>
            <p style="color: var(--text-muted); margin-bottom: 40px;">Manage registered accounts and assign roles</p>

            @if(session('success'))
                <div class="clay-card" style="padding: 16px 24px; background: rgba(86, 171, 47, 0.15); border: 1px solid rgba(86, 171, 47, 0.3); border-radius: 16px; color: #56ab2f; margin-bottom: 24px; font-weight: 600;">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="clay-card" style="padding: 16px 24px; background: rgba(220, 53, 69, 0.15); border: 1px solid rgba(220, 53, 69, 0.3); border-radius: 16px; color: #dc3545; margin-bottom: 24px; font-weight: 600;">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Users table -->
            <div class="clay-card" style="padding: 24px; border-radius: 28px; overflow-x: auto;">
                <table class="user-table">
                    <thead>
                        <tr>
                            <th>User ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Created At</th>
                            <th>Total Chats</th>
                            <th>Assign Role</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr id="user-row-{{ $user->id }}">
                                <td>#{{ $user->id }}</td>
                                <td><strong>{{ $user->name }}</strong></td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->created_at->format('M d, Y') }}</td>
                                <td>{{ $user->conversations_count }}</td>
                                <td>
                                    <form action="{{ route('admin.users.role', $user) }}" method="POST" style="margin: 0; display:flex; gap: 8px; align-items:center;">
                                        @csrf
                                        <select name="role" class="clay-inset" style="padding: 4px 12px; border-radius: 10px; border:none; outline:none; font-size:0.85rem; font-weight:600;" onchange="this.form.submit()">
                                            <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>User</option>
                                            <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                        </select>
                                    </form>
                                </td>
                                <td>
                                    @if($user->id !== Auth::user()->id)
                                        <button onclick="deleteUser({{ $user->id }})" class="clay-btn clay-btn-danger" style="padding: 6px 12px; border-radius: 10px; font-size: 0.8rem;">
                                            Delete
                                        </button>
                                    @else
                                        <span style="font-size:0.85rem; color:var(--text-muted); font-style:italic;">Logged In</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div style="margin-top: 24px;">
                    {{ $users->links() }}
                </div>
            </div>
        </main>
    </div>

    <script>
        async function deleteUser(userId) {
            if (!confirm('Are you absolutely sure you want to delete this user and all their chats permanently? This cannot be undone.')) {
                return;
            }

            try {
                const response = await fetch(`/admin/users/${userId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();
                if (response.ok && data.success) {
                    document.getElementById(`user-row-${userId}`).remove();
                } else {
                    alert(data.message || 'Something went wrong while deleting user.');
                }
            } catch (err) {
                console.error(err);
                alert('Connection error.');
            }
        }
    </script>
</body>
</html>
