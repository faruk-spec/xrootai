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
                    📊 <span>Dashboard</span>
                </a>
                <a href="{{ route('admin.users') }}" class="menu-link active">
                    👥 <span>Users List</span>
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
