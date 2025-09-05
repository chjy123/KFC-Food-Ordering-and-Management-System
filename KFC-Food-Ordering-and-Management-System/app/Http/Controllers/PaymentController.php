<?php
// Author's Name: Pang Jun Meng
namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function index(Order $order)
    {
        // Server-side re-validation (amount/ownership) before showing the pay button
        abort_unless($order->user_id === auth()->id(), 403);

        return view('payment.index', compact('order'));
    }

    public function checkout(Request $request, Order $order)
    {
        abort_unless($order->user_id === auth()->id(), 403);

        // Re-validate amount from authoritative order
        $amount = $order->total_amount; // MYR, 2dp in your DB

        // Generate idempotency key to avoid duplicates (save on success later)
        $idemp = (string) Str::uuid();

        // Create Stripe Checkout Session
        $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));

        $session = $stripe->checkout->sessions->create([
            'mode' => 'payment',
            'payment_method_types' => ['card'],     // Visa/Mastercard only
            'line_items' => [[
                'price_data' => [
                    'currency' => 'myr',
                    'product_data' => ['name' => "KFC Order #{$order->id}"],
                    // Stripe expects integer cents:
                    'unit_amount' => (int) round($amount * 100),
                ],
                'quantity' => 1,
            ]],
            'success_url' => route('payment.success').'?session_id={CHECKOUT_SESSION_ID}&order_id='.$order->id,
            'cancel_url'  => route('payment.cancel').'?order_id='.$order->id,
            'metadata' => [
                'order_id' => $order->id,
                'user_id'  => auth()->id(),
                'idempotency_key' => $idemp,
            ],
        ]);

        // Redirect user to Stripe-hosted page
        return redirect()->away($session->url);
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
                'payment_dDate'   => now()->utc(),                     // store UTC
                'amount'          => $session->amount_total / 100.0,   // MYR
                'transaction_ref' => is_string($session->payment_intent) ? $session->payment_intent : ($session->payment_intent->id ?? $sessionId),
                'card_brand'      => $brand,
                'card_last4'      => $last4,
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

        return view('payment.cancel', [
            'orderId' => $orderId,
            'homeUrl' => route('home') ?? url('/'),
            'retryUrl'=> route('payment.index', $orderId),
        ]);
    }

    // Profile page -> payment history table
    public function history()
    {
        $payments = Payment::where('user_id', auth()->id())
            ->orderByDesc('payment_dDate')
            ->get(['payment_id','payment_method','payment_status','payment_date','amount']);

        return view('profile.payments', compact('payments'));
    }
}

