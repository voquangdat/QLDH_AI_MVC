<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Color extends Model
{
    protected $table = 'colors';
    protected $primaryKey = 'color_id';
    protected $keyType = 'int';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = ['color_ten', 'color_anh'];

    // ===== Relationships =====
    public function variants()
    {
        return $this->hasMany(Variant::class, 'color_id', 'color_id');
    }
}