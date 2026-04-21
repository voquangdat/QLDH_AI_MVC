<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\Invoice;

class InvoiceSeeder extends Seeder
{
    public function run(): void
    {
        $orders = Order::where('payment_status', 'paid')->get();

        foreach ($orders as $order) {
            Invoice::firstOrCreate(
                ['order_id' => $order->id],
                [
                    'invoice_number'  => 'INV-' . str_pad($order->id, 6, '0', STR_PAD_LEFT),
                    'subtotal'        => $order->subtotal,
                    'tax'             => $order->tax,
                    'shipping_fee'    => $order->shipping_fee,
                    'discount_amount' => $order->discount_amount,
                    'total_amount'    => $order->total_amount,
                    'issued_at'       => $order->order_date,
                ]
            );
        }
    }
}
