<?php
#author’s name： Pang Jun Meng
namespace App\Http\Middleware;

use Closure;

class Idempotency
{
    public function handle($request, Closure $next)
    {
        $key = $request->header('Idempotency-Key');
        if (!$key) {
            return response()->json(['status' => 'failed', 'message' => 'Missing Idempotency-Key', 'processed_at' => now()->toIso8601String()], 400);
        }
        // Let PaymentService handle existing-key detection so DB transaction is atomic.
        return $next($request);
    }
}
