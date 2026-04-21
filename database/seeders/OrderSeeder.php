<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Order;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $customers = User::where('role_id', 3)->get();

        if ($customers->isEmpty()) return;

        $orders = [
            [
                'user_id'         => $customers[0]->user_id,
                'order_number'    => 'ORD-2024-0001',
                'order_date'      => '2026-04-10 10:00:00',
                'order_status'    => 'delivered',
                'payment_status'  => 'paid',
                'subtotal'        => 850000,
                'shipping_fee'    => 30000,
                'discount_amount' => 0,
                'tax'             => 0,
                'total_amount'    => 880000,
                'notes'           => null,
            ],
            [
                'user_id'         => $customers[0]->user_id,
                'order_number'    => 'ORD-2024-0002',
                'order_date'      => '2026-04-15 14:30:00',
                'order_status'    => 'processing',
                'payment_status'  => 'paid',
                'subtotal'        => 1700000,
                'shipping_fee'    => 30000,
                'discount_amount' => 50000,
                'tax'             => 0,
                'total_amount'    => 1680000,
                'notes'           => 'Giao gio hanh chinh',
            ],
            [
                'user_id'         => $customers[1]->user_id ?? $customers[0]->user_id,
                'order_number'    => 'ORD-2024-0003',
                'order_date'      => '2026-04-18 09:00:00',
                'order_status'    => 'pending',
                'payment_status'  => 'unpaid',
                'subtotal'        => 900000,
                'shipping_fee'    => 30000,
                'discount_amount' => 0,
                'tax'             => 0,
                'total_amount'    => 930000,
                'notes'           => null,
            ],
        ];

        foreach ($orders as $order) {
            Order::firstOrCreate(['order_number' => $order['order_number']], $order);
        }
    }
}
