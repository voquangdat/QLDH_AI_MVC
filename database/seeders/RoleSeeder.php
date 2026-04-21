<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'role_name' => 'admin',
                'description' => 'Quản trị viên hệ thống, có toàn quyền truy cập.',
            ],
            [
                'role_name' => 'staff',
                'description' => 'Nhân viên, có quyền quản lý đơn hàng và sản phẩm.',
            ],
            [
                'role_name' => 'customer',
                'description' => 'Khách hàng, có quyền mua hàng và xem lịch sử đơn hàng.',
            ],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['role_name' => $role['role_name']], $role);
        }
    }
}
