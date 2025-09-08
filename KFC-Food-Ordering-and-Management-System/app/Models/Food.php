<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Food extends Model
{
    use HasFactory;

    protected $table = 'foods';

    
    protected $fillable = [
        'category_id',
        'name',
        'description',
        'price',
        'availability',
        'image_url',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'availability' => 'boolean',
    ];

    public function category() { return $this->belongsTo(Category::class); }
    public function reviews()  { return $this->hasMany(Review::class); }
}
