<?php

namespace App\Http\Middleware;

use App\Services\AIQuotaService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnforceAIQuota
{
    /**
     * Handle an incoming request by enforcing AI quota rules.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $sessionToken = $request->input('session_token', session()->getId());

        $quotaCheck = AIQuotaService::checkCanStream($user, $sessionToken);
        if (!$quotaCheck['allowed']) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => strip_tags($quotaCheck['message']),
                    'error' => 'QUOTA_EXCEEDED'
                ], 429);
            }

            return back()->withErrors(['quota' => strip_tags($quotaCheck['message'])]);
        }

        return $next($request);
    }
}
