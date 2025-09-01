@include('partials.header')

<div class="container mx-auto py-12">
  <h1 class="text-3xl font-bold text-red-600 mb-6">My Cart</h1>

  @if(session('status'))
    <div class="mb-4 p-3 rounded bg-green-50 text-green-700">{{ session('status') }}</div>
  @endif

  @if($cart && $cart->items->count() > 0)
    <table class="w-full text-left border">
        <thead>
            <tr class="bg-gray-100">
                <th class="p-2">Image</th>
                <th class="p-2">Item</th>
                <th class="p-2">Quantity</th>
                <th class="p-2">Price</th>
                <th class="p-2">Subtotal</th>
                <th class="p-2">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cart->items as $item)
                <tr>
                    <td class="p-2">
                        <img src="{{ $item->food->image_url ?: 'https://via.placeholder.com/80x80?text=Food' }}"
                             alt="{{ $item->food->name }}" class="w-16 h-16 object-cover rounded">
                    </td>
                    <td class="p-2 font-semibold">{{ $item->food->name }}</td>
                    <td class="p-2">
                        <form method="POST" action="{{ route('cart.update', $item->id) }}">
                            @csrf
                            <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" class="w-16 border rounded p-1">
                            <button type="submit" class="ml-2 px-2 py-1 bg-blue-500 text-white rounded">Update</button>
                        </form>
                    </td>
                    <td class="p-2">RM {{ number_format($item->unit_price, 2) }}</td>
                    <td class="p-2">RM {{ number_format($item->quantity * $item->unit_price, 2) }}</td>
                    <td class="p-2">
                        <form method="POST" action="{{ route('cart.remove', $item->id) }}">
                            @csrf
                            @method('DELETE')
                            <button class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700">Remove</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-6 flex justify-between">
        <form method="POST" action="{{ route('cart.clear') }}">
            @csrf
            <button type="submit" class="px-6 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400">Clear Cart</button>
        </form>

        <form method="POST" action="{{ route('orders.create') }}">
            @csrf
            <input type="hidden" name="action" value="checkout">
            <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Checkout</button>
        </form>
    </div>
  @else
    <p class="text-gray-600">Your cart is empty.</p>
  @endif
</div>

@include('partials.footer')
