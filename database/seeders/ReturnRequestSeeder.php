<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\ReturnRequest;

class ReturnRequestSeeder extends Seeder
{
    public function run(): void
    {
        $order = Order::where('order_status', 'delivered')->first();
        if (!$order) return;

        $detail = OrderDetail::where('order_id', $order->id)->first();
        if (!$detail) return;

        ReturnRequest::firstOrCreate(
            ['order_id' => $order->id, 'order_detail_id' => $detail->detail_id],
            [
                'reason'        => 'San pham bi loi duong may.',
                'quantity'      => 1,
                'status'        => 'requested',
                'refund_amount' => $detail->product_gia,
            ]
        );
    }
}
