<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['category_name' => 'ÁO CLB'],
            ['category_name' => 'ÁO ĐỘI TUYỂN QUỐC GIA'],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(['category_name' => $cat['category_name']]);
        }
    }
}