<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\User;
use App\Models\OrderStatusHistory;

class OrderStatusHistorySeeder extends Seeder
{
    public function run(): void
    {
        $orders = Order::all();
        $staff  = User::where('role_id', 2)->first();

        if ($orders->isEmpty()) return;

        $order1 = $orders->firstWhere('order_status', 'delivered');
        if ($order1) {
            $histories = [
                ['old_status' => null,          'new_status' => 'pending',    'created_at' => '2026-04-10 10:00:00'],
                ['old_status' => 'pending',     'new_status' => 'confirmed',  'created_at' => '2026-04-10 10:30:00'],
                ['old_status' => 'confirmed',   'new_status' => 'processing', 'created_at' => '2026-04-10 11:00:00'],
                ['old_status' => 'processing',  'new_status' => 'shipped',    'created_at' => '2026-04-11 08:00:00'],
                ['old_status' => 'shipped',     'new_status' => 'delivered',  'created_at' => '2026-04-13 15:00:00'],
            ];
            foreach ($histories as $h) {
                OrderStatusHistory::firstOrCreate(
                    ['order_id' => $order1->id, 'new_status' => $h['new_status']],
                    array_merge($h, ['order_id' => $order1->id, 'changed_by' => $staff?->user_id])
                );
            }
        }

        $order2 = $orders->firstWhere('order_status', 'processing');
        if ($order2) {
            OrderStatusHistory::firstOrCreate(
                ['order_id' => $order2->id, 'new_status' => 'pending'],
                ['order_id' => $order2->id, 'old_status' => null, 'new_status' => 'pending', 'changed_by' => null, 'created_at' => '2026-04-15 14:30:00']
            );
            OrderStatusHistory::firstOrCreate(
                ['order_id' => $order2->id, 'new_status' => 'confirmed'],
                ['order_id' => $order2->id, 'old_status' => 'pending', 'new_status' => 'confirmed', 'changed_by' => $staff?->user_id, 'created_at' => '2026-04-15 15:00:00']
            );
        }
    }
}
