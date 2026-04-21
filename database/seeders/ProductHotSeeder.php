<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductHotSeeder extends Seeder
{
    public function run(): void
    {
        Product::whereIn('product_id', [1, 2])->update(['product_hot' => true]);
    }
}
