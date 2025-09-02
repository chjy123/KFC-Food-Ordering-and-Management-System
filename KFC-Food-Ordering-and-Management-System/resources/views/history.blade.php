@extends('layouts.app')
<!-- author’s name： Pang Jun Meng -->
@section('content')
<div class="container">
    <h2>Your Payment History</h2>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Payment ID</th>
                <th>Order</th>
                <th>Amount</th>
                <th>Method</th>
                <th>Status</th>
                <th>Txn Ref</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
        @forelse ($payments as $p)
            <tr>
                <td>{{ $p->id }}</td>
                <td>{{ $p->order_id }}</td>
                <td>{{ number_format($p->amount, 2) }} {{ $p->currency }}</td>
                <td>{{ $p->method }}</td>
                <td>{{ $p->status }}</td>
                <td>{{ $p->transaction_ref }}</td>
                <td>{{ $p->created_at }}</td>
            </tr>
        @empty
            <tr><td colspan="7">No payments found.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
