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

    public function settings()
    {
        $settings = [];
        foreach (\App\Models\SystemSetting::$defaults as $key => $default) {
            $settings[$key] = \App\Models\SystemSetting::get($key);
        }

        return view('admin.settings', ['settings' => $settings]);
    }

    public function updateSettings(Request $request)
    {
        $settings = $request->except(['_token']);
        $group = $request->input('_group', 'general');

        foreach ($settings as $key => $value) {
            if ($key === '_group') {
                continue;
            }

            // Determine if the key exists in our defaults to prevent injecting arbitrary keys
            if (array_key_exists($key, \App\Models\SystemSetting::$defaults)) {
                $defaultValue = \App\Models\SystemSetting::$defaults[$key];
                $type = 'string';
                
                if (is_bool($defaultValue)) {
                    $type = 'boolean';
                    $value = $request->has($key); // Handles check boxes properly
                } elseif (is_int($defaultValue)) {
                    $type = 'integer';
                    $value = (int)$value;
                } elseif (is_double($defaultValue) || is_float($defaultValue)) {
                    $type = 'double';
                    $value = (double)$value;
                } elseif (is_array($defaultValue)) {
                    $type = 'array';
                    if (is_string($value)) {
                        $decoded = json_decode($value, true);
                        if (json_last_error() === JSON_ERROR_NONE) {
                            $value = $decoded;
                        } else {
                            $value = array_filter(array_map('trim', explode(',', $value)));
                        }
                    }
                }

                \App\Models\SystemSetting::set($key, $value, $group, $type);
            }
        }

        // Handle missing checkbox keys which browser does not submit
        // Loop through all defaults in the current group and if it's a boolean, set to false if not present in request
        foreach (\App\Models\SystemSetting::$defaults as $key => $defaultValue) {
            if (is_bool($defaultValue) && str_starts_with($key, $group . '_') && !$request->has($key)) {
                // Special edge cases for prefixes if some groups don't match group name prefix perfectly
                \App\Models\SystemSetting::set($key, false, $group, 'boolean');
            }
            // Handling general edge cases for prefix mapping
            if ($group === 'plans' && str_starts_with($key, 'plans_') && is_bool($defaultValue) && !$request->has($key)) {
                \App\Models\SystemSetting::set($key, false, $group, 'boolean');
            }
            if ($group === 'behavior' && str_starts_with($key, 'behavior_') && is_bool($defaultValue) && !$request->has($key)) {
                \App\Models\SystemSetting::set($key, false, $group, 'boolean');
            }
            if ($group === 'model' && str_starts_with($key, 'model_') && is_bool($defaultValue) && !$request->has($key)) {
                \App\Models\SystemSetting::set($key, false, $group, 'boolean');
            }
            if ($group === 'kb' && str_starts_with($key, 'kb_') && is_bool($defaultValue) && !$request->has($key)) {
                \App\Models\SystemSetting::set($key, false, $group, 'boolean');
            }
            if ($group === 'conv' && str_starts_with($key, 'conv_') && is_bool($defaultValue) && !$request->has($key)) {
                \App\Models\SystemSetting::set($key, false, $group, 'boolean');
            }
            if ($group === 'handoff' && str_starts_with($key, 'handoff_') && is_bool($defaultValue) && !$request->has($key)) {
                \App\Models\SystemSetting::set($key, false, $group, 'boolean');
            }
            if ($group === 'ux' && str_starts_with($key, 'ux_') && is_bool($defaultValue) && !$request->has($key)) {
                \App\Models\SystemSetting::set($key, false, $group, 'boolean');
            }
            if ($group === 'lang' && str_starts_with($key, 'lang_') && is_bool($defaultValue) && !$request->has($key)) {
                \App\Models\SystemSetting::set($key, false, $group, 'boolean');
            }
            if ($group === 'notif' && str_starts_with($key, 'notif_') && is_bool($defaultValue) && !$request->has($key)) {
                \App\Models\SystemSetting::set($key, false, $group, 'boolean');
            }
            if ($group === 'security' && str_starts_with($key, 'security_') && is_bool($defaultValue) && !$request->has($key)) {
                \App\Models\SystemSetting::set($key, false, $group, 'boolean');
            }
            if ($group === 'privacy' && str_starts_with($key, 'privacy_') && is_bool($defaultValue) && !$request->has($key)) {
                \App\Models\SystemSetting::set($key, false, $group, 'boolean');
            }
            if ($group === 'moderation' && str_starts_with($key, 'moderation_') && is_bool($defaultValue) && !$request->has($key)) {
                \App\Models\SystemSetting::set($key, false, $group, 'boolean');
            }
            if ($group === 'logging' && str_starts_with($key, 'logging_') && is_bool($defaultValue) && !$request->has($key)) {
                \App\Models\SystemSetting::set($key, false, $group, 'boolean');
            }
            if ($group === 'backup' && str_starts_with($key, 'backup_') && is_bool($defaultValue) && !$request->has($key)) {
                \App\Models\SystemSetting::set($key, false, $group, 'boolean');
            }
            if ($group === 'developer' && str_starts_with($key, 'developer_') && is_bool($defaultValue) && !$request->has($key)) {
                \App\Models\SystemSetting::set($key, false, $group, 'boolean');
            }
            if ($group === 'branding' && str_starts_with($key, 'branding_') && is_bool($defaultValue) && !$request->has($key)) {
                \App\Models\SystemSetting::set($key, false, $group, 'boolean');
            }
            if ($group === 'toggle' && str_starts_with($key, 'toggle_') && is_bool($defaultValue) && !$request->has($key)) {
                \App\Models\SystemSetting::set($key, false, $group, 'boolean');
            }
        }

        return redirect()->back()->with('success', 'Settings updated successfully.');
    }
}
