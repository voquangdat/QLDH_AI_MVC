<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $table = 'reviews';
    protected $primaryKey = 'review_id';
    protected $keyType = 'int';
    public $incrementing = true;

    protected $fillable = [
        'product_id', 'users_id', 'rating', 'title', 'comment', 'helpful_count'
    ];

    protected $casts = [
        'rating' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ===== Relationships =====
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id', 'user_id');
    }

    // ===== Scopes =====
    public function scopeHighestRated($query)
    {
        return $query->orderByDesc('rating');
    }

    public function scopeRecent($query)
    {
        return $query->latest('created_at');
    }
}