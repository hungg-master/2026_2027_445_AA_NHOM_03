<?php

namespace App\Http\Controllers\GiaoVien;

use App\Http\Controllers\Controller;
use App\Http\Requests\DangKyLop\DuyetHocVienDangKyRequest;
use App\Models\DangKyLop;
use App\Models\HocVien;
use App\Models\LopHoc;
use App\Services\EnrollmentService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DuyetHocVienController extends Controller
{
    public function duyet(DuyetHocVienDangKyRequest $request, int $lopHocId, EnrollmentService $service)
    {
        $result = DB::transaction(function () use ($request, $lopHocId, $service) {
            $actor = Auth::guard('sanctum')->user();
            $row = DangKyLop::whereKey($request->integer('id_dang_ky'))->where('id_lop_hoc', $lopHocId)->firstOrFail();
            $student = HocVien::whereKey($row->id_hoc_vien)->lockForUpdate()->firstOrFail();
            $class = LopHoc::whereKey($lopHocId)->where('id_giao_vien', $actor->id)->lockForUpdate()->firstOrFail();
            $row = DangKyLop::whereKey($row->id)->lockForUpdate()->firstOrFail();
            $approve = $request->input('hanh_dong') === 'duyet';
            if ($approve && ! in_array($row->trang_thai, ['da_xac_nhan', 'da_thanh_toan'])) {
                abort_if($row->trang_thai === 'da_huy', 409, 'Đăng ký đã hủy; học viên cần đăng ký lại.');
                abort_if($student->is_block || $student->tinh_trang != 1 || ! $student->is_active, 403);
                $service->check($student, $class, $row->id);
                $row->update(['trang_thai' => 'da_xac_nhan']);
            } elseif (! $approve && $row->trang_thai !== 'da_huy') {
                $row->update(['trang_thai' => 'da_huy']);
            }

            return $row->load('hocVien');
        }, 5);

        return response()->json(['status' => true, 'data' => $result]);
    }
}
