<?php

namespace App\Http\Controllers\GiaoVien;

use App\Http\Controllers\Controller;
use App\Http\Requests\DangKyLop\DuyetHocVienDangKyRequest;
use App\Models\DangKyLop;
use App\Models\LopHoc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Controller: Duyệt học viên đăng ký (phía Giáo viên)
 */
class DuyetHocVienController extends Controller
{
    /**
     * POST /api/giao-vien/lop-hoc/{id}/duyet-hoc-vien
     * Body: id_dang_ky, hanh_dong (duyet|tu_choi|huy)
     */
    public function duyet(DuyetHocVienDangKyRequest $request, $lopHocId)
    {
        try {
            $gv = Auth::guard('sanctum')->user();

            // Kiểm tra lớp học có thuộc GV này không
            $lopHoc = LopHoc::where('id_giao_vien', $gv->id)
                ->where('id', $lopHocId)
                ->first();

            if (!$lopHoc) {
                return response()->json([
                    'status' => false,
                    'message' => 'Không tìm thấy lớp học hoặc bạn không có quyền.',
                ], 404);
            }

            $dangKy = DangKyLop::where('id', $request->id_dang_ky)
                ->where('id_lop_hoc', $lopHocId)
                ->first();

            if (!$dangKy) {
                return response()->json([
                    'status' => false,
                    'message' => 'Không tìm thấy đăng ký của lớp này.',
                ], 404);
            }

            // Mapping hành động -> trạng thái
            $map = [
                'duyet' => 'da_xac_nhan',
                'tu_choi' => 'da_huy',
                'huy' => 'da_huy',
            ];

            $trangThaiMoi = $map[$request->hanh_dong];

            // Nếu duyệt thì kiểm tra sĩ số tối đa
            if ($trangThaiMoi === 'da_xac_nhan') {
                $soHVDaDuyet = $lopHoc->dangKyLops()
                    ->whereIn('trang_thai', ['da_xac_nhan', 'da_thanh_toan'])
                    ->count();
                if ($soHVDaDuyet >= $lopHoc->si_so_toi_da) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Lớp đã đủ sĩ số tối đa!',
                    ], 400);
                }
            }

            $dangKy->trang_thai = $trangThaiMoi;
            $dangKy->save();
            $dangKy->load('hocVien');

            return response()->json([
                'status' => true,
                'message' => $trangThaiMoi === 'da_xac_nhan'
                    ? 'Đã duyệt học viên vào lớp!'
                    : 'Đã cập nhật trạng thái đăng ký.',
                'data' => $dangKy,
            ]);
        } catch (\Exception $e) {
            Log::error('Duyet HV error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Duyệt thất bại: ' . $e->getMessage(),
            ], 500);
        }
    }
}
