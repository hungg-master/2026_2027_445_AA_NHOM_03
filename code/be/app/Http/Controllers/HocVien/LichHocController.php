<?php

namespace App\Http\Controllers\HocVien;

use App\Http\Controllers\Controller;
use App\Http\Requests\DangKyLop\DangKyLopRequest;
use App\Models\DangKyLop;
use App\Models\HocVien;
use App\Models\LopHoc;
use App\Services\EnrollmentService;
use App\Services\LessonScheduleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LichHocController extends Controller
{
    public function lichHoc(Request $request, LessonScheduleService $schedule)
    {
        return response()->json(['status' => true, 'data' => $schedule->calendar($request, Auth::guard('sanctum')->user())]);
    }

    public function lopHocCuaToi()
    {
        $rows = DangKyLop::with(['lopHoc.monHoc', 'lopHoc.phongHoc', 'lopHoc.giaoVien', 'lopHoc.buoiHocs'])
            ->where('id_hoc_vien', Auth::guard('sanctum')->id())->orderByDesc('ngay_dang_ky')->get();

        return response()->json(['status' => true, 'data' => $rows]);
    }

    public function dangKy(DangKyLopRequest $request, EnrollmentService $enrollments)
    {
        $row = $enrollments->register(Auth::guard('sanctum')->user(), $request->integer('id_lop_hoc'), $request->string('verification_id')->toString());

        return response()->json(['status' => true, 'message' => 'Đăng ký lớp học thành công.', 'data' => $row]);
    }

    public function huyDangKy(int $id)
    {
        DB::transaction(function () use ($id) {
            $actor = Auth::guard('sanctum')->user();
            HocVien::whereKey($actor->id)->lockForUpdate()->firstOrFail();
            $enrollment = DangKyLop::whereKey($id)->where('id_hoc_vien', $actor->id)->firstOrFail();
            LopHoc::whereKey($enrollment->id_lop_hoc)->lockForUpdate()->firstOrFail();
            if ($enrollment->trang_thai !== 'da_huy') {
                $enrollment->update(['trang_thai' => 'da_huy']);
            }
        }, 5);

        return response()->json(['status' => true, 'message' => 'Đã hủy đăng ký.']);
    }
}
