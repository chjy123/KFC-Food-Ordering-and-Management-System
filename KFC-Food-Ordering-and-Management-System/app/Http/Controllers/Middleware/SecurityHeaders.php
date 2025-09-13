<?php
#author’s name： Yew Kai Quan
namespace App\Http\Controllers\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Block framing (clickjacking)
        $response->headers->set('X-Frame-Options', 'DENY');
        // Modern framing control + basic hardening
        $response->headers->set('Content-Security-Policy',
            "default-src 'self'; ".
            "img-src 'self' data: https:; ".
            "style-src 'self' 'unsafe-inline' https:; ".
            "script-src 'self' 'unsafe-inline' https:; ".
            "frame-ancestors 'none'; ".
            "object-src 'none'; ".
            "base-uri 'self'; ".
            "upgrade-insecure-requests"
        );

        // Nice-to-have hardening headers
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');

        return $response;
    }
}
