<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturnRequest extends Model
{
    protected $table = 'return_request';
    protected $primaryKey = 'return_id';
    protected $keyType = 'int';
    public $incrementing = true;

    protected $fillable = [
        'order_id', 'order_detail_id', 'reason', 'quantity', 'status', 'refund_amount'
    ];

    protected $casts = [
        'refund_amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ===== Relationships =====
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function orderDetail()
    {
        return $this->belongsTo(OrderDetail::class, 'order_detail_id', 'detail_id');
    }

    // ===== Scopes =====
    public function scopePending($query)
    {
        return $query->where('status', 'requested');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRefunded($query)
    {
        return $query->where('status', 'refunded');
    }
}