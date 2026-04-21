<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingTracking extends Model
{
    protected $table = 'shipping_tracking';
    protected $primaryKey = 'tracking_id';
    protected $keyType = 'int';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = ['shipping_id', 'location', 'status', 'timestamp'];

    protected $casts = [
        'timestamp' => 'datetime',
    ];

    // ===== Relationships =====
    public function shipping()
    {
        return $this->belongsTo(Shipping::class, 'shipping_id', 'shipping_id');
    }
}