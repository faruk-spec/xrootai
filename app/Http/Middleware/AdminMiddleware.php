<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Check IP Allowlist if configured
        $allowedIps = config('admin.allowed_ips', []);
        if (!empty($allowedIps) && !in_array($request->ip(), $allowedIps)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Your IP address is not authorized for administrative access.'], 403);
            }
            abort(403, 'Your IP address is not authorized for administrative access.');
        }

        // Allow if exact role equals administrative staff roles or if user possesses specific admin permissions
        $allowedRoles = ['admin', 'Super Admin', 'Admin', 'Manager', 'Support Agent', 'Developer'];
        $adminPermissions = [
            'manage-users', 'manage-ai', 'manage-prompts', 'manage-kb',
            'view-analytics', 'manage-settings', 'view-logs', 'human-handoff'
        ];

        $hasRole = in_array($user->role, $allowedRoles) || $user->roles()->whereIn('name', $allowedRoles)->exists();
        $hasAdminPermission = $user->roles()->whereHas('permissions', function ($query) use ($adminPermissions) {
            $query->whereIn('name', $adminPermissions);
        })->exists();

        if (!$hasRole && !$hasAdminPermission) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized access to admin area.'], 403);
            }
            abort(403, 'Unauthorized access to admin area. You do not possess administrative roles or permissions.');
        }

        // Check mandatory 2FA if configured
        if (config('admin.require_2fa', false) && ($user->isSuperAdmin() || $user->hasRole('Admin') || strcasecmp($user->role, 'admin') === 0)) {
            if (!$user->hasTwoFactorEnabled()) {
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Two-Factor Authentication is required for admin access.'], 403);
                }
                return redirect()->route('profile.security')->with('error', 'Mandatory Security Requirement: You must enable Two-Factor Authentication to access the administrative dashboard.');
            }
        }

        // Check and update admin inactivity session timeout
        $timeoutMinutes = (int) config('admin.session_timeout', 15);
        if ($timeoutMinutes > 0 && $user->last_admin_activity_at) {
            $inactiveMinutes = $user->last_admin_activity_at->diffInMinutes(now());
            if ($inactiveMinutes > $timeoutMinutes && !$request->is('login*') && !$request->is('logout*')) {
                auth()->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('login')->with('error', 'Your administrative session timed out due to inactivity. Please log in again.');
            }
        }

        // Update last admin activity timestamp
        $user->forceFill(['last_admin_activity_at' => now()])->save();

        return $next($request);
    }
}
