<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    protected $table = 'product_images';
    protected $primaryKey = 'product_anh_id';
    protected $keyType = 'int';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = ['product_id', 'product_anh'];

    // ===== Relationships =====
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }
}