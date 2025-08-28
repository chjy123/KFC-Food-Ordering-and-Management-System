<?php
#author’s name： Pang Jun Meng
namespace App\Models;

#use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model {
    /*use HasFactory;
    protected $fillable = ['order_id','payment_method','payment_status','payment_date'];
    public function order() { return $this->belongsTo(Order::class); }*/

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

    protected $casts = [
        'meta' => 'array'
    ];

    // object references (Eloquent relations)
    public function order()
    {
        return $this->belongsTo(\App\Models\Order::class, 'order_id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
}

