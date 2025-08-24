<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Food extends Model
{
    use HasFactory;
    protected $table = 'foods';
    protected $fillable = ['category_id','name','description','price','availability','image_url'];

    public function category()    { return $this->belongsTo(Category::class, 'category_id'); }
    public function reviews()     { return $this->hasMany(Review::class, 'food_id'); }
    public function orderDetails(){ return $this->hasMany(OrderDetail::class, 'food_id'); }
}
