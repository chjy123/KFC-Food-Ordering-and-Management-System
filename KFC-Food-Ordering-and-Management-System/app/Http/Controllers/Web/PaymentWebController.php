<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;

class PaymentWebController extends Controller
{
    /**
     * Show checkout page. Falls back to query param `amount` if OrderClient not available.
     */
    public function showCheckout(int $orderId)
    {
        $order = null;
        if (class_exists(\App\Services\OrderClient::class)) {
            try {
                $orderClient = app(\App\Services\OrderClient::class);
                $order = $orderClient->getOrder($orderId);
            } catch (\Throwable $e) {
                $order = null;
            }
        }

        $amount = $order['totalAmount'] ?? request()->query('amount', 0);

        return view('payments.checkout', [
            'orderId' => $orderId,
            'amount' => $amount,
            'user' => Auth::user()
        ]);
    }

    /**
     * Server-side checkout processing (creates Payment or delegates to PaymentService).
     */
    public function processCheckout(Request $request)
    {
        $data = $request->validate([
            'order_id' => 'required|integer',
            'amount' => 'required|numeric',
            'method' => 'required|string',
            'payment_payload' => 'nullable|array'
        ]);

        $user = Auth::user();
        $input = [
            'order_id' => $data['order_id'],
            'user_id' => $user->id,
            'amount' => $data['amount'],
            'method' => $data['method'],
            'payment_payload' => $data['payment_payload'] ?? null,
            'currency' => $request->input('currency', 'MYR')
        ];

        $idempotencyKey = (string) Str::uuid();

        // If PaymentService is bound, use it. Otherwise create a simple successful payment record for dev.
        if (app()->bound(\App\Services\PaymentService::class)) {
            /** @var \App\Services\PaymentService $svc */
            $svc = app(\App\Services\PaymentService::class);
            $result = $svc->processPayment($input, $idempotencyKey);
            if (!empty($result['success']) && !empty($result['payment'])) {
                return redirect()->route('payments.success', ['id' => $result['payment']->id]);
            }
            return redirect()->route('payments.failed')->with('error', $result['message'] ?? 'Payment failed');
        }

        // Fallback (dev): insert a Payment row and mark Success using the model directly
        $payment = Payment::create([
            'order_id' => $input['order_id'],
            'user_id' => $input['user_id'],
            'amount' => $input['amount'],
            'currency' => $input['currency'],
            'method' => $input['method'],
            'status' => 'Success',
            'transaction_ref' => 'DEV-' . uniqid(),
            'idempotency_key' => $idempotencyKey,
            'meta' => $input['payment_payload'] ?? null
        ]);

        return redirect()->route('payments.success', ['id' => $payment->id]);
    }

    public function success($id)
    {
        $payment = Payment::with(['order','user'])->findOrFail($id);
        return view('payments.result_success', ['payment' => $payment]);
    }

    public function failed()
    {
        return view('payments.result_failed', ['message' => session('error', 'Payment failed')]);
    }

    public function history()
    {
        $user = Auth::user();
        $payments = Payment::where('user_id', $user->id)->orderBy('created_at', 'desc')->get();
        return view('payments.history', ['payments' => $payments]);
    }

    public function adminHistory()
    {
        $payments = Payment::with(['user','order'])->orderBy('created_at','desc')->paginate(30);
        return view('payments.admin_history', ['payments' => $payments]);
    }
}
