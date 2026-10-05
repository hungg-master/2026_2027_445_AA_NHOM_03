<?php

namespace App\Services;

use App\Models\DangKyLop;
use App\Models\GiaoVien;
use App\Models\HocVien;
use App\Models\LopHoc;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class EnrollmentService
{
    public function check(HocVien $student, LopHoc $class, ?int $excludeEnrollment = null): void
    {
        abort_unless(in_array($class->tinh_trang, ['sap_mo', 'dang_mo']), 409, 'Lớp không nhận đăng ký.');
        $teacher = GiaoVien::whereKey($class->id_giao_vien)->lockForUpdate()->firstOrFail();
        abort_unless($teacher->trang_thai_duyet === 'da_duyet' && ! $teacher->is_block && $teacher->tinh_trang == 1 && $teacher->is_active, 403, 'Giáo viên chưa đủ điều kiện.');
        app(LessonScheduleService::class)->ensureSessions($class);
        $future = $class->buoiHocs()->where('trang_thai', 'scheduled')->where('thoi_gian_bat_dau', '>', now())->get();
        abort_if($future->isEmpty(), 409, 'Lớp không còn buổi học sắp tới.');
        $count = $class->dangKyLops()->whereIn('trang_thai', ['da_xac_nhan', 'da_thanh_toan'])
            ->when($excludeEnrollment, fn ($q) => $q->where('id', '!=', $excludeEnrollment))->count();
        abort_if($count >= $class->si_so_toi_da, 409, 'Lớp đã đủ sĩ số.');
        $active = DangKyLop::where('id_hoc_vien', $student->id)->where('id_lop_hoc', '!=', $class->id)
            ->where('trang_thai', '!=', 'da_huy')->whereHas('lopHoc', fn ($q) => $q->whereNotIn('tinh_trang', ['da_huy', 'da_ket_thuc']))
            ->with('lopHoc.buoiHocs')->get();
        foreach ($active as $enrollment) {
            $other = $enrollment->lopHoc;
            app(LessonScheduleService::class)->ensureSessions($other);
            $sessions = $other->buoiHocs()->where('trang_thai', 'scheduled')->where('thoi_gian_ket_thuc', '>', now())->get();
            if ($sessions->isNotEmpty() && $other->id_mon_hoc === $class->id_mon_hoc) {
                throw ValidationException::withMessages(['id_lop_hoc' => 'Bạn đang học một lớp khác của môn này.']);
            }
            foreach ($sessions as $busy) {
                foreach ($future as $target) {
                    if ($target->thoi_gian_bat_dau->lt($busy->thoi_gian_ket_thuc) && $target->thoi_gian_ket_thuc->gt($busy->thoi_gian_bat_dau)) {
                        throw ValidationException::withMessages(['id_lop_hoc' => 'Buổi học bị trùng với lịch đã đăng ký.']);
                    }
                }
            }
        }
    }

    public function register(HocVien $student, int $classId, string $proof): DangKyLop
    {
        return DB::transaction(function () use ($student, $classId, $proof) {
            $student = HocVien::whereKey($student->id)->lockForUpdate()->firstOrFail();
            $class = LopHoc::whereKey($classId)->lockForUpdate()->firstOrFail();
            $existing = DangKyLop::where('id_hoc_vien', $student->id)->where('id_lop_hoc', $classId)->lockForUpdate()->first();
            $this->check($student, $class, $existing?->id);
            app(FaceVerificationService::class)->consume($student, $proof, 'enrollment', $classId);
            $enrollment = $existing ?? new DangKyLop(['id_hoc_vien' => $student->id, 'id_lop_hoc' => $classId]);
            $enrollment->fill(['trang_thai' => 'da_xac_nhan', 'ngay_dang_ky' => now()])->save();

            return $enrollment->load(['lopHoc.monHoc', 'lopHoc.giaoVien', 'lopHoc.phongHoc']);
        }, 5);
    }
}
