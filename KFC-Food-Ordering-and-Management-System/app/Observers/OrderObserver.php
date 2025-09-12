<?php

namespace App\Observers;

use App\Models\Order;

class OrderObserver
{
    public function created(Order $order)
    {
        // Example: log creation, send notification, update analytics
        \Log::info("Order #{$order->id} created by user {$order->user_id}");
    }

    public function deleting(Order $order)
    {
        // Ensure order details are deleted
        $order->items()->delete();
        \Log::info("Order #{$order->id} deleted by user {$order->user_id}");
    }

    public function updated(Order $order)
    {
        // Example: handle status change (Pending → Paid)
        if ($order->wasChanged('status')) {
            \Log::info("Order #{$order->id} status changed to {$order->status}");
        }
    }
}
