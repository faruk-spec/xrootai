<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\AIProvider;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'total_conversations' => Conversation::count(),
            'total_messages' => Message::count(),
            'active_keys' => AIProvider::where('is_active', true)->count(),
        ];

        // Fetch recent users
        $recentUsers = User::orderBy('created_at', 'desc')->limit(5)->get();

        // Fetch user chat statistics
        $userStats = User::withCount(['conversations'])
            ->orderBy('conversations_count', 'desc')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentUsers', 'userStats'));
    }
}
