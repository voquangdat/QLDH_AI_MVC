<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = 'notifications';
    protected $primaryKey = 'notification_id';
    protected $keyType = 'int';
    public $incrementing = true;

    protected $fillable = [
        'users_id', 'title', 'message', 'type', 'related_order_id', 'is_read'
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ===== Relationships =====
    public function user()
    {
        return $this->belongsTo(User::class, 'users_id', 'user_id');
    }

    public function relatedOrder()
    {
        return $this->belongsTo(Order::class, 'related_order_id', 'id');
    }

    // ===== Scopes =====
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    // ===== Helpers =====
    public function markAsRead()
    {
        $this->update(['is_read' => true]);
    }

    public function markAsUnread()
    {
        $this->update(['is_read' => false]);
    }
}