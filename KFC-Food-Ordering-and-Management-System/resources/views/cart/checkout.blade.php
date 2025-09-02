@include('partials.header')

<div class="container mx-auto py-12">
    <h1 class="text-3xl font-bold text-red-600 mb-6">Checkout</h1>

    <div class="bg-white p-6 rounded-lg shadow">
        <h2 class="text-xl font-semibold mb-4">Order Summary</h2>

        <table class="w-full text-left border">
            <thead>
                <tr class="bg-gray-100">
                    <th class="p-2">Item</th>
                    <th class="p-2">Quantity</th>
                    <th class="p-2">Unit Price</th>
                    <th class="p-2">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cart->items as $item)
                    <tr>
                        <td class="p-2">{{ $item->food->name }}</td>
                        <td class="p-2">{{ $item->quantity }}</td>
                        <td class="p-2">RM {{ number_format($item->unit_price, 2) }}</td>
                        <td class="p-2">RM {{ number_format($item->quantity * $item->unit_price, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-4 text-right">
            <h3 class="text-xl font-bold">
                Total: RM {{ number_format($cart->items->sum(fn($i) => $i->quantity * $i->unit_price), 2) }}
            </h3>
        </div>

        <form method="POST" action="{{ route('orders.create') }}" class="mt-6">
            @csrf
            <button type="submit"
                    class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                Confirm Order
            </button>
        </form>
    </div>
</div>

@include('partials.footer')
