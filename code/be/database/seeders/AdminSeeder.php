<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Administrator credentials are created only by the interactive console command.
        $this->command?->info('Tạo quản trị viên bằng lệnh edulink:create-admin; seeder không thay đổi tài khoản.');
    }
}
