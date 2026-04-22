<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'fullname'  => 'Admin',
                'email'     => 'admin@voxfootball.com',
                'password'  => Hash::make('password'),
                'phone'     => '0900000001',
                'role_id'   => 1,
                'is_active' => true,
            ],
            [
                'fullname'  => 'Vo Quang Dat',
                'email'     => 'voquangdat03072004@gmail.com',
                'password'  => Hash::make('Dat227889#'),
                'phone'     => '0903527632',
                'role_id'   => 1,
                'is_active' => true,
            ],
            [
                'fullname'  => 'Staff',
                'email'     => 'staff@voxfootball.com',
                'password'  => Hash::make('password'),
                'phone'     => '0900000002',
                'role_id'   => 2,
                'is_active' => true,
            ],
            [
                'fullname'  => 'Nguyen Van A',
                'email'     => 'customer1@gmail.com',
                'password'  => Hash::make('password'),
                'phone'     => '0901111111',
                'role_id'   => 3,
                'is_active' => true,
            ],
            [
                'fullname'  => 'Tran Thi B',
                'email'     => 'customer2@gmail.com',
                'password'  => Hash::make('password'),
                'phone'     => '0902222222',
                'role_id'   => 3,
                'is_active' => true,
            ],
            [
                'fullname'  => 'Le Van C',
                'email'     => 'customer3@gmail.com',
                'password'  => Hash::make('password'),
                'phone'     => '0903333333',
                'role_id'   => 3,
                'is_active' => true,
            ],
        ];

        foreach ($users as $user) {
            User::firstOrCreate(['email' => $user['email']], $user);
        }
    }
}
