<?php

namespace App\Http\Controllers\HocVien;

use App\Http\Controllers\Controller;
use App\Http\Requests\DangKyLop\DangKyLopRequest;
use App\Models\DangKyLop;
use App\Models\LopHoc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Controller: Lịch học + Lớp học của tôi (phía Học viên)
 */
class LichHocController extends Controller
{
    /**
     * GET /api/hoc-vien/lich-hoc
     * Query: from_date, to_date, id_mon_hoc
     */
    public function lichHoc(Request $request)
    {
        try {
            $hv = Auth::guard('sanctum')->user();

            $query = DangKyLop::with([
                'lopHoc' => function ($q) {
                    $q->with(['monHoc', 'phongHoc', 'giaoVien']);
                }
            ])
                ->where('id_hoc_vien', $hv->id)
                ->where('trang_thai', '!=', 'da_huy');

            $data = $query->orderBy('ngay_dang_ky', 'desc')->get()
                ->filter(function ($dk) use ($request) {
                    if (!$dk->lopHoc) return false;
                    $lop = $dk->lopHoc;

                    if ($request->filled('from_date') && $lop->thoi_gian_ket_thuc < $request->from_date) {
                        return false;
                    }
                    if ($request->filled('to_date') && $lop->thoi_gian_bat_dau > $request->to_date) {
                        return false;
                    }
                    if ($request->filled('id_mon_hoc') && $lop->id_mon_hoc != $request->id_mon_hoc) {
                        return false;
                    }
                    return true;
                })
                ->values();

            // Gắn thêm trạng thái buổi học so với hiện tại
            $now = now();
            $data->each(function ($dk) use ($now) {
                $lop = $dk->lopHoc;
                if ($lop->thoi_gian_ket_thuc < $now) {
                    $dk->trang_thai_buoi = 'da_hoc';
                } elseif ($lop->thoi_gian_bat_dau <= $now && $lop->thoi_gian_ket_thuc >= $now) {
                    $dk->trang_thai_buoi = 'dang_dien_ra';
                } else {
                    $dk->trang_thai_buoi = 'sap_toi';
                }
            });

            return response()->json([
                'status' => true,
                'message' => 'Lấy lịch học thành công.',
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            Log::error('LichHoc HV error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Đã có lỗi xảy ra: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET /api/hoc-vien/lop-hoc-cua-toi
     * Danh sách các lớp HV đã đăng ký
     */
    public function lopHocCuaToi()
    {
        try {
            $hv = Auth::guard('sanctum')->user();

            $data = DangKyLop::with([
                'lopHoc.monHoc',
                'lopHoc.phongHoc',
                'lopHoc.giaoVien',
            ])
                ->where('id_hoc_vien', $hv->id)
                ->orderBy('ngay_dang_ky', 'desc')
                ->get();

            return response()->json([
                'status' => true,
                'message' => 'Lấy danh sách lớp đã đăng ký thành công.',
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            Log::error('LopHocCuaToi HV error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Đã có lỗi xảy ra: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * POST /api/hoc-vien/dang-ky-lop-hoc
     * Body: id_lop_hoc
     */
    public function dangKy(DangKyLopRequest $request)
    {
        try {
            $hv = Auth::guard('sanctum')->user();
            $lopHoc = LopHoc::find($request->id_lop_hoc);

            if (!$lopHoc) {
                return response()->json([
                    'status' => false,
                    'message' => 'Lớp học không tồn tại.',
                ], 404);
            }

            if (in_array($lopHoc->tinh_trang, ['da_huy', 'da_ket_thuc'])) {
                return response()->json([
                    'status' => false,
                    'message' => 'Lớp học này đã hủy hoặc kết thúc, không thể đăng ký!',
                ], 400);
            }

            // Kiểm tra đã đăng ký chưa
            $existing = DangKyLop::where('id_hoc_vien', $hv->id)
                ->where('id_lop_hoc', $request->id_lop_hoc)
                ->first();
            if ($existing) {
                return response()->json([
                    'status' => false,
                    'message' => 'Bạn đã đăng ký lớp học này rồi!',
                ], 400);
            }

            DB::beginTransaction();
            $dangKy = DangKyLop::create([
                'id_hoc_vien' => $hv->id,
                'id_lop_hoc' => $request->id_lop_hoc,
                'ngay_dang_ky' => now(),
                'trang_thai' => 'cho_thanh_toan',
            ]);
            $dangKy->load(['lopHoc.monHoc', 'lopHoc.giaoVien']);
            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Đăng ký lớp học thành công! Vui lòng thanh toán để hoàn tất.',
                'data' => $dangKy,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Dang ky HV error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Đăng ký thất bại: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * DELETE /api/hoc-vien/huy-dang-ky/{id}
     */
    public function huyDangKy($id)
    {
        try {
            $hv = Auth::guard('sanctum')->user();
            $dangKy = DangKyLop::where('id', $id)
                ->where('id_hoc_vien', $hv->id)
                ->first();

            if (!$dangKy) {
                return response()->json([
                    'status' => false,
                    'message' => 'Không tìm thấy đăng ký.',
                ], 404);
            }

            if ($dangKy->trang_thai === 'da_huy') {
                return response()->json([
                    'status' => false,
                    'message' => 'Đăng ký này đã được hủy trước đó.',
                ], 400);
            }

            $dangKy->trang_thai = 'da_huy';
            $dangKy->save();

            return response()->json([
                'status' => true,
                'message' => 'Đã hủy đăng ký lớp học thành công!',
            ]);
        } catch (\Exception $e) {
            Log::error('Huy dang ky error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Hủy đăng ký thất bại: ' . $e->getMessage(),
            ], 500);
        }
    }
}
