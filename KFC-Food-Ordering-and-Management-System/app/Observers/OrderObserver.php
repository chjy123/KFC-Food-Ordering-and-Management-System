<?php

#author’s name： Lim Jun Hong
namespace App\Observers;

use App\Models\Order;

class OrderObserver
{
    public function created(Order $order)
    {
        \Log::info("Order #{$order->id} created by user {$order->user_id}");
    }

    public function deleting(Order $order)
    {
        $order->items()->delete();
        \Log::info("Order #{$order->id} deleted by user {$order->user_id}");
    }

    public function updated(Order $order)
    {
        if ($order->wasChanged('status')) {
            \Log::info("Order #{$order->id} status changed to {$order->status}");
        }
    }
}
