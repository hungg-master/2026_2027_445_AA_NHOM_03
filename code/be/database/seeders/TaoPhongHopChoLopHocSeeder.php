<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LopHoc;
use App\Models\PhongHop;

class TaoPhongHopChoLopHocSeeder extends Seeder
{
    public function run()
    {
        $lops = LopHoc::all();
        $this->command->info('Tổng số lớp học: ' . $lops->count());

        foreach ($lops as $lop) {
            $phong = PhongHop::where('id_lop_hoc', $lop->id)->first();
            if (!$phong) {
                $maPhong = sprintf('%03d-%03d-%03d', mt_rand(100, 999), mt_rand(100, 999), mt_rand(100, 999));
                $phong = PhongHop::create([
                    'ma_phong' => $maPhong,
                    'ten_phong' => 'Phòng học: ' . $lop->ten_lop,
                    'id_chu_phong' => $lop->id_giao_vien,
                    'id_lop_hoc' => $lop->id,
                    'so_nguoi_toi_da' => $lop->so_luong_hoc_vien_toi_da ?: 100,
                    'mo_ta' => 'Phòng học trực tuyến EduLink cho lớp ' . $lop->ten_lop,
                    'thoi_gian_bat_dau' => $lop->thoi_gian_bat_dau ?: now(),
                    'trang_thai' => 1
                ]);
                $this->command->info("Tạo phòng {$maPhong} cho lớp #{$lop->id} ({$lop->ten_lop})");
            } else {
                $maPhong = $phong->ma_phong;
            }

            // Cập nhật link_online trỏ về phòng học EduLink
            $lop->link_online = '/phong-hoc/' . $maPhong;
            $lop->save();
        }

        $this->command->info('Đã hoàn tất khởi tạo phòng học EduLink cho toàn bộ các lớp!');
    }
}
