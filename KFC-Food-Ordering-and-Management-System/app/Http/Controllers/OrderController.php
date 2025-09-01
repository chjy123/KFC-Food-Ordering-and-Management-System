<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Food;
use App\Models\Cart;

class OrderController extends Controller
{
    public function create(Request $request)
    {
        if ($request->action === 'order') {
            // ✅ Order Now from food_show
            $request->validate([
                'food_id' => 'required|exists:foods,id',
                'quantity' => 'required|integer|min:1'
            ]);

            $food = Food::findOrFail($request->food_id);

            $order = Order::create([
                'user_id' => Auth::id(),
                'status' => 'Pending',
                'total_amount' => 0
            ]);

            $order->items()->create([
                'food_id' => $food->id,
                'quantity' => $request->quantity,
                'unit_price' => $food->price,
            ]);

            $order->total_amount = $order->items->sum(fn($i) => $i->quantity * $i->unit_price);
            $order->save();

            return redirect()->route('orders.show', $order->id)
                             ->with('status', 'Order placed successfully!');
        }

        // ✅ Checkout from cart
        $cart = Cart::with('items.food')->where('user_id', Auth::id())->first();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('status', 'Your cart is empty!');
        }

        $order = Order::create([
            'user_id' => Auth::id(),
            'status' => 'Pending',
            'total_amount' => 0
        ]);

        foreach ($cart->items as $item) {
            $order->items()->create([
                'food_id'    => $item->food_id,
                'quantity'   => $item->quantity,
                'unit_price' => $item->unit_price,
            ]);
        }

        $order->total_amount = $order->items->sum(fn($i) => $i->quantity * $i->unit_price);
        $order->save();

        // ✅ Clear cart after checkout
        $cart->items()->delete();

        return redirect()->route('orders.show', $order->id)
                         ->with('status', 'Checkout successful!');
    }

    public function show(Order $order)
    {
        $order->load('items.food');
        return view('orders.show', compact('order'));
    }
}
