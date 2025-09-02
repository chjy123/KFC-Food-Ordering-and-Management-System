<?php
#author’s name： Pang Jun Meng
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\PaymentService;
use App\Models\Payment;

class PaymentController extends Controller
{
    protected $service;

    public function __construct(PaymentService $service)
    {
        $this->service = $service;
        // optionally apply middleware here if not set in routes
    }

    /**
     * POST /api/payments/process
     */
    public function process(Request $request)
    {
        $data = $request->validate([
            'order_id' => 'required|integer',
            'user_id' => 'required|integer',
            'amount' => 'required|numeric',
            'method' => 'required|string',
            'payment_payload' => 'nullable|array',
            'currency' => 'nullable|string'
        ]);

        $idempotencyKey = $request->header('Idempotency-Key');
        if (!$idempotencyKey) {
            return response()->json(['status' => 'failed', 'message' => 'Missing Idempotency-Key', 'processed_at' => now()->toIso8601String()], 400);
        }

        // HMAC verification middleware should have validated X-Signature; but double-check if desired.

        $res = $this->service->processPayment($data, $idempotencyKey);

        if ($res['success']) {
            $payment = $res['payment'];
            return response()->json([
                'status' => 'success',
                'payment_id' => $payment->id,
                'transaction_ref' => $payment->transaction_ref,
                'message' => 'Payment processed',
                'processed_at' => $payment->updated_at->toIso8601String()
            ], 200);
        }

        return response()->json([
            'status' => 'failed',
            'message' => $res['message'] ?? 'Payment failed',
            'processed_at' => now()->toIso8601String()
        ], $res['httpStatus'] ?? 402);
    }

    public function show($id)
    {
        $payment = Payment::with(['order','user'])->findOrFail($id);
        return response()->json(['status' => 'success', 'payment' => $payment]);
    }

    public function history($userId)
    {
        $payments = Payment::where('user_id', $userId)->orderBy('created_at', 'desc')->get();
        return response()->json(['status' => 'success', 'payments' => $payments]);
    }
/*
    public function refund(Request $request, $id)
    {
        // authorization gate assumed elsewhere
        $res = $this->service->refundPayment((int)$id);
        if ($res['success']) {
            return response()->json(['status' => 'success', 'message' => $res['message'] ?? 'Refund succeeded']);
        }
        return response()->json(['status' => 'failed', 'message' => $res['message'] ?? 'Refund failed'], $res['httpStatus'] ?? 402);
    }
*/
}
