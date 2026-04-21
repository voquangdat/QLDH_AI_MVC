<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Product;
use App\Models\Review;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $customers = User::where('role_id', 3)->get();
        $products  = Product::all();

        if ($customers->isEmpty() || $products->isEmpty()) return;

        $reviews = [
            [
                'product_id'    => $products[0]->product_id,
                'users_id'      => $customers[0]->user_id,
                'rating'        => 5,
                'title'         => 'San pham chat luong',
                'comment'       => 'Ao dep, vai day, dung chuan size. Rat hai long!',
                'helpful_count' => 3,
            ],
            [
                'product_id'    => $products[0]->product_id,
                'users_id'      => $customers[1]->user_id ?? $customers[0]->user_id,
                'rating'        => 4,
                'title'         => 'Oke lam',
                'comment'       => 'San pham tot, giao hang nhanh. Mau sac dep.',
                'helpful_count' => 1,
            ],
        ];

        if ($products->count() > 1) {
            $reviews[] = [
                'product_id'    => $products[1]->product_id,
                'users_id'      => $customers[0]->user_id,
                'rating'        => 5,
                'title'         => 'Rat hai long',
                'comment'       => 'Chinh hang, giao nhanh, dong goi can than.',
                'helpful_count' => 2,
            ];
        }

        foreach ($reviews as $review) {
            Review::firstOrCreate(
                ['product_id' => $review['product_id'], 'users_id' => $review['users_id']],
                $review
            );
        }
    }
}
