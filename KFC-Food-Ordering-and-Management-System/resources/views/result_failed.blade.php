@extends('layouts.app')
<!-- author’s name： Pang Jun Meng -->
@section('content')
<div class="container">
    <h2>Payment Failed</h2>
    <div class="alert alert-danger">
        {{ $message ?? 'Payment could not be processed.' }}
    </div>
    <a href="{{ url()->previous() }}" class="btn btn-secondary">Back</a>
</div>
@endsection
