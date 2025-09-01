<?php
#author’s name： Pang Jun Meng
namespace App\Http\Middleware;

use Closure;

class VerifyHmac
{
    public function handle($request, Closure $next)
    {
        $signature = $request->header('X-Signature');
        $secret = env('SERVICE_SHARED_SECRET');
        $payload = $request->getContent();

        if (!$signature || !$secret) {
            return response()->json(['status' => 'failed', 'message' => 'Invalid signature', 'processed_at' => now()->toIso8601String()], 401);
        }

        $expected = hash_hmac('sha256', $payload, $secret);

        if (!hash_equals($expected, $signature)) {
            return response()->json(['status' => 'failed', 'message' => 'Invalid signature', 'processed_at' => now()->toIso8601String()], 401);
        }

        return $next($request);
    }
}
