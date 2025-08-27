<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OrderClient
{
    protected $base;

    public function __construct()
    {
        $this->base = rtrim(env('ORDER_SERVICE_URL', ''), '/');
    }

    /**
     * Fetch order by id. Returns associative array or null.
     */
    public function getOrder(int $orderId): ?array
    {
        try {
            $res = Http::withToken(env('INTERNAL_SERVICE_TOKEN'))->get($this->base . '/api/orders/' . $orderId);
            if ($res->successful()) {
                $body = $res->json();
                return $body['order'] ?? $body;
            }
            return null;
        } catch (\Throwable $ex) {
            Log::error('OrderClient exception: ' . $ex->getMessage());
            return null;
        }
    }

    /**
     * Notify order module about payment status update (simple PUT)
     */
    public function updatePaymentStatus(int $orderId, string $status, int $paymentId = null): bool
    {
        try {
            $payload = ['payment_status' => $status, 'payment_id' => $paymentId];
            $res = Http::withToken(env('INTERNAL_SERVICE_TOKEN'))->put($this->base . '/api/orders/' . $orderId . '/payment', $payload);
            return $res->successful();
        } catch (\Throwable $ex) {
            Log::error('OrderClient updatePaymentStatus exception: ' . $ex->getMessage());
            return false;
        }
    }
}
