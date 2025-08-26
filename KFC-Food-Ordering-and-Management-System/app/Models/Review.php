<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model {
    use HasFactory;
    protected $fillable = ['user_id','food_id','rating','comment','review_date'];
    public function food() { return $this->belongsTo(Food::class); }
    public function user() { return $this->belongsTo(User::class); }
}
