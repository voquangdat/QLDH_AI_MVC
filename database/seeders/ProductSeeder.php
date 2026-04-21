<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $catClb = Category::where('category_name', 'AO CLB')->first();
        $catQg  = Category::where('category_name', 'AO DOI TUYEN QUOC GIA')->first();

        $products = [];

        if ($catClb) {
            $subMU  = Subcategory::where('subcategory_name', 'Man United')->first();
            $subMC  = Subcategory::where('subcategory_name', 'Man City')->first();
            $subRM  = Subcategory::where('subcategory_name', 'Real Madrid')->first();
            $subBar = Subcategory::where('subcategory_name', 'Barcelona')->first();

            if ($subMU) {
                $products[] = [
                    'product_name'   => 'Ao dau Man United san nha 2024/25',
                    'description'    => 'Ao dau chinh hang Man United mua giai 2024/25, san nha.',
                    'care_note'      => 'Giat tay, khong say kho.',
                    'category_id'    => $catClb->category_id,
                    'subcategory_id' => $subMU->subcategory_id,
                    'product_gia'    => 850000,
                ];
                $products[] = [
                    'product_name'   => 'Ao dau Man United san khach 2024/25',
                    'description'    => 'Ao dau chinh hang Man United mua giai 2024/25, san khach.',
                    'care_note'      => 'Giat tay, khong say kho.',
                    'category_id'    => $catClb->category_id,
                    'subcategory_id' => $subMU->subcategory_id,
                    'product_gia'    => 850000,
                ];
            }
            if ($subMC) {
                $products[] = [
                    'product_name'   => 'Ao dau Man City san nha 2024/25',
                    'description'    => 'Ao dau chinh hang Man City mua giai 2024/25.',
                    'care_note'      => 'Giat tay, khong say kho.',
                    'category_id'    => $catClb->category_id,
                    'subcategory_id' => $subMC->subcategory_id,
                    'product_gia'    => 850000,
                ];
            }
            if ($subRM) {
                $products[] = [
                    'product_name'   => 'Ao dau Real Madrid san nha 2024/25',
                    'description'    => 'Ao dau chinh hang Real Madrid mua giai 2024/25.',
                    'care_note'      => 'Giat tay, khong say kho.',
                    'category_id'    => $catClb->category_id,
                    'subcategory_id' => $subRM->subcategory_id,
                    'product_gia'    => 900000,
                ];
            }
            if ($subBar) {
                $products[] = [
                    'product_name'   => 'Ao dau Barcelona san nha 2024/25',
                    'description'    => 'Ao dau chinh hang Barcelona mua giai 2024/25.',
                    'care_note'      => 'Giat tay, khong say kho.',
                    'category_id'    => $catClb->category_id,
                    'subcategory_id' => $subBar->subcategory_id,
                    'product_gia'    => 900000,
                ];
            }
        }

        if ($catQg) {
            $subVN  = Subcategory::where('subcategory_name', 'Viet Nam')->first();
            $subArg = Subcategory::where('subcategory_name', 'Argentina')->first();
            $subBra = Subcategory::where('subcategory_name', 'Brazil')->first();

            if ($subVN) {
                $products[] = [
                    'product_name'   => 'Ao dau doi tuyen Viet Nam san nha 2024',
                    'description'    => 'Ao dau chinh hang doi tuyen Viet Nam 2024.',
                    'care_note'      => 'Giat tay, khong say kho.',
                    'category_id'    => $catQg->category_id,
                    'subcategory_id' => $subVN->subcategory_id,
                    'product_gia'    => 650000,
                ];
            }
            if ($subArg) {
                $products[] = [
                    'product_name'   => 'Ao dau Argentina san nha 2024',
                    'description'    => 'Ao dau chinh hang Argentina 2024, phien ban Messi.',
                    'care_note'      => 'Giat tay, khong say kho.',
                    'category_id'    => $catQg->category_id,
                    'subcategory_id' => $subArg->subcategory_id,
                    'product_gia'    => 750000,
                ];
            }
            if ($subBra) {
                $products[] = [
                    'product_name'   => 'Ao dau Brazil san nha 2024',
                    'description'    => 'Ao dau chinh hang doi tuyen Brazil 2024.',
                    'care_note'      => 'Giat tay, khong say kho.',
                    'category_id'    => $catQg->category_id,
                    'subcategory_id' => $subBra->subcategory_id,
                    'product_gia'    => 750000,
                ];
            }
        }

        foreach ($products as $product) {
            Product::firstOrCreate(
                ['product_name' => $product['product_name']],
                $product
            );
        }
    }
}
