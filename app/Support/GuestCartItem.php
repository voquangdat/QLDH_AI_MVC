<?php

namespace App\Support;

class GuestCartItem
{
    public int $variant_id;
    public $variant;
    public int $quantity;

    public function __construct(int $variantId, $variant, int $quantity)
    {
        $this->variant_id = $variantId;
        $this->variant    = $variant;
        $this->quantity   = $quantity;
    }

    public function subtotal(): float
    {
        return $this->quantity * $this->variant->product->product_gia;
    }
}
