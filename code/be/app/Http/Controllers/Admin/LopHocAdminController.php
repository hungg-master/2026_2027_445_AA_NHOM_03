<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\LopHoc\DuyetLopHocRequest;
use App\Models\LopHoc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Controller: Quản lý lớp học (phía Admin)
 */
class LopHocAdminController extends Controller
{
    /**
     * GET /api/admin/lop-hoc
     * Lấy tất cả lớp học trong hệ thống
     */
    public function index(Request $request)
    {
        try {
            $query = LopHoc::with(['monHoc', 'phongHoc', 'giaoVien']);

            if ($request->filled('tinh_trang')) {
                $query->where('tinh_trang', $request->tinh_trang);
            }
            if ($request->filled('id_giao_vien')) {
                $query->where('id_giao_vien', $request->id_giao_vien);
            }
            if ($request->filled('keyword')) {
                $kw = $request->keyword;
                $query->whereHas('monHoc', function ($q) use ($kw) {
                    $q->where('ten_mon_hoc', 'LIKE', "%$kw%");
                })->orWhereHas('giaoVien', function ($q) use ($kw) {
                    $q->where('ho_ten', 'LIKE', "%$kw%");
                });
            }

            $data = $query->orderBy('created_at', 'desc')
                ->paginate($request->get('per_page', 20));

            $data->getCollection()->each(function ($lop) {
                $lop->so_hoc_vien_hien_tai = $lop->so_hoc_vien_hien_tai;
            });

            return response()->json([
                'status' => true,
                'message' => 'Lấy danh sách lớp học thành công.',
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            Log::error('Admin lop-hoc index error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Đã có lỗi xảy ra: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET /api/admin/lop-hoc/{id}
     */
    public function show($id)
    {
        try {
            $lopHoc = LopHoc::with(['monHoc', 'phongHoc', 'giaoVien', 'dangKyLops.hocVien'])
                ->find($id);

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
            Log::error('Admin lop-hoc show error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Đã có lỗi xảy ra: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * POST /api/admin/lop-hoc/duyet
     * Body: id, tinh_trang
     */
    public function duyet(DuyetLopHocRequest $request)
    {
        try {
            $admin = Auth::guard('sanctum')->user();
            $lopHoc = LopHoc::find($request->id);

            if (!$lopHoc) {
                return response()->json([
                    'status' => false,
                    'message' => 'Không tìm thấy lớp học.',
                ], 404);
            }

            $lopHoc->tinh_trang = $request->tinh_trang;
            $lopHoc->save();

            $msg = $request->tinh_trang === 'da_huy'
                ? 'Đã hủy lớp học thành công!'
                : 'Đã cập nhật trạng thái lớp học!';

            return response()->json([
                'status' => true,
                'message' => $msg,
                'data' => $lopHoc,
            ]);
        } catch (\Exception $e) {
            Log::error('Admin duyet lop-hoc error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Cập nhật thất bại: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET /api/admin/thong-ke
     * Thống kê tổng quan
     */
    public function thongKe()
    {
        try {
            $stats = [
                'tong_lop' => LopHoc::count(),
                'lop_sap_mo' => LopHoc::where('tinh_trang', 'sap_mo')->count(),
                'lop_dang_mo' => LopHoc::where('tinh_trang', 'dang_mo')->count(),
                'lop_dang_hoc' => LopHoc::where('tinh_trang', 'dang_hoc')->count(),
                'lop_da_ket_thuc' => LopHoc::where('tinh_trang', 'da_ket_thuc')->count(),
                'lop_da_huy' => LopHoc::where('tinh_trang', 'da_huy')->count(),
                'lop_online' => LopHoc::where('hinh_thuc', 'online')->count(),
                'lop_offline' => LopHoc::where('hinh_thuc', 'offline')->count(),
            ];

            return response()->json([
                'status' => true,
                'message' => 'Lấy thống kê thành công.',
                'data' => $stats,
            ]);
        } catch (\Exception $e) {
            Log::error('Admin thong-ke error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Đã có lỗi xảy ra: ' . $e->getMessage(),
            ], 500);
        }
    }
}
