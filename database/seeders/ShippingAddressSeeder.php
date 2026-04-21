<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\ShippingAddress;

class ShippingAddressSeeder extends Seeder
{
    public function run(): void
    {
        $customers = User::where('role_id', 3)->get();

        $addresses = [
            [
                'full_name'    => 'Nguyen Van A',
                'phone_number' => '0901111111',
                'province'     => 'Ho Chi Minh',
                'district'     => 'Quan 1',
                'ward'         => 'Phuong Ben Nghe',
                'detail'       => '123 Nguyen Hue',
                'is_default'   => true,
            ],
            [
                'full_name'    => 'Tran Thi B',
                'phone_number' => '0902222222',
                'province'     => 'Ha Noi',
                'district'     => 'Quan Hoan Kiem',
                'ward'         => 'Phuong Hang Bac',
                'detail'       => '45 Hang Bac',
                'is_default'   => true,
            ],
            [
                'full_name'    => 'Le Van C',
                'phone_number' => '0903333333',
                'province'     => 'Da Nang',
                'district'     => 'Quan Hai Chau',
                'ward'         => 'Phuong Hai Chau 1',
                'detail'       => '78 Tran Phu',
                'is_default'   => true,
            ],
        ];

        foreach ($customers as $index => $customer) {
            if (isset($addresses[$index])) {
                ShippingAddress::firstOrCreate(
                    ['user_id' => $customer->user_id, 'is_default' => true],
                    array_merge($addresses[$index], ['user_id' => $customer->user_id])
                );
            }
        }
    }
}
