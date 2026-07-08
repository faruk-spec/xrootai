<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\ApiKey;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'total_conversations' => Conversation::count(),
            'total_messages' => Message::count(),
            'active_keys' => ApiKey::where('is_active', true)->count(),
        ];

        // Fetch recent users
        $recentUsers = User::orderBy('created_at', 'desc')->limit(5)->get();

        // Fetch user chat statistics
        $userStats = User::withCount(['conversations', 'apiKeys'])
            ->orderBy('conversations_count', 'desc')
            ->limit(10)
            ->get();

        return view('admin.dashboard', [
            'stats' => $stats,
            'recentUsers' => $recentUsers,
            'userStats' => $userStats,
        ]);
    }

    public function users()
    {
        $users = User::withCount('conversations')->paginate(15);
        return view('admin.users', ['users' => $users]);
    }

    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role' => ['required', 'string', 'in:user,admin'],
        ]);

        // Prevent self-demotion
        if ($user->id === $request->user()->id) {
            return redirect()->back()->with('error', 'You cannot change your own role.');
        }

        $user->update([
            'role' => $request->role,
        ]);

        return redirect()->back()->with('success', "User role updated to {$request->role} successfully.");
    }

    public function deleteUser(Request $request, User $user)
    {
        // Prevent self-deletion
        if ($user->id === $request->user()->id) {
            return response()->json(['success' => false, 'message' => 'You cannot delete yourself.'], 400);
        }

        $user->delete();

        return response()->json(['success' => true]);
    }
}
