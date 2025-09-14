@include('partials.header')

<!-- Author's Name: Lim Jun Hong -->
<div class="container mx-auto py-12">
    <h1 class="text-3xl font-bold text-red-600 mb-6">Your Order</h1>

    @if(session('status'))
        <div class="mb-4 p-3 rounded bg-green-50 text-green-700">{{ session('status') }}</div>
    @endif

    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-semibold mb-4">Order #{{ $order->id }}</h2>

        <table class="w-full text-left border">
            <thead>
                <tr class="bg-gray-100">
                    <th class="p-2">Image</th>
                    <th class="p-2">Item</th>
                    <th class="p-2">Quantity</th>
                    <th class="p-2">Unit Price</th>
                    <th class="p-2">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                    <tr>
                        <td class="p-2">
                            <img src="{{ $item->food->image_url ? asset('storage/'.$item->food->image_url) : 'https://via.placeholder.com/80x80?text=Food' }}"
                                 alt="{{ $item->food->name }}" class="w-16 h-16 object-cover rounded">
                        </td>
                        <td class="p-2">{{ $item->food->name }}</td>
                        <td class="p-2">{{ $item->quantity }}</td>
                        <td class="p-2">RM {{ number_format($item->unit_price, 2) }}</td>
                        <td class="p-2">RM {{ number_format($item->quantity * $item->unit_price, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-4 text-right">
            <h3 class="text-xl font-bold">Total: RM {{ number_format($order->total_amount, 2) }}</h3>
        </div>
    </div>

    <div class="mt-6 flex justify-end">
    <form action="{{ route('orders.continueShopping', $order->id) }}" method="POST">
    @csrf
    @method('DELETE')
    <button type="submit" 
        class="px-6 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 mr-3">
        Continue Shopping
    </button>
</form>


    <form action="{{ route('cart.deleteCart') }}" method="POST">
    @csrf
    <input type="hidden" name="order_id" value="{{ $order->id }}">
    <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
        Proceed to Payment
    </button>
</form>



</div>

</div>

@include('partials.footer')
