<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\LopHoc;
use App\Models\MonHoc;
use App\Models\PhongHoc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Controller: Public API cho trang chủ (không cần đăng nhập)
 *
 * Dùng cho học viên duyệt lớp học đang mở để đăng ký.
 */
class LopHocPublicController extends Controller
{
    /**
     * GET /api/client/lop-hoc/data
     * Query: loai_lop, hinh_thuc, id_mon_hoc, keyword
     */
    public function index(Request $request)
    {
        try {
            $query = LopHoc::with([
                'monHoc',
                'phongHoc',
                'giaoVien' => function ($q) {
                    $q->select('id', 'ho_ten', 'chuc_danh', 'so_nam_kinh_nghiem', 'hinh_anh');
                }
            ])
                ->whereIn('tinh_trang', ['sap_mo', 'dang_mo'])
                ->whereHas('giaoVien', function ($q) {
                    $q->where('trang_thai_duyet', 'da_duyet');
                });

            if ($request->filled('loai_lop')) {
                $query->where('loai_lop', $request->loai_lop);
            }
            if ($request->filled('hinh_thuc')) {
                $query->where('hinh_thuc', $request->hinh_thuc);
            }
            if ($request->filled('id_mon_hoc')) {
                $query->where('id_mon_hoc', $request->id_mon_hoc);
            }

            $data = $query->orderBy('thoi_gian_bat_dau', 'asc')
                ->paginate($request->get('per_page', 12));

            // Thêm sĩ số
            $data->getCollection()->each(function ($lop) {
                $lop->so_hoc_vien_hien_tai = $lop->so_hoc_vien_hien_tai;
            });

            return response()->json([
                'status' => true,
                'message' => 'Lấy danh sách lớp học công khai thành công.',
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            Log::error('Public lop-hoc error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Đã có lỗi xảy ra: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET /api/client/lop-hoc/{id}
     * Chi tiết lớp học public
     */
    public function show($id)
    {
        try {
            $lopHoc = LopHoc::with(['monHoc', 'phongHoc', 'giaoVien'])
                ->where('id', $id)
                ->whereIn('tinh_trang', ['sap_mo', 'dang_mo', 'dang_hoc', 'da_ket_thuc'])
                ->first();

            if (!$lopHoc) {
                return response()->json([
                    'status' => false,
                    'message' => 'Không tìm thấy lớp học.',
                ], 404);
            }

            $lopHoc->so_hoc_vien_hien_tai = $lopHoc->so_hoc_vien_hien_tai;

            return response()->json([
                'status' => true,
                'message' => 'Lấy chi tiết lớp học thành công.',
                'data' => $lopHoc,
            ]);
        } catch (\Exception $e) {
            Log::error('Public show lop-hoc error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Đã có lỗi xảy ra: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET /api/client/mon-hoc
     * Danh sách môn học (dropdown filter)
     */
    public function monHoc()
    {
        try {
            $data = MonHoc::where('tinh_trang', 'hoat_dong')
                ->orWhereNull('tinh_trang')
                ->orderBy('ten_mon_hoc')
                ->get();

            return response()->json([
                'status' => true,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Đã có lỗi xảy ra: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET /api/client/phong-hoc
     * Danh sách phòng học
     */
    public function phongHoc()
    {
        try {
            $data = PhongHoc::orderBy('so_phong')->get();

            return response()->json([
                'status' => true,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Đã có lỗi xảy ra: ' . $e->getMessage(),
            ], 500);
        }
    }
}
