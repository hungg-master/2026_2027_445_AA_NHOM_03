<?php

namespace App\Services;

use App\Models\BuoiHoc;
use App\Models\DangKyLop;
use App\Models\GiaoVien;
use App\Models\HocVien;

class SessionAccessService
{
    public function authorize(HocVien|GiaoVien $actor, BuoiHoc $session): void
    {
        $class = $session->lopHoc;
        abort_if(! $class || in_array($class->tinh_trang, ['da_huy', 'da_ket_thuc']) || $session->trang_thai !== 'scheduled', 403, 'Buổi học không còn mở.');
        $teacher = $class->giaoVien;
        abort_unless($teacher && $teacher->trang_thai_duyet === 'da_duyet' && ! $teacher->is_block && $teacher->tinh_trang == 1, 403, 'Giáo viên chưa đủ điều kiện giảng dạy.');
        if ($actor instanceof GiaoVien) {
            abort_unless($class->id_giao_vien === $actor->id, 403, 'Bạn không phụ trách buổi học này.');
        } else {
            abort_unless(DangKyLop::where('id_hoc_vien', $actor->id)->where('id_lop_hoc', $class->id)
                ->whereIn('trang_thai', ['da_xac_nhan', 'da_thanh_toan'])->exists(), 403, 'Bạn chưa được ghi danh vào buổi học này.');
        }
    }
}
