<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Variant extends Model
{
    protected $table = 'variant';
    protected $primaryKey = 'variant_id';
    protected $keyType = 'int';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'product_id', 'color_id', 'product_size_id', 'quantity', 'variant_code'
    ];

    // ===== Relationships =====
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    public function color()
    {
        return $this->belongsTo(Color::class, 'color_id', 'color_id');
    }

    public function size()
    {
        return $this->belongsTo(Size::class, 'product_size_id', 'product_size_id');
    }

    public function inventory()
    {
        return $this->hasOne(Inventory::class, 'variant_id', 'variant_id');
    }

    public function carts()
    {
        return $this->hasMany(Cart::class, 'variant_id', 'variant_id');
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class, 'variant_id', 'variant_id');
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class, 'variant_id', 'variant_id');
    }

    // ===== Helpers =====
    public function displayName()
    {
        return $this->product->product_name . ' - ' .
               $this->color->color_ten . ' - ' .
               $this->size->product_size;
    }

    public function isOutOfStock()
    {
        return $this->inventory && $this->inventory->soluong_co_the_ban <= 0;
    }
}