<?php

namespace App\Services;

use App\Models\BuoiHoc;
use App\Models\GiaoVien;
use App\Models\HocVien;
use App\Models\ThoiGianRanh;
use Carbon\Carbon;

class TeacherMatchingService
{
    public function eligible(int $teacher, int $subject): bool
    {
        return GiaoVien::whereKey($teacher)->where('trang_thai_duyet', 'da_duyet')->where('is_block', 0)
            ->where('is_active', 1)->where('tinh_trang', 1)->whereHas('classes', fn ($q) => $q->where('id_mon_hoc', $subject)->whereIn('tinh_trang', ['sap_mo', 'dang_mo', 'dang_hoc']))->exists();
    }

    public function available(GiaoVien $teacher, HocVien $student, Carbon $start, Carbon $end, bool $requireStudentAvailability = true): bool
    {
        if (! $start->gt(now()) || ! $end->gt($start) || ! $start->isSameDay($end)) {
            return false;
        }
        $contains = function ($actor) use ($start, $end) {
            foreach ($this->availabilityIntervals($actor, $start) as [$slotStart, $slotEnd]) {
                if ($slotStart->lte($start) && $slotEnd->gte($end)) {
                    return true;
                }
            }

            return false;
        };
        if (! $contains($teacher) || ($requireStudentAvailability && ! $contains($student))) {
            return false;
        }

        return ! BuoiHoc::where('trang_thai', 'scheduled')->where('thoi_gian_bat_dau', '<', $end)->where('thoi_gian_ket_thuc', '>', $start)
            ->whereHas('lopHoc', fn ($q) => $q->whereNotIn('tinh_trang', ['da_huy', 'da_ket_thuc'])->where(function ($q) use ($teacher, $student) {
                $q->where('id_giao_vien', $teacher->id)->orWhereHas('dangKyLops', fn ($q) => $q->where('id_hoc_vien', $student->id)->whereIn('trang_thai', ['da_xac_nhan', 'da_thanh_toan']));
            }))->exists();
    }

    public function suggestions(HocVien $student, int $subject, Carbon $date, int $duration): array
    {
        $teachers = GiaoVien::where('trang_thai_duyet', 'da_duyet')->where('is_block', 0)->where('tinh_trang', 1)->where('is_active', 1)
            ->whereHas('classes', fn ($q) => $q->where('id_mon_hoc', $subject)->whereIn('tinh_trang', ['sap_mo', 'dang_mo', 'dang_hoc']))->orderBy('id')->get();
        $rows = [];
        foreach ($teachers as $teacher) {
            $slots = [];
            foreach ($this->availabilityIntervals($teacher, $date) as [$start, $last]) {
                while ($start->copy()->addMinutes($duration)->lte($last)) {
                    $end = $start->copy()->addMinutes($duration);
                    if ($this->available($teacher, $student, $start, $end)) {
                        $slots[$start->timestamp] = ['start' => $start->toIso8601String(), 'end' => $end->toIso8601String()];
                    }
                    $start->addMinutes(15);
                }
            }
            if ($slots) {
                ksort($slots);
                $rows[] = ['id_giao_vien' => $teacher->id, 'ho_ten' => $teacher->ho_ten, 'hinh_anh' => $teacher->hinh_anh,
                    'chuc_danh' => $teacher->chuc_danh, 'id_mon_hoc' => $subject, 'slots' => array_values($slots)];
            }
        }

        return $rows;
    }

    private function availabilityIntervals(GiaoVien|HocVien $actor, Carbon $date): array
    {
        $isTeacher = $actor instanceof GiaoVien;
        $rows = ThoiGianRanh::where('loai_nguoi_dung', $isTeacher ? 'giao_vien' : 'hoc_vien')
            ->where($isTeacher ? 'id_giao_vien' : 'id_hoc_vien', $actor->id)
            ->where('ngay_trong_tuan', $date->dayOfWeek)->whereIn('trang_thai', ['active', 'hoat_dong'])
            ->orderBy('thoi_gian_bat_dau')->get();
        $merged = [];
        foreach ($rows as $row) {
            $start = $date->copy()->setTimeFromTimeString($row->thoi_gian_bat_dau);
            $end = $date->copy()->setTimeFromTimeString($row->thoi_gian_ket_thuc);
            if (! $end->gt($start)) {
                continue;
            }
            $last = count($merged) - 1;
            if ($last >= 0 && $start->lte($merged[$last][1])) {
                if ($end->gt($merged[$last][1])) {
                    $merged[$last][1] = $end;
                }
            } else {
                $merged[] = [$start, $end];
            }
        }

        return $merged;
    }
}
