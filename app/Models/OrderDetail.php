<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    protected $table = 'order_details';
    protected $primaryKey = 'detail_id';
    protected $keyType = 'int';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'order_id', 'product_id', 'variant_id', 'quantity',
        'product_name', 'product_gia', 'product_anh', 'subtotal'
    ];

    protected $casts = [
        'product_gia' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    // ===== Relationships =====
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    public function variant()
    {
        return $this->belongsTo(Variant::class, 'variant_id', 'variant_id');
    }

    public function returnRequests()
    {
        return $this->hasMany(ReturnRequest::class, 'order_detail_id', 'detail_id');
    }
}