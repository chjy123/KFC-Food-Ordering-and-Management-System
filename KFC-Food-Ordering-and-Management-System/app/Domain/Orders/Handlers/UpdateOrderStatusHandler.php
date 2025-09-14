<?php

namespace App\Domain\Orders\Handlers;

use App\Domain\Orders\Commands\UpdateOrderStatusCommand;
use App\Domain\Orders\Exceptions\OrderAlreadyCompleted;
use App\Domain\Orders\Exceptions\PaymentNotSuccessful;
use App\Domain\Orders\Exceptions\UnsupportedOrderStatus;
use App\Models\Order;
use App\Support\Bus\Command;
use App\Support\Bus\CommandHandler;
use Illuminate\Support\Facades\Auth;

class UpdateOrderStatusHandler implements CommandHandler
{
    public function handle(Command $command)
    {
        /** @var UpdateOrderStatusCommand $command */
        $order = Order::with('payment')->findOrFail($command->orderId);
        if (!Auth::user()?->role === 'admin') {
        throw new \Illuminate\Auth\Access\AuthorizationException('Admins only');
    }
    
        if ($order->status === Order::COMPLETED) {
            throw new OrderAlreadyCompleted("Order #{$order->id} is already Completed.");
        }

       
        $payPretty = optional($order->payment)->payment_status ?? 'Pending';
        $payKey    = strtolower($payPretty); 

        if ($payKey !== 'success') {
            $why = match ($payKey) {
                'pending' => 'Payment is Pending. Order has not yet been paid for.',
                'failed'  => 'Payment Failed. The order payment was unsuccessful.',
                default   => 'Payment not successful. You can only update when payment status is Success.',
            };
            throw new PaymentNotSuccessful("Order #{$order->id} cannot be updated: {$why}");
        }

       
        $now = now();
        switch ($order->status) {
            case Order::RECEIVED:
                $order->status       = Order::PREPARING;
                $order->preparing_at = $now;
                break;

            case Order::PREPARING:
                $order->status       = Order::COMPLETED;
                $order->completed_at = $now;
                break;

            default:
                throw new UnsupportedOrderStatus("Order #{$order->id} has an unsupported status.");
        }

        $order->save();

        return $order->fresh(); 
    }
}
