<?php
#Author's Name: Pang Jun Meng
namespace App\Payments;

use App\Models\Order;
use App\Models\User;
use Stripe\StripeClient;

class StripeCardHandler implements PaymentHandlerInterface
{
    public function __construct(private StripeClient $stripe) {}

    public function pay(Order $order, User $user, array $context = []): array
    {
        $idemp = $context['idempotency_key'] ?? null;

        // build Checkout Session params
        $params = [
            'mode' => 'payment',
            'payment_method_types' => ['card'], // card rails (Visa/Mastercard/UnionPay via card)
            'line_items' => [[
                'price_data' => [
                    'currency' => 'myr',
                    'product_data' => ['name' => "KFC Order #{$order->id}"],
                    'unit_amount' => (int) round($order->total_amount * 100),
                ],
                'quantity' => 1,
            ]],
            'success_url' => $context['success_url'] ?? url('/payment/success'),
            'cancel_url'  => $context['cancel_url']  ?? url('/payment/cancel'),
            'metadata' => [
                'order_id'         => $order->id,
                'user_id'          => $user->id,
                // keep it in metadata so you can read it on success()
                'idempotency_key'  => $idemp,
            ],
        ];

        // pass idempotency header to Stripe request
        $opts = $idemp ? ['idempotency_key' => $idemp] : [];

        $session = $this->stripe->checkout->sessions->create($params, $opts);

        return [
            'redirect_url'    => $session->url,
            'session_id'      => $session->id,
            'idempotency_key' => $idemp,
        ];
    }
}