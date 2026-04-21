<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingAddress extends Model
{
    protected $table = 'shipping_addresses';
    protected $primaryKey = 'id';
    protected $keyType = 'int';
    public $incrementing = true;

    protected $fillable = [
        'user_id', 'full_name', 'phone_number',
        'province', 'district', 'ward', 'detail', 'is_default'
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ===== Relationships =====
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    // ===== Helpers =====
    public function fullAddress()
    {
        return "{$this->province}, {$this->district}, {$this->ward}, {$this->detail}";
    }

    public function setAsDefault()
    {
        $this->user->shippingAddresses()->update(['is_default' => false]);
        $this->update(['is_default' => true]);
    }
}