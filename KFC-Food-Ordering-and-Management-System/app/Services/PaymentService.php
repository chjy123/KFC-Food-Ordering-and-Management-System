<?php
#Author's Name: Pang Jun Meng
namespace App\Services;

use App\Models\Order;
use App\Models\User;
use App\Payments\PaymentHandlerInterface;
use App\Payments\StripeCardHandler;
use Stripe\StripeClient;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class PaymentService
{
    /** @var array<string, PaymentHandlerInterface> */
    protected array $handlers = [];

    public function __construct(StripeClient $stripe)
    {
        // Register our active strategy
        $this->registerHandler('card', new StripeCardHandler($stripe));
    }

    public function registerHandler(string $code, PaymentHandlerInterface $handler): void
    {
        $this->handlers[$code] = $handler;
    }

    protected function selectHandler(string $code): PaymentHandlerInterface
    {
        if (! isset($this->handlers[$code])) {
            throw new \InvalidArgumentException("Unsupported payment method: {$code}");
        }
        return $this->handlers[$code];
    }

    /**
     * Orchestrates validation, idempotency, then delegates.
     */
    public function processPayment(Order $order, User $user, array $context = []): array
    {
        $method  = Arr::get($context, 'method', 'card');
        // generate an idempotency key and pass it down
        $context['idempotency_key'] = $context['idempotency_key'] ?? (string) Str::uuid();

        $handler = $this->selectHandler($method);

        return $handler->pay($order, $user, $context);
    }
}
