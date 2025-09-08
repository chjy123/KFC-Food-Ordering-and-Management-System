<?php
// Author's Name: Pang Jun Meng
namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Services\PaymentService; 

class PaymentController extends Controller
{
    public function __construct(private PaymentService $payments) {}

    public function index(Order $order)
    {
        // Server-side re-validation (amount/ownership) before showing the pay button
        abort_unless($order->user_id === auth()->id(), 403);

        return view('payment.index', compact('order'));
    }

    public function checkout(Request $request, Order $order)
    {
        abort_unless($order->user_id === auth()->id(), 403);

        $context = [
            'method'      => 'card', // Visa/Mastercard/UnionPay via Stripe card rails
            'success_url' => route('payment.success') . '?session_id={CHECKOUT_SESSION_ID}&order_id=' . $order->id,
            'cancel_url'  => route('payment.cancel')  . '?order_id=' . $order->id,
        ];

        $result = $this->payments->processPayment($order, $request->user(), $context);

        return redirect()->away($result['redirect_url']);

    }

    public function success(Request $request)
    {
        $sessionId = $request->query('session_id');
        $orderId   = (int) $request->query('order_id');

        abort_unless($sessionId && $orderId, 400);

        $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));
        $session = $stripe->checkout->sessions->retrieve($sessionId, ['expand' => ['payment_intent.payment_method']]);

        // Stripe canonical result
        $paid = ($session->payment_status === 'paid');

        // Prevent duplicate rows if user refreshes (idempotent by session id)
        $existing = Payment::where('transaction_ref', $session->payment_intent)->first();
        if (!$existing) {
            $brand = null; $last4 = null;
            if (($pi = $session->payment_intent) && isset($session->payment_intent->payment_method->card)) {
                $brand = $session->payment_intent->payment_method->card->brand ?? null;
                $last4 = $session->payment_intent->payment_method->card->last4 ?? null;
            }

            Payment::create([
                'user_id'         => auth()->id(),
                'order_id'        => $orderId,
                'payment_method'  => 'card',
                'payment_status'  => $paid ? 'success' : 'failed',
                'payment_date'   => now()->utc(),                     // store UTC
                'amount'          => $session->amount_total / 100.0,   // MYR
                'transaction_ref' => is_string($session->payment_intent) ? $session->payment_intent : ($session->payment_intent->id ?? $sessionId),
                'card_brand'      => $brand ?? null,
                'card_last4'      => $last4 ?? null,
                'idempotency_key' => $session->metadata->idempotency_key ?? null,
            ]);
        }

        return view('payment.success', [
            'success' => $paid,
            'orderId' => $orderId,
            'homeUrl' => route('home') ?? url('/'),
            'retryUrl'=> route('payment.index', $orderId),
        ]);
    }

    public function cancel(Request $request)
    {
        $orderId = (int) $request->query('order_id');

        // If we know the order and it belongs to the user, log a "failed" attempt once
        if ($orderId) {
            $order = \App\Models\Order::find($orderId);

            if ($order && $order->user_id === auth()->id()) {
                // Avoid duplicating a failed record if the user refreshes the cancel page repeatedly.
                $alreadyLogged = \App\Models\Payment::where('order_id', $orderId)
                    ->where('payment_status', 'failed')
                    ->whereDate('payment_date', now()->toDateString())
                    ->exists();

            if (!$alreadyLogged) {
                \App\Models\Payment::create([
                    'user_id'        => auth()->id(),
                    'order_id'       => $orderId,
                    'payment_method' => 'card',
                    'payment_status' => 'failed',      
                    'amount'         => $order->total_amount,
                    'payment_date'    => now()->utc(),
                    'transaction_ref'=> null,         
                    'card_brand'     => null,
                    'card_last4'     => null,
                    'idempotency_key'=> null,
                ]);
            }
        }
    }

    return view('payment.cancel', [
        'orderId' => $orderId,
        'homeUrl' => route('home') ?? url('/'),
        'retryUrl'=> route('payment.index', $orderId),
    ]);
    }

    // Profile page -> payment history table
    public function history()
    {
        $payments = \App\Models\Payment::where('user_id', auth()->id())
            ->orderByDesc('id')     
            ->select([
                'id as payment_id',                  
                'payment_method',
                'payment_status',
                'payment_date',                      
                'amount',
        ])
        ->paginate(15);                           

        return view('payment.history', compact('payments'));
    }
}

