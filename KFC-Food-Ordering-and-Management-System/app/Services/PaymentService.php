<?php

namespace App\Services;

use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Payments\Handlers\PaymentHandlerInterface;
use App\Services\OrderClient;

class PaymentService
{
    /** @var array<string, PaymentHandlerInterface> */
    protected $handlers = [];

    public function __construct(OrderClient $orderClient)
    {
        // We'll resolve handlers from container when registered (via provider)
        $this->orderClient = $orderClient;

        // You may register handlers here or via provider
        if (app()->bound(\App\Payments\Handlers\CardSimHandler::class)) {
            $this->registerHandler('cardsim', app(\App\Payments\Handlers\CardSimHandler::class));
        }
        if (app()->bound(\App\Payments\Handlers\WalletSimHandler::class)) {
            $this->registerHandler('walletsim', app(\App\Payments\Handlers\WalletSimHandler::class));
        }
        // COD is handled internally: mark success without gateway
        $this->registerHandler('cod', new class implements PaymentHandlerInterface {
            public function pay(array $data): array {
                return ['success' => true, 'transactionRef' => 'COD-' . uniqid(), 'message' => 'Cash on Delivery'];
            }
            public function refund(string $transactionRef): array {
                return ['success' => false, 'message' => 'COD refunds must be handled manually'];
            }
        });
    }

    public function registerHandler(string $code, PaymentHandlerInterface $handler)
    {
        $this->handlers[$code] = $handler;
    }

    /**
     * Orchestrate payment processing
     *
     * @param array $input validated data (order_id,user_id,amount,method,payment_payload)
     * @param string $idempotencyKey
     * @return array ['success'=>bool, 'payment'=>Payment|null, 'message'=>string|null, 'httpStatus'=>int|null]
     */
    public function processPayment(array $input, string $idempotencyKey): array
    {
        // 1) idempotency quick check
        $existing = Payment::where('idempotency_key', $idempotencyKey)->first();
        if ($existing) {
            return ['success' => true, 'payment' => $existing];
        }

        // 2) Re-validate order via OrderClient
        $order = $this->orderClient->getOrder($input['order_id']);
        if (!$order || ($order['user_id'] ?? null) != $input['user_id']) {
            return ['success' => false, 'message' => 'Order validation failed', 'httpStatus' => 400];
        }
        // Compare amounts (float-safe)
        if (bccomp((string)$order['totalAmount'], (string)$input['amount'], 2) !== 0) {
            return ['success' => false, 'message' => 'Amount mismatch with order', 'httpStatus' => 400];
        }

        // 3) create pending payment and run handler
        try {
            return DB::transaction(function () use ($input, $idempotencyKey) {
                $payment = Payment::create([
                    'order_id' => $input['order_id'],
                    'user_id' => $input['user_id'],
                    'amount' => $input['amount'],
                    'currency' => $input['currency'] ?? 'MYR',
                    'method' => $input['method'],
                    'status' => 'Pending',
                    'idempotency_key' => $idempotencyKey,
                    'meta' => $input['payment_payload'] ?? null
                ]);

                $method = $input['method'];
                if (!isset($this->handlers[$method])) {
                    $payment->update(['status' => 'Failed']);
                    return ['success' => false, 'message' => 'Unsupported payment method', 'httpStatus' => 400];
                }

                /** @var PaymentHandlerInterface $handler */
                $handler = $this->handlers[$method];

                $result = $handler->pay(['payment' => $payment, 'payload' => $input['payment_payload'] ?? []]);

                if ($result['success']) {
                    $payment->update([
                        'status' => 'Success',
                        'transaction_ref' => $result['transactionRef'] ?? null
                    ]);
                    // Notify Order Module (a simple call, real system should queue)
                    $this->orderClient->updatePaymentStatus($payment->order_id, 'Paid', $payment->id);
                    return ['success' => true, 'payment' => $payment];
                } else {
                    $payment->update(['status' => 'Failed']);
                    return ['success' => false, 'message' => $result['message'] ?? 'Gateway error', 'httpStatus' => 402];
                }
            });
        } catch (\Throwable $ex) {
            Log::error('PaymentService exception: ' . $ex->getMessage());
            return ['success' => false, 'message' => 'Server error', 'httpStatus' => 500];
        }
    }

    public function refundPayment(int $paymentId): array
    {
        $payment = Payment::find($paymentId);
        if (!$payment) {
            return ['success'=>false,'message'=>'Payment not found','httpStatus'=>404];
        }
        if (!$payment->transaction_ref) {
            return ['success'=>false,'message'=>'No transaction reference available','httpStatus'=>400];
        }

        $method = $payment->method;
        if (!isset($this->handlers[$method])) {
            return ['success'=>false,'message'=>'Unsupported method for refund','httpStatus'=>400];
        }

        $res = $this->handlers[$method]->refund($payment->transaction_ref);
        if ($res['success']) {
            $payment->update(['status' => 'Refunded']);
            return ['success'=>true,'message'=>'Refunded'];
        }
        return ['success'=>false,'message'=>$res['message'] ?? 'Refund failed','httpStatus'=>402];
    }
}
