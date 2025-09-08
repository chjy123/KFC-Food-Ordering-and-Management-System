<?php
#Author's Name: Pang Jun Meng
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Order;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class PaymentApiController extends Controller
{
    public function __construct(private PaymentService $payments) {}

    public function checkout(Request $request)
    {
        $validated = $request->validate([
            'order_id' => ['required','integer','min:1'],
            'method'   => ['nullable','in:card'],
            'idempotency_key' => ['nullable','string','max:128'],
        ]);

        $order = Order::findOrFail($validated['order_id']);
        abort_unless($order->user_id === $request->user()->id, 403);

        $result = $this->payments->processPayment($order, $request->user(), [
            'method'      => $validated['method'] ?? 'card',
            'success_url' => route('payment.success').'?session_id={CHECKOUT_SESSION_ID}&order_id='.$order->id,
            'cancel_url'  => route('payment.cancel').'?order_id='.$order->id,
            'idempotency_key' => $validated['idempotency_key'] ?? null,
        ]);

        return response()->json([
            'status'          => 'ok',
            'session_url'     => $result['redirect_url'],
            'session_id'      => $result['session_id'],
            'idempotency_key' => $result['idempotency_key'] ?? null,
        ]);
    }

    public function show($id)
    {
        $p = Payment::findOrFail($id);
        $user = request()->user();
        abort_unless($user->id === $p->user_id || $user->role === 'admin', 403);

        return response()->json([
            'payment_id'     => $p->id,
            'order_id'       => $p->order_id,
            'user_id'        => $p->user_id,
            'payment_method' => $p->payment_method,
            'payment_status' => $p->payment_status,
            'payment_date'   => $p->payment_date?->utc()->toIso8601String(),
            'amount'         => (float) $p->amount,
            'transaction_ref'=> $p->transaction_ref,
            'card_brand'     => $p->card_brand,
            'card_last4'     => $p->card_last4,
        ]);
    }

    public function listByUser($id)
    {
        $user = request()->user();
        abort_unless($user->id == $id || $user->role === 'admin', 403);

        $page = (int) request('page', 1);
        $pag  = Payment::where('user_id', $id)->orderByDesc('id')->paginate(15, ['*'], 'page', $page);

        return response()->json([
            'data' => $pag->getCollection()->map(fn($p) => [
                'payment_id'     => $p->id,
                'order_id'       => $p->order_id,
                'user_id'        => $p->user_id,
                'payment_method' => $p->payment_method,
                'payment_status' => $p->payment_status,
                'payment_date'   => $p->payment_date?->utc()->toIso8601String(),
                'amount'         => (float) $p->amount,
                'transaction_ref'=> $p->transaction_ref,
                'card_brand'     => $p->card_brand,
                'card_last4'     => $p->card_last4,
            ])->all(),
            'meta' => [
                'page'     => $pag->currentPage(),
                'per_page' => $pag->perPage(),
                'total'    => $pag->total(),
            ],
        ]);
    }
}
