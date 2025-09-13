<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class VerifyUserSession
{
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            $ip = $request->ip();
            $agent = substr($request->userAgent(), 0, 120);

            // ✅ If session already locked, verify it
            if (session()->has('ip_address') && session()->has('user_agent')) {
                if (session('ip_address') !== $ip || session('user_agent') !== $agent) {
                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();

                    return redirect('/login')->withErrors([
                        'message' => 'Session hijacking attempt detected!',
                    ]);
                }
            } else {
                // Lock session if not yet bound
                session(['ip_address' => $ip]);
                session(['user_agent' => $agent]);
            }
        }

        return $next($request);
    }
}
