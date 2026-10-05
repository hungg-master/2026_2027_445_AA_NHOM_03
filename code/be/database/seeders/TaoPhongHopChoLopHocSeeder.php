<?php

namespace Database\Seeders;

use App\Models\LopHoc;
use App\Services\ClassRoomService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaoPhongHopChoLopHocSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            foreach (LopHoc::where('hinh_thuc', 'online')->get() as $class) {
                app(ClassRoomService::class)->sync($class);
            }
        }, 5);
    }
}
