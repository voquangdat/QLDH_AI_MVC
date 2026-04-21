<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductSize extends Model
{
    protected $table = 'product_size';
    protected $primaryKey = 'product_size_id';
    public $timestamps = false;

    protected $fillable = ['product_size'];

    public function variants()
    {
        return $this->hasMany(Variant::class, 'product_size_id', 'product_size_id');
    }
}
