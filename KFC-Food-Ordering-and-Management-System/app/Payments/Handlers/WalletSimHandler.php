<?php
#author’s name： Pang Jun Meng
namespace App\Payments\Handlers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WalletSimHandler implements PaymentHandlerInterface
{
    public function pay(array $data): array
    {
        // Implementation mirrors CardSim but uses wallet endpoint
        $payment = $data['payment'];
        $payload = [
            'merchant_id' => env('MERCHANT_ID'),
            'order_id' => $payment->order_id,
            'amount' => $payment->amount,
            'currency' => $payment->currency,
            'meta' => $payment->meta ?? []
        ];

        $signature = hash_hmac('sha256', json_encode($payload), env('GATEWAY_SHARED_SECRET'));

        try {
            $res = Http::withHeaders([
                'X-Signature' => $signature,
                'Idempotency-Key' => $payment->idempotency_key
            ])->post(rtrim(env('GATEWAY_URL'), '/') . '/api/wallet/charge', $payload);

            if ($res->successful()) {
                $body = $res->json();
                if (($body['status'] ?? '') === 'success') {
                    return [
                        'success' => true,
                        'transactionRef' => $body['transaction_ref'] ?? null,
                        'message' => $body['message'] ?? 'Wallet charged'
                    ];
                }
                return ['success' => false, 'message' => $body['message'] ?? 'Wallet declined'];
            }
            return ['success' => false, 'message' => 'Gateway HTTP error'];
        } catch (\Throwable $ex) {
            Log::error('WalletSimHandler exception: ' . $ex->getMessage());
            return ['success' => false, 'message' => 'Gateway exception'];
        }
    }

    public function refund(string $transactionRef): array
    {
        $payload = ['transaction_ref' => $transactionRef, 'merchant_id' => env('MERCHANT_ID')];
        $signature = hash_hmac('sha256', json_encode($payload), env('GATEWAY_SHARED_SECRET'));

        try {
            $res = Http::withHeaders(['X-Signature' => $signature])->post(rtrim(env('GATEWAY_URL'), '/') . '/api/wallet/refund', $payload);
            if ($res->successful()) {
                $body = $res->json();
                return ['success' => ($body['status'] ?? '') === 'success', 'message' => $body['message'] ?? null];
            }
            return ['success' => false, 'message' => 'Gateway HTTP error'];
        } catch (\Throwable $ex) {
            return ['success' => false, 'message' => 'Gateway exception'];
        }
    }
}
