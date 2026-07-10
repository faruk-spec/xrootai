<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request and check if user has required permission.
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (!$request->user() || !$request->user()->hasPermission($permission)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => "Unauthorized. Required permission: {$permission}"], 403);
            }
            abort(403, "Unauthorized access. You need the '{$permission}' permission to perform this action.");
        }

        return $next($request);
    }
}
