<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\Product;
use App\Models\Variant;
use App\Models\OrderDetail;

class OrderDetailSeeder extends Seeder
{
    public function run(): void
    {
        $orders   = Order::all();
        $products = Product::all();
        $variants = Variant::all();

        if ($orders->isEmpty() || $products->isEmpty() || $variants->isEmpty()) return;

        $product1  = $products->first();
        $variant1  = $variants->where('product_id', $product1->product_id)->first();

        $product2  = $products->skip(1)->first() ?? $product1;
        $variant2  = $variants->where('product_id', $product2->product_id)->first() ?? $variant1;

        $details = [
            [
                'order_id'     => $orders[0]->id,
                'product_id'   => $product1->product_id,
                'variant_id'   => $variant1->variant_id,
                'quantity'     => 1,
                'product_name' => $product1->product_name,
                'product_gia'  => $product1->product_gia,
                'product_anh'  => null,
                'subtotal'     => $product1->product_gia,
            ],
            [
                'order_id'     => $orders[1]->id,
                'product_id'   => $product1->product_id,
                'variant_id'   => $variant1->variant_id,
                'quantity'     => 2,
                'product_name' => $product1->product_name,
                'product_gia'  => $product1->product_gia,
                'product_anh'  => null,
                'subtotal'     => $product1->product_gia * 2,
            ],
            [
                'order_id'     => $orders[1]->id,
                'product_id'   => $product2->product_id,
                'variant_id'   => $variant2->variant_id,
                'quantity'     => 1,
                'product_name' => $product2->product_name,
                'product_gia'  => $product2->product_gia,
                'product_anh'  => null,
                'subtotal'     => $product2->product_gia,
            ],
            [
                'order_id'     => $orders[2]->id,
                'product_id'   => $product2->product_id,
                'variant_id'   => $variant2->variant_id,
                'quantity'     => 1,
                'product_name' => $product2->product_name,
                'product_gia'  => $product2->product_gia,
                'product_anh'  => null,
                'subtotal'     => $product2->product_gia,
            ],
        ];

        foreach ($details as $detail) {
            OrderDetail::firstOrCreate(
                ['order_id' => $detail['order_id'], 'variant_id' => $detail['variant_id']],
                $detail
            );
        }
    }
}
