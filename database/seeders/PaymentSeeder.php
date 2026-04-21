<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\Payment;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        $orders = Order::where('payment_status', 'paid')->get();

        foreach ($orders as $order) {
            Payment::firstOrCreate(
                ['order_id' => $order->id],
                [
                    'payment_method' => 'momo',
                    'transaction_id' => 'TXN-' . strtoupper(substr(md5($order->order_number), 0, 10)),
                    'payment_date'   => $order->order_date,
                    'amount'         => $order->total_amount,
                    'payment_status' => 'completed',
                ]
            );
        }
    }
}
