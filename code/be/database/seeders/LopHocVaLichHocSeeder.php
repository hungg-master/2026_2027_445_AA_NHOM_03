<?php

namespace Database\Seeders;

use App\Models\DangKyLop;
use App\Models\GiaoVien;
use App\Models\HocVien;
use App\Models\LopHoc;
use App\Models\MonHoc;
use App\Models\PhongHoc;
use App\Models\ThoiGianRanh;
use App\Services\LessonScheduleService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class LopHocVaLichHocSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment('production')) {
            throw new \RuntimeException('Dữ liệu mẫu chỉ dành cho môi trường phát triển.');
        }
        $this->command->info('=== BẮT ĐẦU SEED DỮ LIỆU LỚP HỌC & LỊCH HỌC ===');

        // -------------------------------------------------------------
        // 1. SEED MÔN HỌC
        // -------------------------------------------------------------
        $monHocsData = [
            ['ten_mon_hoc' => 'Toán học 12 (Luyện thi THPT)', 'lop' => 'Lớp 12', 'mo_ta' => 'Chuyên đề Hàm số, Tích phân và Hình học Oxyz luyện thi Đại học'],
            ['ten_mon_hoc' => 'Tiếng Anh IELTS Master', 'lop' => 'Đại học', 'mo_ta' => 'Luyện 4 kỹ năng Nghe - Nói - Đọc - Viết cam kết đầu ra Band 6.5 - 7.5+'],
            ['ten_mon_hoc' => 'Vật lý 12 Nâng cao', 'lop' => 'Lớp 12', 'mo_ta' => 'Dao động cơ, Sóng cơ và Dòng điện xoay chiều nâng cao'],
            ['ten_mon_hoc' => 'Hóa học 12 Chuyên sâu', 'lop' => 'Lớp 12', 'mo_ta' => 'Chuyên đề Este - Lipit, Peptit và Hóa học vô cơ'],
            ['ten_mon_hoc' => 'Lập trình Web Frontend (Vue.js)', 'lop' => 'Đại học', 'mo_ta' => 'Xây dựng ứng dụng Web hiện đại với HTML5, CSS3, JavaScript ES6 và Vue 3'],
            ['ten_mon_hoc' => 'Lập trình Python & AI Cơ bản', 'lop' => 'Đại học', 'mo_ta' => 'Nhập môn lập trình Python, xử lý dữ liệu và thuật toán trí tuệ nhân tạo'],
            ['ten_mon_hoc' => 'Ngữ văn 12 - Luyện đề chuẩn', 'lop' => 'Lớp 12', 'mo_ta' => 'Kỹ năng làm bài Nghị luận văn học và Nghị luận xã hội đạt 8+'],
            ['ten_mon_hoc' => 'Toán tư duy & Thống kê', 'lop' => 'Lớp 10', 'mo_ta' => 'Phát triển tư duy logic và kỹ năng giải toán ứng dụng thực tế'],
        ];

        $monHocMap = [];
        foreach ($monHocsData as $mh) {
            $record = MonHoc::firstOrCreate(
                ['ten_mon_hoc' => $mh['ten_mon_hoc']],
                [
                    'lop' => $mh['lop'],
                    'mo_ta' => $mh['mo_ta'],
                    'tinh_trang' => 'active',
                ]
            );
            $monHocMap[$mh['ten_mon_hoc']] = $record->id;
        }
        $this->command->info('-> Đã kiểm tra/khởi tạo '.count($monHocMap).' môn học.');

        // -------------------------------------------------------------
        // 2. SEED PHÒNG HỌC
        // -------------------------------------------------------------
        $phongHocsData = [
            ['so_phong' => 'P.301', 'dia_chi' => 'Cơ sở 1 - 254 Nguyễn Văn Linh, Đà Nẵng', 'mo_ta' => 'Phòng học lý thuyết 30 chỗ, trang bị máy chiếu và điều hòa hai chiều'],
            ['so_phong' => 'P.202', 'dia_chi' => 'Cơ sở 1 - 254 Nguyễn Văn Linh, Đà Nẵng', 'mo_ta' => 'Phòng học nhóm tiêu chuẩn 20 chỗ, có bảng kính tương tác'],
            ['so_phong' => 'P.405', 'dia_chi' => 'Cơ sở 2 - 41 Lê Duẩn, Đà Nẵng', 'mo_ta' => 'Phòng hội thảo 25 chỗ, trang bị màn hình thông minh Smart Board'],
            ['so_phong' => 'Lab-01', 'dia_chi' => 'Cơ sở 1 - 254 Nguyễn Văn Linh, Đà Nẵng', 'mo_ta' => 'Phòng thực hành máy tính 25 máy cấu hình cao Core i7, 16GB RAM'],
            ['so_phong' => 'Lab-02', 'dia_chi' => 'Cơ sở 2 - 41 Lê Duẩn, Đà Nẵng', 'mo_ta' => 'Phòng Lab thực hành mạng và lập trình chuyên sâu'],
        ];

        $phongHocMap = [];
        foreach ($phongHocsData as $ph) {
            $record = PhongHoc::firstOrCreate(
                ['so_phong' => $ph['so_phong']],
                [
                    'dia_chi' => $ph['dia_chi'],
                    'mo_ta' => $ph['mo_ta'],
                ]
            );
            $phongHocMap[$ph['so_phong']] = $record->id;
        }
        $this->command->info('-> Đã kiểm tra/khởi tạo '.count($phongHocMap).' phòng học.');

        // -------------------------------------------------------------
        // 3. SEED GIÁO VIÊN
        // -------------------------------------------------------------
        $giaoViensData = [
            [
                'ho_ten' => 'Cô Sarah Jenkins',
                'email' => 'sarah.jenkins@edulink.edu.vn',
                'password' => Hash::make('123456'),
                'so_dien_thoai' => '0905111222',
                'chuc_danh' => 'Thạc sĩ Toán học - ĐH Quốc Gia',
                'so_nam_kinh_nghiem' => 7,
                'mo_ta' => 'Hơn 7 năm kinh nghiệm luyện thi THPT Quốc Gia, giúp hơn 500 học viên đạt điểm 9+ môn Toán và Vật lý.',
                'hinh_anh' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=240&q=80',
                'trang_thai_duyet' => 'da_duyet',
                'is_active' => 1,
                'tinh_trang' => 1,
            ],
            [
                'ho_ten' => 'Thầy David Chen',
                'email' => 'david.chen@edulink.edu.vn',
                'password' => Hash::make('123456'),
                'so_dien_thoai' => '0905333444',
                'chuc_danh' => 'Kỹ sư phần mềm & Chuyên gia AI',
                'so_nam_kinh_nghiem' => 8,
                'mo_ta' => 'Chuyên gia đào tạo Fullstack Web (Vue/React/Laravel), Python Data Science và luyện thi SAT Math.',
                'hinh_anh' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=240&q=80',
                'trang_thai_duyet' => 'da_duyet',
                'is_active' => 1,
                'tinh_trang' => 1,
            ],
            [
                'ho_ten' => 'Thầy Nguyễn Minh Triết',
                'email' => 'minhtriet.nguyen@edulink.edu.vn',
                'password' => Hash::make('123456'),
                'so_dien_thoai' => '0905555666',
                'chuc_danh' => 'Giảng viên Tiếng Anh IELTS (Band 8.5)',
                'so_nam_kinh_nghiem' => 6,
                'mo_ta' => 'Cựu du học sinh Anh Quốc, chuyên gia phương pháp phản xạ giao tiếp tự nhiên và chiến thuật làm bài IELTS.',
                'hinh_anh' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=240&q=80',
                'trang_thai_duyet' => 'da_duyet',
                'is_active' => 1,
                'tinh_trang' => 1,
            ],
            [
                'ho_ten' => 'Cô Lê Thị Hoàng Yến',
                'email' => 'hoangyen.le@edulink.edu.vn',
                'password' => Hash::make('123456'),
                'so_dien_thoai' => '0905777888',
                'chuc_danh' => 'Tiến sĩ Hóa học Ứng dụng',
                'so_nam_kinh_nghiem' => 10,
                'mo_ta' => 'Giảng viên Đại học Sư Phạm, hơn 10 năm kinh nghiệm bồi dưỡng học sinh giỏi Quốc gia môn Hóa học.',
                'hinh_anh' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=240&q=80',
                'trang_thai_duyet' => 'da_duyet',
                'is_active' => 1,
                'tinh_trang' => 1,
            ],
        ];

        $giaoVienList = [];
        foreach ($giaoViensData as $gv) {
            $record = GiaoVien::firstOrCreate(
                ['email' => $gv['email']],
                $gv
            );
            $giaoVienList[] = $record;
        }
        $this->command->info('-> Đã kiểm tra/khởi tạo '.count($giaoVienList).' giáo viên chuẩn EduLink.');

        // -------------------------------------------------------------
        // 4. KIỂM TRA HỌC VIÊN CHÍNH
        // -------------------------------------------------------------
        $hocVienChinh = HocVien::where('email', 'student.demo@smarttrial.test')->first();
        if (! $hocVienChinh) {
            $hocVienChinh = HocVien::firstOrCreate(
                ['email' => 'student.demo@smarttrial.test'],
                [
                    'ho_ten' => 'Phú Hưng Phạm',
                    'password' => Hash::make('123456'),
                    'so_dien_thoai' => '0935663037',
                    'is_active' => 1,
                    'tinh_trang' => 1,
                ]
            );
        }
        $this->command->info('-> Học viên đăng ký lịch học: '.$hocVienChinh->ho_ten.' (ID: '.$hocVienChinh->id.')');

        $hocVienPhu = null;

        // -------------------------------------------------------------
        // 5. SEED LỚP HỌC (LopHoc)
        // -------------------------------------------------------------
        // Tính toán các mốc thời gian dựa theo tuần hiện tại (Now = 2026-10-02)
        $now = Carbon::now();
        $startOfWeek = $now->copy()->startOfWeek(Carbon::MONDAY); // Thứ 2 tuần này: 2026-09-28
        $nextWeek = $startOfWeek->copy()->addWeek();              // Thứ 2 tuần sau: 2026-10-05

        // Xóa các lớp cũ nếu cần hoặc kiểm tra trùng
        // Ta tạo danh sách các lớp học phong phú:
        $lopHocsSeed = [
            // --- TUẦN NÀY (CURRENT WEEK: 28/09 - 04/10/2026) ---
            [
                'id_giao_vien' => $giaoVienList[0]->id, // Cô Sarah Jenkins
                'id_mon_hoc' => $monHocMap['Toán học 12 (Luyện thi THPT)'],
                'id_phong_hoc' => $phongHocMap['P.301'],
                'loai_lop' => 'dai_tra',
                'hinh_thuc' => 'offline',
                'link_online' => null,
                'hoc_phi' => 1800000.00,
                'si_so_toi_da' => 25,
                'thoi_gian_bat_dau' => $startOfWeek->copy()->addDays(0)->setTime(9, 0, 0), // T2: 09:00 - 11:00
                'thoi_gian_ket_thuc' => $startOfWeek->copy()->addDays(0)->setTime(11, 0, 0),
                'tinh_trang' => 'dang_mo',
                'enroll_student' => true,
            ],
            [
                'id_giao_vien' => $giaoVienList[2]->id, // Thầy Nguyễn Minh Triết
                'id_mon_hoc' => $monHocMap['Tiếng Anh IELTS Master'],
                'id_phong_hoc' => null,
                'loai_lop' => 'dai_tra',
                'hinh_thuc' => 'online',
                'link_online' => 'https://meet.google.com/ielts-speaking-pro',
                'hoc_phi' => 2500000.00,
                'si_so_toi_da' => 20,
                'thoi_gian_bat_dau' => $startOfWeek->copy()->addDays(2)->setTime(19, 0, 0), // T4: 19:00 - 21:00
                'thoi_gian_ket_thuc' => $startOfWeek->copy()->addDays(2)->setTime(21, 0, 0),
                'tinh_trang' => 'dang_mo',
                'enroll_student' => true,
            ],
            [
                'id_giao_vien' => $giaoVienList[1]->id, // Thầy David Chen
                'id_mon_hoc' => $monHocMap['Lập trình Web Frontend (Vue.js)'],
                'id_phong_hoc' => $phongHocMap['Lab-01'],
                'loai_lop' => 'dai_tra',
                'hinh_thuc' => 'offline',
                'link_online' => null,
                'hoc_phi' => 2800000.00,
                'si_so_toi_da' => 25,
                'thoi_gian_bat_dau' => $startOfWeek->copy()->addDays(4)->setTime(14, 0, 0), // T6: 14:00 - 16:30 (Hôm nay!)
                'thoi_gian_ket_thuc' => $startOfWeek->copy()->addDays(4)->setTime(16, 30, 0),
                'tinh_trang' => 'dang_mo',
                'enroll_student' => true,
            ],
            [
                'id_giao_vien' => $giaoVienList[0]->id, // Cô Sarah Jenkins
                'id_mon_hoc' => $monHocMap['Vật lý 12 Nâng cao'],
                'id_phong_hoc' => $phongHocMap['P.202'],
                'loai_lop' => 'kem',
                'hinh_thuc' => 'offline',
                'link_online' => null,
                'hoc_phi' => 3200000.00,
                'si_so_toi_da' => 5,
                'thoi_gian_bat_dau' => $startOfWeek->copy()->addDays(5)->setTime(8, 30, 0), // T7: 08:30 - 10:30
                'thoi_gian_ket_thuc' => $startOfWeek->copy()->addDays(5)->setTime(10, 30, 0),
                'tinh_trang' => 'dang_mo',
                'enroll_student' => true,
            ],
            [
                'id_giao_vien' => $giaoVienList[3]->id, // Cô Lê Thị Hoàng Yến
                'id_mon_hoc' => $monHocMap['Hóa học 12 Chuyên sâu'],
                'id_phong_hoc' => null,
                'loai_lop' => 'dai_tra',
                'hinh_thuc' => 'online',
                'link_online' => 'https://meet.google.com/chemistry-thpt-vip',
                'hoc_phi' => 1950000.00,
                'si_so_toi_da' => 30,
                'thoi_gian_bat_dau' => $startOfWeek->copy()->addDays(6)->setTime(15, 0, 0), // CN: 15:00 - 17:00
                'thoi_gian_ket_thuc' => $startOfWeek->copy()->addDays(6)->setTime(17, 0, 0),
                'tinh_trang' => 'dang_mo',
                'enroll_student' => true,
            ],

            // --- TUẦN SAU (NEXT WEEK: 05/10 - 11/10/2026) ---
            [
                'id_giao_vien' => $giaoVienList[0]->id, // Cô Sarah Jenkins
                'id_mon_hoc' => $monHocMap['Toán học 12 (Luyện thi THPT)'],
                'id_phong_hoc' => $phongHocMap['P.301'],
                'loai_lop' => 'dai_tra',
                'hinh_thuc' => 'offline',
                'link_online' => null,
                'hoc_phi' => 1800000.00,
                'si_so_toi_da' => 25,
                'thoi_gian_bat_dau' => $nextWeek->copy()->addDays(0)->setTime(9, 0, 0), // T2 tuần sau: 09:00 - 11:00
                'thoi_gian_ket_thuc' => $nextWeek->copy()->addDays(0)->setTime(11, 0, 0),
                'tinh_trang' => 'dang_mo',
                'enroll_student' => true,
            ],
            [
                'id_giao_vien' => $giaoVienList[2]->id, // Thầy Nguyễn Minh Triết
                'id_mon_hoc' => $monHocMap['Tiếng Anh IELTS Master'],
                'id_phong_hoc' => null,
                'loai_lop' => 'dai_tra',
                'hinh_thuc' => 'online',
                'link_online' => 'https://meet.google.com/ielts-speaking-pro',
                'hoc_phi' => 2500000.00,
                'si_so_toi_da' => 20,
                'thoi_gian_bat_dau' => $nextWeek->copy()->addDays(2)->setTime(19, 0, 0), // T4 tuần sau: 19:00 - 21:00
                'thoi_gian_ket_thuc' => $nextWeek->copy()->addDays(2)->setTime(21, 0, 0),
                'tinh_trang' => 'dang_mo',
                'enroll_student' => true,
            ],
            [
                'id_giao_vien' => $giaoVienList[1]->id, // Thầy David Chen
                'id_mon_hoc' => $monHocMap['Lập trình Python & AI Cơ bản'],
                'id_phong_hoc' => $phongHocMap['Lab-02'],
                'loai_lop' => 'dai_tra',
                'hinh_thuc' => 'offline',
                'link_online' => null,
                'hoc_phi' => 2900000.00,
                'si_so_toi_da' => 20,
                'thoi_gian_bat_dau' => $nextWeek->copy()->addDays(5)->setTime(18, 0, 0), // T7 tuần sau: 18:00 - 20:30
                'thoi_gian_ket_thuc' => $nextWeek->copy()->addDays(5)->setTime(20, 30, 0),
                'tinh_trang' => 'dang_mo',
                'enroll_student' => true,
            ],
            [
                'id_giao_vien' => $giaoVienList[0]->id, // Cô Sarah Jenkins
                'id_mon_hoc' => $monHocMap['Toán tư duy & Thống kê'],
                'id_phong_hoc' => $phongHocMap['P.405'],
                'loai_lop' => 'kem',
                'hinh_thuc' => 'offline',
                'link_online' => null,
                'hoc_phi' => 2200000.00,
                'si_so_toi_da' => 5,
                'thoi_gian_bat_dau' => $nextWeek->copy()->addDays(3)->setTime(14, 0, 0), // T5 tuần sau: 14:00 - 16:00
                'thoi_gian_ket_thuc' => $nextWeek->copy()->addDays(3)->setTime(16, 0, 0),
                'tinh_trang' => 'dang_mo',
                'enroll_student' => false, // Lớp mở để học viên khám phá và đăng ký thêm
            ],
            [
                'id_giao_vien' => $giaoVienList[3]->id, // Cô Lê Thị Hoàng Yến
                'id_mon_hoc' => $monHocMap['Ngữ văn 12 - Luyện đề chuẩn'],
                'id_phong_hoc' => null,
                'loai_lop' => 'dai_tra',
                'hinh_thuc' => 'online',
                'link_online' => 'https://meet.google.com/van-thpt-vip-12',
                'hoc_phi' => 1650000.00,
                'si_so_toi_da' => 30,
                'thoi_gian_bat_dau' => $nextWeek->copy()->addDays(6)->setTime(8, 30, 0), // CN tuần sau: 08:30 - 10:30
                'thoi_gian_ket_thuc' => $nextWeek->copy()->addDays(6)->setTime(10, 30, 0),
                'tinh_trang' => 'dang_mo',
                'enroll_student' => false, // Lớp mở để học viên khám phá
            ],

            // --- CÁC LỚP MỞ BỔ SUNG ĐỂ HỌC VIÊN ĐĂNG KÝ & TEST TRÙNG GIỜ ---
            // 1. Toán học 12 - Ca Thứ 3 (08:30 - 10:30) [KHÔNG TRÙNG]
            [
                'id_giao_vien' => $giaoVienList[0]->id, // Cô Sarah Jenkins
                'id_mon_hoc' => $monHocMap['Toán học 12 (Luyện thi THPT)'],
                'id_phong_hoc' => $phongHocMap['P.301'],
                'loai_lop' => 'dai_tra',
                'hinh_thuc' => 'offline',
                'link_online' => null,
                'hoc_phi' => 1800000.00,
                'si_so_toi_da' => 25,
                'thoi_gian_bat_dau' => $startOfWeek->copy()->addDays(1)->setTime(8, 30, 0), // T3: 08:30 - 10:30
                'thoi_gian_ket_thuc' => $startOfWeek->copy()->addDays(1)->setTime(10, 30, 0),
                'tinh_trang' => 'dang_mo',
                'enroll_student' => false,
            ],
            // 2. Toán học 12 - Ca Thứ 6 (14:00 - 16:30) [TRÙNG VỚI LỚP WEB FRONTEND T6 CỦA HỌC VIÊN]
            [
                'id_giao_vien' => $giaoVienList[2]->id, // Thầy Triết
                'id_mon_hoc' => $monHocMap['Toán học 12 (Luyện thi THPT)'],
                'id_phong_hoc' => null,
                'loai_lop' => 'dai_tra',
                'hinh_thuc' => 'online',
                'link_online' => 'https://meet.google.com/toan-12-online-vip',
                'hoc_phi' => 1750000.00,
                'si_so_toi_da' => 30,
                'thoi_gian_bat_dau' => $startOfWeek->copy()->addDays(4)->setTime(14, 0, 0), // T6: 14:00 - 16:30 (Trùng Web Frontend)
                'thoi_gian_ket_thuc' => $startOfWeek->copy()->addDays(4)->setTime(16, 30, 0),
                'tinh_trang' => 'dang_mo',
                'enroll_student' => false,
            ],
            // 3. Lập trình Web Frontend - Ca Thứ 6 (15:00 - 17:30) [TRÙNG VỚI LỚP WEB FRONTEND HIỆN TẠI]
            [
                'id_giao_vien' => $giaoVienList[1]->id, // Thầy David Chen
                'id_mon_hoc' => $monHocMap['Lập trình Web Frontend (Vue.js)'],
                'id_phong_hoc' => $phongHocMap['Lab-01'],
                'loai_lop' => 'dai_tra',
                'hinh_thuc' => 'offline',
                'link_online' => null,
                'hoc_phi' => 2800000.00,
                'si_so_toi_da' => 25,
                'thoi_gian_bat_dau' => $startOfWeek->copy()->addDays(4)->setTime(15, 0, 0), // T6: 15:00 - 17:30 (Trùng ca 14:00 - 16:30)
                'thoi_gian_ket_thuc' => $startOfWeek->copy()->addDays(4)->setTime(17, 30, 0),
                'tinh_trang' => 'dang_mo',
                'enroll_student' => false,
            ],
            // 4. Lập trình Web Frontend - Ca Thứ 5 (18:30 - 21:00) [KHÔNG TRÙNG]
            [
                'id_giao_vien' => $giaoVienList[1]->id, // Thầy David Chen
                'id_mon_hoc' => $monHocMap['Lập trình Web Frontend (Vue.js)'],
                'id_phong_hoc' => null,
                'loai_lop' => 'dai_tra',
                'hinh_thuc' => 'online',
                'link_online' => 'https://meet.google.com/vue3-night-master',
                'hoc_phi' => 2800000.00,
                'si_so_toi_da' => 25,
                'thoi_gian_bat_dau' => $startOfWeek->copy()->addDays(3)->setTime(18, 30, 0), // T5: 18:30 - 21:00
                'thoi_gian_ket_thuc' => $startOfWeek->copy()->addDays(3)->setTime(21, 0, 0),
                'tinh_trang' => 'dang_mo',
                'enroll_student' => false,
            ],
            // 5. Tiếng Anh IELTS Master - Ca Thứ 4 (19:30 - 21:30) [TRÙNG VỚI IELTS T4 CỦA HỌC VIÊN]
            [
                'id_giao_vien' => $giaoVienList[2]->id, // Thầy Nguyễn Minh Triết
                'id_mon_hoc' => $monHocMap['Tiếng Anh IELTS Master'],
                'id_phong_hoc' => null,
                'loai_lop' => 'dai_tra',
                'hinh_thuc' => 'online',
                'link_online' => 'https://meet.google.com/ielts-speaking-conflict',
                'hoc_phi' => 2500000.00,
                'si_so_toi_da' => 20,
                'thoi_gian_bat_dau' => $startOfWeek->copy()->addDays(2)->setTime(19, 30, 0), // T4: 19:30 - 21:30 (Trùng ca 19:00 - 21:00)
                'thoi_gian_ket_thuc' => $startOfWeek->copy()->addDays(2)->setTime(21, 30, 0),
                'tinh_trang' => 'dang_mo',
                'enroll_student' => false,
            ],
            // 6. Tiếng Anh IELTS Master - Ca Thứ 3 (19:00 - 21:00) [KHÔNG TRÙNG]
            [
                'id_giao_vien' => $giaoVienList[2]->id, // Thầy Nguyễn Minh Triết
                'id_mon_hoc' => $monHocMap['Tiếng Anh IELTS Master'],
                'id_phong_hoc' => $phongHocMap['P.202'],
                'loai_lop' => 'dai_tra',
                'hinh_thuc' => 'offline',
                'link_online' => null,
                'hoc_phi' => 2600000.00,
                'si_so_toi_da' => 18,
                'thoi_gian_bat_dau' => $startOfWeek->copy()->addDays(1)->setTime(19, 0, 0), // T3: 19:00 - 21:00
                'thoi_gian_ket_thuc' => $startOfWeek->copy()->addDays(1)->setTime(21, 0, 0),
                'tinh_trang' => 'dang_mo',
                'enroll_student' => false,
            ],
            // 7. Vật lý 12 Nâng cao - Ca Thứ 7 (09:00 - 11:00) [TRÙNG VẬT LÝ T7 CỦA HỌC VIÊN]
            [
                'id_giao_vien' => $giaoVienList[0]->id, // Cô Sarah Jenkins
                'id_mon_hoc' => $monHocMap['Vật lý 12 Nâng cao'],
                'id_phong_hoc' => $phongHocMap['P.202'],
                'loai_lop' => 'kem',
                'hinh_thuc' => 'offline',
                'link_online' => null,
                'hoc_phi' => 3200000.00,
                'si_so_toi_da' => 5,
                'thoi_gian_bat_dau' => $startOfWeek->copy()->addDays(5)->setTime(9, 0, 0), // T7: 09:00 - 11:00 (Trùng ca 08:30 - 10:30)
                'thoi_gian_ket_thuc' => $startOfWeek->copy()->addDays(5)->setTime(11, 0, 0),
                'tinh_trang' => 'dang_mo',
                'enroll_student' => false,
            ],
            // 8. Python AI & Data Science - Ca Chiều Thứ 2 (14:00 - 16:30) [KHÔNG TRÙNG]
            [
                'id_giao_vien' => $giaoVienList[1]->id, // Thầy David Chen
                'id_mon_hoc' => $monHocMap['Lập trình Python & AI Cơ bản'],
                'id_phong_hoc' => $phongHocMap['Lab-02'],
                'loai_lop' => 'dai_tra',
                'hinh_thuc' => 'offline',
                'link_online' => null,
                'hoc_phi' => 2900000.00,
                'si_so_toi_da' => 20,
                'thoi_gian_bat_dau' => $startOfWeek->copy()->addDays(0)->setTime(14, 0, 0), // T2: 14:00 - 16:30
                'thoi_gian_ket_thuc' => $startOfWeek->copy()->addDays(0)->setTime(16, 30, 0),
                'tinh_trang' => 'dang_mo',
                'enroll_student' => false,
            ],
        ];

        $enrolledCount = 0;
        foreach ($lopHocsSeed as $index => $item) {
            $shouldEnroll = $item['enroll_student'];
            unset($item['enroll_student']);

            // Tìm hoặc tạo lớp học
            $lop = LopHoc::firstOrCreate(
                [
                    'id_giao_vien' => $item['id_giao_vien'],
                    'id_mon_hoc' => $item['id_mon_hoc'],
                    'thoi_gian_bat_dau' => $item['thoi_gian_bat_dau'],
                ],
                $item
            );
            app(LessonScheduleService::class)->ensureSessions($lop);

            // Ghi danh học viên chính vào lớp
            if ($shouldEnroll) {
                DangKyLop::firstOrCreate(
                    [
                        'id_lop_hoc' => $lop->id,
                        'id_hoc_vien' => $hocVienChinh->id,
                    ],
                    [
                        'ngay_dang_ky' => $item['thoi_gian_bat_dau']->copy()->subDays(3),
                        'trang_thai' => 'da_xac_nhan',
                    ]
                );
                $enrolledCount++;

                // Thêm học viên phụ vào lớp cho sinh động
                if ($hocVienPhu) {
                    DangKyLop::firstOrCreate(
                        [
                            'id_lop_hoc' => $lop->id,
                            'id_hoc_vien' => $hocVienPhu->id,
                        ],
                        [
                            'ngay_dang_ky' => $item['thoi_gian_bat_dau']->copy()->subDays(2),
                            'trang_thai' => 'da_xac_nhan',
                        ]
                    );
                }
            }
        }

        $this->command->info('-> Đã tạo '.count($lopHocsSeed).' lớp học mẫu (cả Online & Offline).');
        $this->command->info('-> Đã đăng ký '.$enrolledCount.' lớp học vào Lịch học của học viên: '.$hocVienChinh->ho_ten);

        // -------------------------------------------------------------
        // 6. SEED LỊCH RẢNH MẪU CHO HỌC VIÊN & GIÁO VIÊN
        // -------------------------------------------------------------
        // Bổ sung lịch mẫu mà không xóa lịch đã chỉnh trước đó.
        $studentFreeSlots = [
            ['ngay_trong_tuan' => 1, 'thoi_gian_bat_dau' => '09:00:00', 'thoi_gian_ket_thuc' => '10:00:00'], // Thứ 2 09:00
            ['ngay_trong_tuan' => 2, 'thoi_gian_bat_dau' => '10:00:00', 'thoi_gian_ket_thuc' => '11:00:00'], // Thứ 3 10:00
            ['ngay_trong_tuan' => 3, 'thoi_gian_bat_dau' => '09:00:00', 'thoi_gian_ket_thuc' => '10:00:00'], // Thứ 4 09:00
            ['ngay_trong_tuan' => 4, 'thoi_gian_bat_dau' => '11:00:00', 'thoi_gian_ket_thuc' => '12:00:00'], // Thứ 5 11:00
            ['ngay_trong_tuan' => 5, 'thoi_gian_bat_dau' => '14:00:00', 'thoi_gian_ket_thuc' => '15:00:00'], // Thứ 6 14:00
            ['ngay_trong_tuan' => 6, 'thoi_gian_bat_dau' => '09:00:00', 'thoi_gian_ket_thuc' => '10:00:00'], // Thứ 7 09:00
        ];
        foreach ($studentFreeSlots as $slot) {
            ThoiGianRanh::firstOrCreate([
                'id_giao_vien' => null,
                'id_hoc_vien' => $hocVienChinh->id,
                'loai_nguoi_dung' => 'hoc_vien',
                'ngay_trong_tuan' => $slot['ngay_trong_tuan'],
                'thoi_gian_bat_dau' => $slot['thoi_gian_bat_dau'],
                'thoi_gian_ket_thuc' => $slot['thoi_gian_ket_thuc'],
                'trang_thai' => 'active',
            ]);
        }
        $this->command->info('-> Đã tạo '.count($studentFreeSlots).' khung giờ rảnh cho học viên '.$hocVienChinh->ho_ten);

        $this->command->info('=== HOÀN TẤT SEED LỚP HỌC & LỊCH HỌC THÀNH CÔNG! ===');
    }
}
