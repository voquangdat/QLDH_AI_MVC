<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderStatusHistory extends Model
{
    protected $table = 'order_status_history';
    protected $primaryKey = 'status_history_id';
    protected $keyType = 'int';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'order_id', 'old_status', 'new_status', 'changed_by', 'reason', 'created_at'
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    // ===== Relationships =====
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function changedByUser()
    {
        return $this->belongsTo(User::class, 'changed_by', 'user_id');
    }
}