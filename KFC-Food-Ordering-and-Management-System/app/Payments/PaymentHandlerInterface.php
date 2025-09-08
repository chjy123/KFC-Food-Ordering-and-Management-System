<?php
#Author's Name: Pang Jun Meng
namespace App\Payments;

use App\Models\Order;
use App\Models\User;

interface PaymentHandlerInterface
{
    public function pay(Order $order, User $user, array $context = []): array;
}
