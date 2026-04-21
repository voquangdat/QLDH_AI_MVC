<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $table = 'inventory';
    protected $primaryKey = 'inventory_id';
    protected $keyType = 'int';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'variant_id', 'soluong_ton', 'soluong_dat',
        'soluong_co_the_ban', 'muc_canh_bao'
    ];

    // ===== Relationships =====
    public function variant()
    {
        return $this->belongsTo(Variant::class, 'variant_id', 'variant_id');
    }

    // ===== Helpers =====
    public function isOutOfStock()
    {
        return $this->soluong_co_the_ban <= 0;
    }

    public function isLowStock()
    {
        return $this->soluong_co_the_ban <= $this->muc_canh_bao;
    }

    public function availableQuantity()
    {
        return max(0, $this->soluong_co_the_ban);
    }
}