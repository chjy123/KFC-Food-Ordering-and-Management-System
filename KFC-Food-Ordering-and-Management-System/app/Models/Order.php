<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;


    public const RECEIVED  = 'Received';    
    public const PREPARING = 'Preparing';
    public const COMPLETED = 'Completed';

    protected $fillable = [
        'user_id', 'order_date', 'status', 'total_amount',
        'received_at', 'preparing_at', 'completed_at',
    ];

    protected $casts = [
        'order_date'   => 'datetime',
        'received_at'  => 'datetime',
        'preparing_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    
    public function user()
    {
        
        return $this->belongsTo(User::class, 'user_id');
    }

    public function items()
    {
        return $this->hasMany(OrderDetail::class, 'order_id');
    }

    public function payment()
    {
        return $this->hasOne(Payment::class, 'order_id');
    }

    public function setStatusAttribute($value)
    {
        $this->attributes['status'] = $value;
        $now = now();

        if (!$this->received_at) {
            $this->received_at = $this->created_at ?? $now;
        }
        if ($value === self::PREPARING && !$this->preparing_at) {
            $this->preparing_at = $now;
        }
        if ($value === self::COMPLETED && !$this->completed_at) {
            $this->completed_at = $now;
        }
    }

   
    public function markReceived(): void  { $this->status = self::RECEIVED;  $this->save(); }
    public function markPreparing(): void { $this->status = self::PREPARING; $this->save(); }
    public function markCompleted(): void { $this->status = self::COMPLETED; $this->save(); }
}
