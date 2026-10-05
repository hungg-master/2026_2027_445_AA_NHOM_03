<?php

namespace App\Services;

use App\Models\BuoiHoc;
use App\Models\GiaoVien;
use App\Models\HocVien;
use App\Models\LopHoc;
use App\Models\PhongHoc;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LessonScheduleService
{
    public function intervals(array $data): array
    {
        $start = Carbon::parse($data['thoi_gian_bat_dau'])->setTimezone(config('app.timezone'));
        $end = Carbon::parse($data['thoi_gian_ket_thuc'])->setTimezone(config('app.timezone'));
        $until = ($data['recurrence'] ?? 'once') === 'weekly'
            ? Carbon::parse($data['recurrence_until'])->endOfDay() : $start->copy();
        if ($until->lt($start) || $until->gt($start->copy()->addYear())) {
            throw ValidationException::withMessages(['recurrence_until' => 'Ngày kết thúc phải từ buổi đầu đến tối đa một năm.']);
        }
        $rows = [];
        do {
            $rows[] = ['thoi_gian_bat_dau' => $start->copy(), 'thoi_gian_ket_thuc' => $end->copy()];
            $start->addWeek();
            $end->addWeek();
        } while (($data['recurrence'] ?? 'once') === 'weekly' && $start->lte($until));

        return $rows;
    }

    public function ensureSessions(LopHoc $class): void
    {
        if (! $class->buoiHocs()->exists()) {
            foreach ($this->intervals($class->toArray()) as $row) {
                $class->buoiHocs()->create($row);
            }
        }
    }

    public function checkResources(int $teacherId, ?int $roomId, array $intervals, ?int $excludeClass = null): void
    {
        GiaoVien::whereKey($teacherId)->lockForUpdate()->firstOrFail();
        if ($roomId) {
            PhongHoc::whereKey($roomId)->lockForUpdate()->firstOrFail();
        }
        foreach ($intervals as $interval) {
            $conflict = BuoiHoc::where('trang_thai', 'scheduled')
                ->where('thoi_gian_bat_dau', '<', $interval['thoi_gian_ket_thuc'])
                ->where('thoi_gian_ket_thuc', '>', $interval['thoi_gian_bat_dau'])
                ->when($excludeClass, fn ($q) => $q->where('id_lop_hoc', '!=', $excludeClass))
                ->whereHas('lopHoc', function ($q) use ($teacherId, $roomId) {
                    $q->whereNotIn('tinh_trang', ['da_huy', 'da_ket_thuc'])->where(function ($q) use ($teacherId, $roomId) {
                        $q->where('id_giao_vien', $teacherId);
                        if ($roomId) {
                            $q->orWhere('id_phong_hoc', $roomId);
                        }
                    });
                })->exists();
            if ($conflict) {
                throw ValidationException::withMessages(['thoi_gian_bat_dau' => 'Lịch giáo viên hoặc phòng học bị trùng.']);
            }
        }
    }

    public function calendar(Request $request, HocVien|GiaoVien $actor)
    {
        $request->validate([
            'from_date' => 'nullable|date_format:Y-m-d', 'to_date' => 'nullable|date_format:Y-m-d|after_or_equal:from_date',
            'id_mon_hoc' => 'nullable|integer|exists:mon_hocs,id',
        ]);
        $query = BuoiHoc::with(['lopHoc.monHoc', 'lopHoc.phongHoc', 'lopHoc.giaoVien', 'lopHoc.phongHop'])
            ->whereHas('lopHoc', function ($q) use ($request, $actor) {
                $q->where('tinh_trang', '!=', 'da_huy');
                if ($actor instanceof GiaoVien) {
                    $q->where('id_giao_vien', $actor->id);
                } else {
                    $q->whereHas('dangKyLops', fn ($q) => $q->where('id_hoc_vien', $actor->id)->whereIn('trang_thai', ['da_xac_nhan', 'da_thanh_toan']));
                }
                if ($request->filled('id_mon_hoc')) {
                    $q->where('id_mon_hoc', $request->integer('id_mon_hoc'));
                }
                if ($request->filled('tinh_trang')) {
                    $q->where('tinh_trang', $request->input('tinh_trang'));
                }
            });
        if ($request->filled('from_date')) {
            $query->where('thoi_gian_ket_thuc', '>=', Carbon::parse($request->from_date)->startOfDay());
        }
        if ($request->filled('to_date')) {
            $query->where('thoi_gian_bat_dau', '<=', Carbon::parse($request->to_date)->endOfDay());
        }

        return $query->orderBy('thoi_gian_bat_dau')->get()->map(function ($session) use ($actor) {
            $row = $session->toArray();
            $row['id_buoi_hoc'] = $session->id;
            $row['trang_thai_buoi'] = $session->trang_thai === 'cancelled' ? 'cancelled'
                : ($session->trang_thai === 'completed' || $session->thoi_gian_ket_thuc->lt(now()) ? 'da_hoc'
                : ($session->thoi_gian_bat_dau->lte(now()) ? 'dang_dien_ra' : 'sap_toi'));
            if ($actor instanceof HocVien) {
                $row['trang_thai_dang_ky'] = $session->lopHoc->dangKyLops()->where('id_hoc_vien', $actor->id)->value('trang_thai');
            }

            return $row;
        });
    }
}
