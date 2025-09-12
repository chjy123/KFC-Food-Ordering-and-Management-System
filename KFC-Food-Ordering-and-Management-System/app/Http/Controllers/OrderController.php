<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\Food;
use App\Models\Cart;

class OrderController extends Controller
{
    /**
     * Create order from "Order Now" or "Checkout"
     */
    public function create(Request $request)
    {
        if ($request->action === 'order') {
            $request->validate([
                'food_id' => 'required|exists:foods,id',
                'quantity' => 'required|integer|min:1'
            ]);

            $food = Food::findOrFail($request->food_id);

            $order = Order::create([
                'user_id' => Auth::id(),
                'status' => 'Received',
                'total_amount' => 0
            ]);

            $order->items()->create([
                'food_id'    => $food->id,
                'quantity'   => $request->quantity,
                'unit_price' => $food->price,
            ]);

            $order->update([
                'total_amount' => $order->items->sum(fn($i) => $i->quantity * $i->unit_price)
            ]);

            return redirect()->route('orders.show', $order->id)
                             ->with('status', 'Order placed successfully!');
        }

        if ($request->action === 'checkout') {
            $cart = Cart::with('items.food')->where('user_id', Auth::id())->first();

            if (!$cart || $cart->items->isEmpty()) {
                return redirect()->route('cart.index')->with('status', 'Your cart is empty!');
            }

            $order = Order::create([
                'user_id' => Auth::id(),
                'status' => 'Received',
                'total_amount' => 0
            ]);

            foreach ($cart->items as $item) {
                $order->items()->create([
                    'food_id'    => $item->food_id,
                    'quantity'   => $item->quantity,
                    'unit_price' => $item->unit_price,
                ]);
            }

            $order->update([
                'total_amount' => $order->items->sum(fn($i) => $i->quantity * $i->unit_price)
            ]);

            return redirect()->route('orders.show', $order->id)
                             ->with('status', 'Checkout successful!');
        }
    }

    /**
     * Show a single order
     */
    public function show(Order $order)
    {
        $order->load('items.food');
        return view('orders.show', compact('order'));
    }

    /**
     * Continue Shopping → deletes the order + details (handled by observer)
     */
    public function continueShopping(Order $order)
    {
        $order->delete();

        return redirect()->route('menu.index')
                         ->with('info', 'Order cancelled. You can continue shopping.');
    }
}
