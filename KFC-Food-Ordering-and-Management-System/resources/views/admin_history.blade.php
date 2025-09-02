<!--@extends('layouts.app')
author’s name： Pang Jun Meng 
@section('content')
<div class="container">
    <h2>Admin - Payment Dashboard</h2>

    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
    @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th><th>User</th><th>Order</th><th>Amount</th><th>Method</th><th>Status</th><th>Txn</th><th>Date</th><th>Action</th>
            </tr>
        </thead>
        <tbody>
        @foreach($payments as $p)
            <tr>
                <td>{{ $p->id }}</td>
                <td>{{ $p->user->email ?? 'N/A' }}</td>
                <td>{{ $p->order_id }}</td>
                <td>{{ number_format($p->amount,2) }}</td>
                <td>{{ $p->method }}</td>
                <td>{{ $p->status }}</td>
                <td>{{ $p->transaction_ref }}</td>
                <td>{{ $p->created_at }}</td>
                <td>
                    @if ($p->status === 'Success')
                        <a href="{{ route('admin.payments.refund.form', ['id' => $p->id]) }}" class="btn btn-sm btn-warning">Refund</a>
                    @else
                        -
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    {{ $payments->links() }}
</div>
@endsection
-->