<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Color;
use App\Models\ProductSize;
use App\Models\Variant;

class VariantSeeder extends Seeder
{
    public function run(): void
    {
        $products = Product::all();
        $colors   = Color::whereIn('color_ten', ['Den', 'Trang', 'Do'])->get();
        $sizes    = ProductSize::whereIn('product_size', ['S', 'M', 'L', 'XL'])->get();

        foreach ($products as $product) {
            foreach ($colors as $color) {
                foreach ($sizes as $size) {
                    $code = strtoupper("P{$product->product_id}-C{$color->color_id}-S{$size->product_size_id}");
                    Variant::firstOrCreate(
                        [
                            'product_id'      => $product->product_id,
                            'color_id'        => $color->color_id,
                            'product_size_id' => $size->product_size_id,
                        ],
                        ['quantity' => 20, 'variant_code' => $code]
                    );
                }
            }
        }
    }
}
