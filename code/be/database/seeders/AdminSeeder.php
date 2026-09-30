<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('admins')->updateOrInsert(
            ['email' => 'admin@edulink.vn'],
            [
                'mat_khau' => Hash::make('admin123'),
                'ho_ten' => 'Quản Trị Viên EduLink',
                'so_dien_thoai' => '0901234567',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $this->command->info('Đã tạo tài khoản Admin mặc định: admin@edulink.vn / admin123');
    }
}
