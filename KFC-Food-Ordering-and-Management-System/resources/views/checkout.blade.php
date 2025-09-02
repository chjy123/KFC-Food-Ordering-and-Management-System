<!-- resources/views/payments/checkout.blade.php -->
@extends('layouts.app')
<!-- author’s name： Pang Jun Meng -->
@section('content')
<div class="container">
    <h2>Checkout</h2>

    <div class="card mb-3">
        <div class="card-body">
            <p><strong>Order ID:</strong> {{ $orderId }}</p>
            <p><strong>Amount:</strong> {{ number_format($amount, 2) }} MYR</p>
        </div>
    </div>

    <form method="POST" action="{{ route('payments.checkout.process') }}">
        @csrf
        <input type="hidden" name="order_id" value="{{ $orderId }}">
        <input type="hidden" name="amount" value="{{ $amount }}">

        <div class="form-group mb-3">
            <label for="method">Payment Method</label>
            <select name="method" id="method" class="form-control">
                <option value="cardsim">Card (Sim)</option>
                <option value="walletsim">Wallet (Sim)</option>
                <option value="cod">Cash on Delivery</option>
            </select>
        </div>

        <!-- optional payment payload area (for test inputs like card last4) -->
        <div id="card-fields" style="display:none;">
            <div class="form-group mb-3">
                <label for="card_last4">Card last 4 digits (sim)</label>
                <input type="text" name="payment_payload[card_last4]" id="card_last4" class="form-control" maxlength="4">
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Pay {{ number_format($amount, 2) }} MYR</button>
    </form>
</div>

<script>
document.getElementById('method').addEventListener('change', function() {
    var v = this.value;
    document.getElementById('card-fields').style.display = (v === 'cardsim' ? 'block' : 'none');
});
</script>
@endsection
