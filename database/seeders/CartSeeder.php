<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Variant;
use App\Models\Cart;

class CartSeeder extends Seeder
{
    public function run(): void
    {
        $customers = User::where('role_id', 3)->get();
        $variants  = Variant::all();

        if ($customers->isEmpty() || $variants->isEmpty()) return;

        $customer = $customers->last();
        $variant1 = $variants->first();
        $variant2 = $variants->skip(1)->first() ?? $variant1;

        Cart::firstOrCreate(
            ['users_id' => $customer->user_id, 'variant_id' => $variant1->variant_id],
            ['quantity' => 1]
        );

        Cart::firstOrCreate(
            ['users_id' => $customer->user_id, 'variant_id' => $variant2->variant_id],
            ['quantity' => 2]
        );
    }
}
