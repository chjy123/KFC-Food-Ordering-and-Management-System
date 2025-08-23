<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model {
    use HasFactory;
    public const PREPARING = 'Preparing';
    public const READY     = 'Ready';
    public const COMPLETED = 'Completed';
    public const CANCELLED = 'Cancelled';

    protected $fillable = ['user_id','order_date','status','total_amount'];
    protected $casts = [
        'order_date'   => 'datetime',
        'total_amount' => 'decimal:2',
    ];

    public function items()   { return $this->hasMany(OrderDetail::class); }
    public function payment() { return $this->hasOne(Payment::class); }
    public function user()    { return $this->belongsTo(User::class); }
    public function recalcTotal(): void {
        $this->loadMissing('items');
        $this->total_amount = $this->items->sum(fn($i)=>$i->quantity * $i->unit_price);
        $this->save();
    }
}

