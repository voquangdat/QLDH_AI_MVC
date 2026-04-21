<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Shipping;
use App\Models\ShippingTracking;

class ShippingTrackingSeeder extends Seeder
{
    public function run(): void
    {
        $shippings = Shipping::all();

        foreach ($shippings as $shipping) {
            $trackings = [
                [
                    'location'  => 'Kho Ha Noi',
                    'status'    => 'Lay hang thanh cong',
                    'timestamp' => now()->subDays(3)->toDateTimeString(),
                ],
                [
                    'location'  => 'Trung tam phan loai mien Nam',
                    'status'    => 'Dang van chuyen',
                    'timestamp' => now()->subDays(2)->toDateTimeString(),
                ],
                [
                    'location'  => 'Kho TP.HCM',
                    'status'    => 'Da den kho dich',
                    'timestamp' => now()->subDays(1)->toDateTimeString(),
                ],
            ];

            if ($shipping->status === 'delivered') {
                $trackings[] = [
                    'location'  => 'Dia chi nguoi nhan',
                    'status'    => 'Giao hang thanh cong',
                    'timestamp' => now()->toDateTimeString(),
                ];
            }

            foreach ($trackings as $t) {
                ShippingTracking::firstOrCreate(
                    ['shipping_id' => $shipping->shipping_id, 'status' => $t['status']],
                    $t
                );
            }
        }
    }
}
