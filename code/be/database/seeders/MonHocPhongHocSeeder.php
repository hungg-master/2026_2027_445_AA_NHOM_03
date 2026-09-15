<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MonHoc;
use App\Models\PhongHoc;

class MonHocPhongHocSeeder extends Seeder
{
    public function run(): void
    {
        // Môn học mẫu
        $monHocs = [
            ['ten_mon_hoc' => 'Toán học 12', 'lop' => 'Lớp 12', 'mo_ta' => 'Ôn thi THPT Quốc Gia môn Toán'],
            ['ten_mon_hoc' => 'Vật lý 12', 'lop' => 'Lớp 12', 'mo_ta' => 'Ôn thi THPT môn Vật lý'],
            ['ten_mon_hoc' => 'Hóa học 12', 'lop' => 'Lớp 12', 'mo_ta' => 'Ôn thi THPT môn Hóa'],
            ['ten_mon_hoc' => 'Tiếng Anh IELTS 6.5+', 'lop' => 'Đại học', 'mo_ta' => 'Luyện thi IELTS đạt 6.5 trở lên'],
            ['ten_mon_hoc' => 'Lập trình Web Frontend', 'lop' => 'Đại học', 'mo_ta' => 'HTML, CSS, JavaScript, Vue.js, React'],
            ['ten_mon_hoc' => 'Lập trình Python', 'lop' => 'Đại học', 'mo_ta' => 'Python cơ bản đến nâng cao'],
            ['ten_mon_hoc' => 'Toán nâng cao', 'lop' => 'Lớp 10', 'mo_ta' => 'Bồi dưỡng HSG Toán 10'],
            ['ten_mon_hoc' => 'Ngữ văn 12', 'lop' => 'Lớp 12', 'mo_ta' => 'Luyện thi THPT môn Văn'],
        ];

        foreach ($monHocs as $mh) {
            MonHoc::firstOrCreate(['ten_mon_hoc' => $mh['ten_mon_hoc']], $mh);
        }

        // Phòng học mẫu
        $phongHocs = [
            ['so_phong' => 'P.302', 'dia_chi' => 'Cơ sở 1 - 254 Nguyễn Văn Linh, Đà Nẵng', 'mo_ta' => 'Phòng học 30 chỗ ngồi, có máy chiếu'],
            ['so_phong' => 'P.201', 'dia_chi' => 'Cơ sở 1 - 254 Nguyễn Văn Linh, Đà Nẵng', 'mo_ta' => 'Phòng học 20 chỗ ngồi'],
            ['so_phong' => 'P.405', 'dia_chi' => 'Cơ sở 2 - 41 Lê Duẩn, Đà Nẵng', 'mo_ta' => 'Phòng học 25 chỗ ngồi, có bảng thông minh'],
            ['so_phong' => 'Lab1', 'dia_chi' => 'Cơ sở 1 - 254 Nguyễn Văn Linh, Đà Nẵng', 'mo_ta' => 'Phòng thực hành máy tính 20 chỗ'],
        ];

        foreach ($phongHocs as $ph) {
            PhongHoc::firstOrCreate(['so_phong' => $ph['so_phong']], $ph);
        }

        $this->command->info('Đã seed ' . count($monHocs) . ' môn học và ' . count($phongHocs) . ' phòng học.');
    }
}
