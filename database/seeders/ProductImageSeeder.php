<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductImage;

class ProductImageSeeder extends Seeder
{
    public function run(): void
    {
        $products = Product::all();

        foreach ($products as $product) {
            $slug = strtolower(str_replace(' ', '-', $product->product_name));
            ProductImage::firstOrCreate(
                ['product_id' => $product->product_id, 'product_anh' => "images/products/{$slug}-1.jpg"]
            );
            ProductImage::firstOrCreate(
                ['product_id' => $product->product_id, 'product_anh' => "images/products/{$slug}-2.jpg"]
            );
        }
    }
}
