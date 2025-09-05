<?php
// Author's Name: Pang Jun Meng
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $primaryKey = 'payment_id';
    protected $fillable = [
        'user_id','order_id',
        'payment_method','payment_status','payment_date','amount',
        'transaction_ref','card_brand','card_last4','idempotency_key'
    ];

    public function user()  { return $this->belongsTo(User::class); }
    public function order() { return $this->belongsTo(Order::class); }

    // aligns with your report’s method naming
    public function viewPaymentDetails(): void { /* view handled by controller */ }
}
