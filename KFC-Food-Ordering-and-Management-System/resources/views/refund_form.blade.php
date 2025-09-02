<!--@extends('layouts.app')
author’s name： Pang Jun Meng 
@section('content')
<div class="container">
    <h2>Refund Payment #{{ $payment->id }}</h2>

    <div class="card">
        <div class="card-body">
            <p>Order: {{ $payment->order_id }}</p>
            <p>Amount: {{ number_format($payment->amount, 2) }}</p>
            <p>Txn: {{ $payment->transaction_ref }}</p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.payments.refund', ['id' => $payment->id]) }}">
        @csrf
        <button type="submit" class="btn btn-danger mt-3">Confirm Refund</button>
        <a href="{{ route('admin.payments') }}" class="btn btn-secondary mt-3">Cancel</a>
    </form>
</div>
@endsection
-->