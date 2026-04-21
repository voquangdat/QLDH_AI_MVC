<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Size extends Model
{
    protected $table = 'product_size';
    protected $primaryKey = 'product_size_id';
    protected $keyType = 'int';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = ['product_size'];

    // ===== Relationships =====
    public function variants()
    {
        return $this->hasMany(Variant::class, 'product_size_id', 'product_size_id');
    }
}