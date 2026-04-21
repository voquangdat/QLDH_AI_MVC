<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Product;
use App\Models\Variant;
use App\Models\Wishlist;

class WishlistSeeder extends Seeder
{
    public function run(): void
    {
        $customers = User::where('role_id', 3)->get();
        $products  = Product::all();
        $variants  = Variant::all();

        if ($customers->isEmpty() || $products->isEmpty()) return;

        $customer  = $customers->first();
        $product1  = $products->first();
        $product2  = $products->skip(1)->first() ?? $product1;
        $variant1  = $variants->where('product_id', $product1->product_id)->first();

        Wishlist::firstOrCreate(
            ['users_id' => $customer->user_id, 'product_id' => $product1->product_id],
            ['variant_id' => $variant1?->variant_id]
        );

        Wishlist::firstOrCreate(
            ['users_id' => $customer->user_id, 'product_id' => $product2->product_id],
            ['variant_id' => null]
        );
    }
}
