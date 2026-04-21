<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $table = 'coupon';
    protected $primaryKey = 'coupon_id';
    protected $keyType = 'int';
    public $incrementing = true;

    protected $fillable = [
        'code', 'discount_type', 'discount_value', 'min_purchase',
        'max_uses', 'current_uses', 'start_date', 'end_date', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ===== Relationships =====
    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_coupon', 'coupon_id', 'order_id');
    }

    // ===== Scopes =====
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeValid($query)
    {
        return $query->where('start_date', '<=', now())
                     ->where('end_date', '>=', now());
    }
}