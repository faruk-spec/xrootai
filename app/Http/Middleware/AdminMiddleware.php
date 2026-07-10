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

        // Allow if exact role column equals 'admin' or if user has any administrative / staff roles assigned
        $allowedRoles = ['admin', 'Super Admin', 'Admin', 'Manager', 'Support Agent', 'Developer'];
        
        $hasAdminAccess = in_array($user->role, $allowedRoles) || 
                          $user->roles()->whereIn('name', $allowedRoles)->exists() ||
                          $user->roles()->whereHas('permissions')->exists();

        if (!$hasAdminAccess) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized access to admin area.'], 403);
            }
            abort(403, 'Unauthorized access to admin area. You do not possess administrative roles or permissions.');
        }

        return $next($request);
    }
}
