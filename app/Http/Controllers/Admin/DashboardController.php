<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\AIProvider;
use App\Models\ActivityLog;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'total_conversations' => Conversation::count(),
            'total_messages' => Message::count(),
            'active_keys' => AIProvider::where('is_active', true)->count(),
            'estimated_tokens' => (int) Message::where('role', 'assistant')->sum(DB::raw('LENGTH(content) / 4')),
        ];

        // Fetch recent users
        $recentUsers = User::orderBy('created_at', 'desc')->limit(5)->get();

        // Fetch user chat statistics
        $userStats = User::withCount(['conversations'])
            ->orderBy('conversations_count', 'desc')
            ->limit(6)
            ->get();

        // Fetch recent audit activity logs
        $recentLogs = ActivityLog::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();

        // System security posture health checks
        $securityHealth = [
            'maintenance_mode' => SystemSetting::get('general_maintenance_mode', false),
            'ip_allowlist_enabled' => !empty(config('admin.allowed_ips', [])),
            'two_factor_enabled' => config('admin.2fa_enabled', false),
            'session_timeout' => config('admin.session_timeout', 120),
        ];

        return view('admin.dashboard', compact('stats', 'recentUsers', 'userStats', 'recentLogs', 'securityHealth'));
    }
}

