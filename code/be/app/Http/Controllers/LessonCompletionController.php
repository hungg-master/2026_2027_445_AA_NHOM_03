<?php

namespace App\Http\Controllers;

use App\Models\BuoiHoc;
use App\Models\ChiTietPhongHop;
use App\Models\GiaoVien;
use App\Models\LopHoc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LessonCompletionController extends Controller
{
    public function complete(Request $request, int $id)
    {
        $data = $request->validate(['attended_student_ids' => 'nullable|array|max:30', 'attended_student_ids.*' => 'integer|distinct']);
        $session = DB::transaction(function () use ($id, $data) {
            $teacher = Auth::guard('sanctum')->user();
            GiaoVien::whereKey($teacher->id)->lockForUpdate()->firstOrFail();
            $preview = BuoiHoc::findOrFail($id);
            $class = LopHoc::where('id_giao_vien', $teacher->id)->lockForUpdate()->findOrFail($preview->id_lop_hoc);
            $session = BuoiHoc::whereKey($id)->lockForUpdate()->firstOrFail();
            abort_if($session->trang_thai === 'cancelled' || $class->tinh_trang === 'da_huy', 409, 'Buổi học đã hủy.');
            abort_if(now()->lt($session->thoi_gian_ket_thuc), 409, 'Buổi học chưa kết thúc.');
            if ($session->trang_thai === 'completed') {
                return $session;
            }
            if ($class->hinh_thuc === 'offline') {
                $ids = $data['attended_student_ids'] ?? [];
                $enrolled = $class->dangKyLops()->whereIn('trang_thai', ['da_xac_nhan', 'da_thanh_toan'])->pluck('id_hoc_vien')->all();
                if (array_diff($ids, $enrolled)) {
                    throw ValidationException::withMessages(['attended_student_ids' => 'Chỉ điểm danh học viên đang đăng ký lớp.']);
                }
                $actors = [['giao_vien', $teacher->id]];
                foreach ($ids as $student) {
                    $actors[] = ['hoc_vien', $student];
                }
                foreach ($actors as [$role, $actorId]) {
                    ChiTietPhongHop::updateOrCreate([
                        'id_buoi_hoc' => $session->id, 'loai_nguoi_dung' => $role, 'id_nguoi_dung' => $actorId,
                    ], ['id_phong_hop' => null, 'attendance_source' => 'teacher_attested', 'xac_thuc_khuon_mat' => 0,
                        'is_active' => 0, 'trang_thai' => 1, 'thoi_gian_tham_gia' => $session->thoi_gian_bat_dau,
                        'thoi_gian_roi' => $session->thoi_gian_ket_thuc]);
                }
            } else {
                ChiTietPhongHop::where('id_buoi_hoc', $session->id)->where('is_active', 1)->update(['is_active' => 0, 'thoi_gian_roi' => $session->thoi_gian_ket_thuc]);
            }
            $session->update(['trang_thai' => 'completed']);
            if (! $class->buoiHocs()->where('trang_thai', 'scheduled')->exists()) {
                $class->update(['tinh_trang' => 'da_ket_thuc']);
                $class->phongHop?->update(['trang_thai' => 0]);
            }

            return $session;
        }, 5);

        return response()->json(['status' => true, 'data' => $session]);
    }
}
