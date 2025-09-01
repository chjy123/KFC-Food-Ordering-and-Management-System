<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class PaymentController extends Controller
{
    public function index(Order $order)
{
    // ✅ Update order status
    $order->update(['status' => 'Received']);

    // ✅ Delete cart ONLY if order came from Cart
    $cart = \App\Models\Cart::where('user_id', auth()->id())->first();
    if ($cart && $order->items->pluck('food_id')->intersect($cart->items->pluck('food_id'))->isNotEmpty()) {
        $cart->items()->delete();
        $cart->delete();
    }

    $order->load('items.food');
    return view('payment.index', compact('order'));
}


    public function process(Request $request, Order $order)
    {
        // ✅ Just a dummy simulation for now
        $request->validate([
            'payment_method' => 'required',
            'card_number' => 'nullable|string'
        ]);

        // Mark order as Paid
        $order->update([
            'status' => 'Paid'
        ]);

        return redirect()->route('orders.show', $order->id)
                         ->with('status', 'Payment successful!');
    }
}
