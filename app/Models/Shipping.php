<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shipping extends Model
{
    protected $table = 'shipping';
    protected $primaryKey = 'shipping_id';
    protected $keyType = 'int';
    public $incrementing = true;

    protected $fillable = [
        'order_id', 'shipping_provider', 'tracking_number',
        'shipping_fee', 'estimated_delivery', 'actual_delivery', 'status'
    ];

    protected $casts = [
        'shipping_fee' => 'decimal:2',
        'estimated_delivery' => 'date',
        'actual_delivery' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ===== Relationships =====
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function trackings()
    {
        return $this->hasMany(ShippingTracking::class, 'shipping_id', 'shipping_id');
    }

    // ===== Helpers =====
    public function latestTracking()
    {
        return $this->trackings()->latest('timestamp')->first();
    }
}