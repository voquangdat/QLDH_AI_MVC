<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Subcategory;

class SubcategorySeeder extends Seeder
{
    public function run(): void
    {
        $catClb = Category::where('category_name', 'AO CLB')->first();
        $catQg  = Category::where('category_name', 'AO DOI TUYEN QUOC GIA')->first();

        if ($catClb) {
            $clbSubs = [
                'Man United', 'Man City', 'Real Madrid',
                'Barcelona', 'PSG', 'Liverpool',
            ];
            foreach ($clbSubs as $name) {
                Subcategory::firstOrCreate(
                    ['category_id' => $catClb->category_id, 'subcategory_name' => $name]
                );
            }
        }

        if ($catQg) {
            $qgSubs = [
                'Viet Nam', 'Brazil', 'Argentina',
                'Phap', 'Anh', 'Duc',
            ];
            foreach ($qgSubs as $name) {
                Subcategory::firstOrCreate(
                    ['category_id' => $catQg->category_id, 'subcategory_name' => $name]
                );
            }
        }
    }
}
