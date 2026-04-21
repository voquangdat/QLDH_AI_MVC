<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Order;
use App\Models\Notification;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $customers = User::where('role_id', 3)->get();
        $orders    = Order::all();

        if ($customers->isEmpty()) return;

        $customer = $customers->first();
        $order    = $orders->first();

        $notifications = [
            [
                'users_id'        => $customer->user_id,
                'title'           => 'Dat hang thanh cong',
                'message'         => 'Don hang ORD-2024-0001 da duoc xac nhan.',
                'type'            => 'order',
                'related_order_id'=> $order?->id,
                'is_read'         => true,
            ],
            [
                'users_id'        => $customer->user_id,
                'title'           => 'Don hang da duoc giao',
                'message'         => 'Don hang ORD-2024-0001 da duoc giao thanh cong.',
                'type'            => 'delivery',
                'related_order_id'=> $order?->id,
                'is_read'         => false,
            ],
        ];

        foreach ($notifications as $notif) {
            Notification::firstOrCreate(
                ['users_id' => $notif['users_id'], 'title' => $notif['title']],
                $notif
            );
        }
    }
}
