<?php

#author’s name： Lim Jun Hong
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SessionDeviceLock
{
    public function handle($request, Closure $next)
    {
        // Only enforce when Laravel sees an authenticated user
        if (Auth::check()) {
            $server = session('sid_lock');                 // secret stored on server @ login/registration
            $client = $request->cookie('sid_lock');        // HttpOnly cookie set @ login/registration

            // STRICT: both must exist and match, otherwise kill session immediately
            if (!$server || !$client || !hash_equals($server, $client)) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect('/login')->withErrors([
                    'message' => 'Device lock failed. Please sign in again.',
                ]);
            }
        }

        return $next($request);
    }
}
