<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment('production')) {
            throw new \RuntimeException('Demo data may only be seeded in a development or test environment.');
        }
        $this->call([MonHocPhongHocSeeder::class, LopHocVaLichHocSeeder::class]);
        $this->call(TaoPhongHopChoLopHocSeeder::class);
    }
}
