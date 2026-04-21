<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Coupon;

class CouponSeeder extends Seeder
{
    public function run(): void
    {
        $coupons = [
            [
                'code'           => 'WELCOME10',
                'discount_type'  => 'percentage',
                'discount_value' => 10,
                'min_purchase'   => 200000,
                'max_uses'       => 100,
                'current_uses'   => 0,
                'start_date'     => '2026-01-01 00:00:00',
                'end_date'       => '2026-12-31 23:59:59',
                'is_active'      => true,
            ],
            [
                'code'           => 'SALE50K',
                'discount_type'  => 'fixed',
                'discount_value' => 50000,
                'min_purchase'   => 300000,
                'max_uses'       => 50,
                'current_uses'   => 5,
                'start_date'     => '2026-04-01 00:00:00',
                'end_date'       => '2026-06-30 23:59:59',
                'is_active'      => true,
            ],
            [
                'code'           => 'VIP20',
                'discount_type'  => 'percentage',
                'discount_value' => 20,
                'min_purchase'   => 500000,
                'max_uses'       => 30,
                'current_uses'   => 2,
                'start_date'     => '2026-04-01 00:00:00',
                'end_date'       => '2026-05-31 23:59:59',
                'is_active'      => true,
            ],
        ];

        foreach ($coupons as $coupon) {
            Coupon::firstOrCreate(['code' => $coupon['code']], $coupon);
        }
    }
}
