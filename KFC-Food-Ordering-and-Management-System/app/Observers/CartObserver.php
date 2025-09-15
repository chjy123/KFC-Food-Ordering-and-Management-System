<?php

#author’s name： Lim Jun Hong
namespace App\Observers;

use App\Models\Cart;

class CartObserver
{
    public function deleting(Cart $cart)
    {
        $cart->items()->delete();
        \Log::info("Cart for user {$cart->user_id} deleted.");
    }

    public function updated(Cart $cart)
    {
        \Log::info("Cart for user {$cart->user_id} updated.");
    }
}
