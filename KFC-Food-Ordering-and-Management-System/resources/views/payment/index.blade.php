@include('partials.header')

<div class="container mx-auto py-12">
    <h1 class="text-3xl font-bold text-red-600 mb-6">Payment</h1>

    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-semibold mb-4">Order #{{ $order->id }}</h2>

        <table class="w-full text-left border mb-6">
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
                            <img src="{{ $item->food->image_url ?: 'https://via.placeholder.com/80x80?text=Food' }}"
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

        <div class="text-right mb-6">
            <h3 class="text-xl font-bold">Total: RM {{ number_format($order->total_amount, 2) }}</h3>
        </div>

        {{-- ✅ Payment Form --}}
        <form method="POST" action="{{ route('payment.process', $order->id) }}">
            @csrf
            <div class="mb-4">
                <label class="block mb-2 font-medium">Payment Method</label>
                <select name="payment_method" class="border rounded w-full p-2" required>
                    <option value="card">Credit/Debit Card</option>
                    <option value="wallet">E-Wallet</option>
                    <option value="cod">Cash on Delivery</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block mb-2 font-medium">Card Number</label>
                <input type="text" name="card_number" class="border rounded w-full p-2" placeholder="1234 5678 9012 3456">
            </div>

            <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                Pay Now
            </button>
        </form>
    </div>
</div>

@include('partials.footer')
