<?php

namespace App\Http\Controllers\Middleware;

use App\Support\SafeRedirect;
use Closure;
use Illuminate\Http\Request;

class SanitizeNextParam
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->has('next')) {
            $clean = SafeRedirect::sanitize($request->query('next'), 'kfc.locations'); 
            $request->merge(['next' => $clean]);
        }
        return $next($request);
    }
}
