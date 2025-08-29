<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    // Point to the right column names
    protected $fillable = ['category_name', 'description'];

    // Optional: add an accessor so you can keep using $category->name in Blade
    public function getNameAttribute()
    {
        return $this->category_name;
    }

    public function foods()
    {
        return $this->hasMany(Food::class);
    }
}
