<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Food;

class CartController extends Controller
{
    public function index()
    {
        $cart = Cart::with('items.food')->where('user_id', Auth::id())->first();
        return view('cart.index', compact('cart'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'food_id' => 'required|exists:foods,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $food = Food::findOrFail($request->food_id);
        $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);
        $item = $cart->items()->where('food_id', $food->id)->first();

        if ($item) {
            $item->increment('quantity', $request->quantity);
        } else {
            $cart->items()->create([
                'food_id'    => $food->id,
                'quantity'   => $request->quantity,
                'unit_price' => $food->price,
            ]);
        }

        return redirect()->back()->with('status', $food->name . ' has been added to your cart!');
    }

    public function update(Request $request, CartItem $item)
    {
        $request->validate(['quantity' => 'required|integer|min:1']);
        $item->update(['quantity' => $request->quantity]);

        return redirect()->back()->with('status', 'Cart updated!');
    }

    public function remove(CartItem $item)
    {
        $item->delete();
        return redirect()->back()->with('status', 'Item removed from cart!');
    }

    public function clear()
    {
        $cart = Cart::where('user_id', Auth::id())->first();
        if ($cart) {
            $cart->items()->delete();
        }
        return redirect()->back()->with('status', 'Cart cleared!');
    }

    public function checkout()
    {
        $cart = Cart::with('items.food')->where('user_id', Auth::id())->first();
        return view('cart.checkout', compact('cart'));
    }

    public function proceedToPayment()
    {
        $cart = Cart::where('user_id', Auth::id())->first();

        if ($cart) {
            $cart->delete(); // Observer will delete items
        }

        return redirect()->route('payment.page')->with('success', 'Cart submitted. Proceeding to payment.');
    }

    public function continueShopping()
    {
        return redirect()->route('products.index')->with('info', 'You can continue shopping.');
    }

    public function deleteCart(Request $request)
    {
        $cart = Cart::where('user_id', Auth::id())->first();

        if ($cart) {
            $cart->delete(); // Observer handles items deletion
        }

        if ($request->has('order_id')) {
            return redirect()->route('payment.index', $request->order_id)
                             ->with('success', 'Cart deleted. Proceeding to payment.');
        }

        return redirect()->back()->with('status', 'Cart cleared and deleted!');
    }
}
