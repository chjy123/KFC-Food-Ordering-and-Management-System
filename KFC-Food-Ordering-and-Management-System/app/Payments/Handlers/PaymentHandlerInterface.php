<?php
#author’s name： Pang Jun Meng
namespace App\Payments\Handlers;

interface PaymentHandlerInterface
{
    /**
     * Process payment (synchronous)
     *
     * @param array $data ['payment' => PaymentModel, 'payload' => array]
     * @return array ['success' => bool, 'transactionRef' => string|null, 'message' => string|null]
     */
    public function pay(array $data): array;

    /**
     * Refund payment
     * @param string $transactionRef
     * @return array ['success' => bool, 'message' => string|null]
     */
    public function refund(string $transactionRef): array;
}
