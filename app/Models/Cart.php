<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $table = 'cart';
    protected $primaryKey = 'cart_id';
    protected $keyType = 'int';
    public $incrementing = true;

    protected $fillable = ['users_id', 'variant_id', 'quantity'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ===== Relationships =====
    public function user()
    {
        return $this->belongsTo(User::class, 'users_id', 'user_id');
    }

    public function variant()
    {
        return $this->belongsTo(Variant::class, 'variant_id', 'variant_id');
    }

    // ===== Helpers =====
    public function product()
    {
        return $this->variant?->product;
    }

    public function subtotal()
    {
        if ($this->variant && $this->variant->product) {
            return $this->quantity * $this->variant->product->product_gia;
        }
        return 0;
    }
}