<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    // use HasApiTokens, Notifiable;
    use Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'user_id';
    protected $keyType = 'int';
    public $incrementing = true;

    protected $fillable = [
        'fullname', 'email', 'password', 'phone', 'avatar', 'role_id', 'is_active'
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'is_active' => 'boolean',
        'email_verified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ===== Relationships =====
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'role_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'user_id', 'user_id');
    }

    public function shippingAddresses()
    {
        return $this->hasMany(ShippingAddress::class, 'user_id', 'user_id');
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class, 'users_id', 'user_id');
    }

    public function cartItems()
    {
        return $this->hasMany(Cart::class, 'users_id', 'user_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'users_id', 'user_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'users_id', 'user_id');
    }

    // ===== Helpers =====
    public function isAdmin()
    {
        return $this->role_id == 1;
    }

    public function isCustomer()
    {
        return $this->role_id == 3;
    }

    public function defaultShippingAddress()
    {
        return $this->shippingAddresses()->where('is_default', true)->first();
    }
}