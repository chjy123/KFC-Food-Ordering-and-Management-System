<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    
    protected $fillable = ['category_name', 'description'];

    
    public function getNameAttribute()
    {
        return $this->category_name;
    }

    public function foods()
    {
        return $this->hasMany(Food::class);
    }
}
