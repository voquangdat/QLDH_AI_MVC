<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Variant;
use App\Models\Inventory;

class InventorySeeder extends Seeder
{
    public function run(): void
    {
        $variants = Variant::all();

        foreach ($variants as $variant) {
            Inventory::firstOrCreate(
                ['variant_id' => $variant->variant_id],
                [
                    'soluong_ton'        => 20,
                    'soluong_dat'        => 0,
                    'soluong_co_the_ban' => 20,
                    'muc_canh_bao'       => 5,
                ]
            );
        }
    }
}
