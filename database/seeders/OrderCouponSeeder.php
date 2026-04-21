<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\Coupon;
use App\Models\OrderCoupon;

class OrderCouponSeeder extends Seeder
{
    public function run(): void
    {
        $order  = Order::where('discount_amount', '>', 0)->first();
        $coupon = Coupon::where('code', 'SALE50K')->first();

        if ($order && $coupon) {
            OrderCoupon::firstOrCreate(
                ['order_id' => $order->id, 'coupon_id' => $coupon->coupon_id],
                ['discount_amount' => $order->discount_amount]
            );
        }
    }
}
