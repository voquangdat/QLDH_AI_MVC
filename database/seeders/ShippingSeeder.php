<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\Shipping;

class ShippingSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'order_status'      => 'delivered',
                'shipping_status'   => 'delivered',
                'actual_delivery'   => '2026-04-13',
                'estimated_delivery'=> '2026-04-13',
            ],
            [
                'order_status'      => 'processing',
                'shipping_status'   => 'in_transit',
                'actual_delivery'   => null,
                'estimated_delivery'=> '2026-04-20',
            ],
        ];

        $orders = Order::whereIn('order_status', ['delivered', 'processing', 'shipped'])->get();

        foreach ($orders as $index => $order) {
            $info = $data[$index] ?? $data[0];
            Shipping::firstOrCreate(
                ['order_id' => $order->id],
                [
                    'shipping_provider'  => 'GHN',
                    'tracking_number'    => 'GHN' . str_pad($order->id, 8, '0', STR_PAD_LEFT),
                    'shipping_fee'       => 30000,
                    'estimated_delivery' => $info['estimated_delivery'],
                    'actual_delivery'    => $info['actual_delivery'],
                    'status'             => $info['shipping_status'],
                ]
            );
        }
    }
}
