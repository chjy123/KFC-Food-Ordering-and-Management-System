<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
#author’s name： Lim Jun Hong
use Laravel\Sanctum\HasApiTokens; // ✅ Added for token support

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable; // ✅ Added HasApiTokens

    /**
     * Mass assignable attributes.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phoneNo',   // optional phone field
        'role',      // 'admin' | 'customer'
    ];

    /**
     * Hidden attributes.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Attribute casting (Laravel 10/11 style).
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Role helpers.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    /**
     * Query scopes for convenience: User::admins()->get(), User::customers()->get()
     */
    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    public function scopeCustomers($query)
    {
        return $query->where('role', 'customer');
    }

    /**
     * Relationships.
     */
    public function orders()
    {
        return $this->hasMany(Order::class, 'user_id');
    }

    public function reviews()
    {
        return $this->hasMany(\App\Models\Review::class);
    }
}
