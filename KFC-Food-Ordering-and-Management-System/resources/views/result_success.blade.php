@extends('layouts.app')
<!-- author’s name： Pang Jun Meng -->
@section('content')
<div class="container">
    <h2>Payment Successful</h2>

    <div class="card">
        <div class="card-body">
            <p>Payment ID: {{ $payment->id }}</p>
            <p>Transaction Reference: {{ $payment->transaction_ref }}</p>
            <p>Amount: {{ number_format($payment->amount, 2) }} {{ $payment->currency }}</p>
            <p>Status: {{ $payment->status }}</p>
            <p>Paid at: {{ $payment->updated_at }}</p>
        </div>
    </div>

    <a href="{{ route('payments.history') }}" class="btn btn-link mt-3">View Payment History</a>
</div>
@endsection
