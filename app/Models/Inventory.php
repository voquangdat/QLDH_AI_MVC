<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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

    // ===== Query Scopes =====

    public function scopeInStock($query)
    {
        return $query->whereColumn('soluong_co_the_ban', '>', 'muc_canh_bao');
    }

    public function scopeLowStock($query)
    {
        return $query->where('soluong_co_the_ban', '>', 0)
                     ->whereColumn('soluong_co_the_ban', '<=', 'muc_canh_bao');
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('soluong_co_the_ban', '<=', 0);
    }

    // ===== Helpers =====

    public function isOutOfStock(): bool
    {
        return $this->soluong_co_the_ban <= 0;
    }

    public function isLowStock(): bool
    {
        return $this->soluong_co_the_ban > 0
            && $this->soluong_co_the_ban <= $this->muc_canh_bao;
    }

    public function availableQuantity(): int
    {
        return max(0, $this->soluong_co_the_ban);
    }

    public function recalculate(): void
    {
        $this->soluong_co_the_ban = max(0, $this->soluong_ton - $this->soluong_dat);
    }

    /**
     * Đồng bộ soluong_co_the_ban = GREATEST(soluong_ton - soluong_dat, 0) thẳng DB.
     * Gọi sau mỗi lần thay đổi soluong_ton hoặc soluong_dat qua raw query.
     */
    public static function syncAvailable(int $variantId): void
    {
        static::where('variant_id', $variantId)
            ->update([
                'soluong_co_the_ban' => DB::raw('GREATEST(soluong_ton - soluong_dat, 0)'),
            ]);
    }
}