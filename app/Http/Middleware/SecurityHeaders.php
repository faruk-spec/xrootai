<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request and inject essential HTTP security headers.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (method_exists($response, 'headers')) {
            // Prevent MIME sniffing
            $response->headers->set('X-Content-Type-Options', 'nosniff');

            // Prevent Clickjacking
            $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

            // Enable XSS Protection (for older browsers)
            $response->headers->set('X-XSS-Protection', '1; mode=block');

            // Control Referrer information
            $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

            // Permissions Policy (disable unnecessary browser APIs by default)
            $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

            // Strict-Transport-Security (HSTS for HTTPS production environments)
            if (app()->environment('production') && $request->isSecure()) {
                $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
            }

            // Content Security Policy (basic secure baseline while allowing inline scripts/styles for UI frameworks)
            if (!$response->headers->has('Content-Security-Policy')) {
                $response->headers->set('Content-Security-Policy', "default-src 'self' 'unsafe-inline' 'unsafe-eval' data: https: blob:; img-src 'self' data: https: blob:; font-src 'self' data: https:;");
            }
        }

        return $response;
    }
}
