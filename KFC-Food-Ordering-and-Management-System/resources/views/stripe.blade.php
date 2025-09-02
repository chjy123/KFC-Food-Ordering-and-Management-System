<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Stripe Payment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container col-md-4">
    <div class="card mt-5">
        <div class="card-header">
            <h4>Stripe Payment Gateway</h4>
        </div>
        <div class="card-body">

        @session("success")
        <div class="alert alert-success">
            {{ $value }}
        </div>
        @endsession

        <!--
    protected $fillable = [
        'order_id',
        'user_id',
        'amount',
        'currency',
        'method',
        'status',
        'transaction_ref',
        'idempotency_key',
        'meta'
    ];
-->
    
        <div class="p-3 bg-light bg-opacity-10">
            <h6 class="card-title mb-3">Order Summary</h6>
            <!--<div class="d-flex justify-content-between mb-1 small">Total Amount: RM<strong>${{ $total }}</strong></h6>-->
            
   