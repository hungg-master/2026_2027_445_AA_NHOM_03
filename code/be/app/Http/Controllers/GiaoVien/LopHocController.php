<?php

namespace App\Http\Controllers\GiaoVien;

use App\Http\Controllers\Controller;
use App\Http\Requests\LopHoc\TaoLopHocRequest;
use App\Models\LopHoc;
use App\Models\MonHoc;
use App\Models\PhongHoc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Controller: Quản lý Lớp học (phía Giáo viên)
 *
 * Bao gồm:
 * - lichDay(): Lấy lịch dạy của GV hiện tại theo khoảng ngày
 * - index(): Danh sách lớp GV đang dạy
 * - store(): Tạo lớp mới
 * - show(): Chi tiết 1 lớp + DS học viên
 * - update(): Cập nhật lớp
 * - destroy(): Hủy lớp (chuyển tinh_trang = da_huy)
 */
class LopHocController extends Controller
{
    /**
     * GET /api/giao-vien/lich-day
     * Query: from_date, to_date, tinh_trang, id_mon_hoc
     */
    public function lichDay(Request $request)
    {
        try {
            $gv = Auth::guard('sanctum')->user();

            $query = LopHoc::with(['monHoc', 'phongHoc'])
                ->where('id_giao_vien', $gv->id);

            // Filter theo khoảng ngày
            if ($request->filled('from_date')) {
                $query->where('thoi_gian_ket_thuc', '>=', $request->from_date);
            }
            if ($request->filled('to_date')) {
                $query->where('thoi_gian_bat_dau', '<=', $request->to_date);
            }

            // Filter theo trạng thái
            if ($request->filled('tinh_trang')) {
                $query->where('tinh_trang', $request->tinh_trang);
            }

            // Filter theo môn học
            if ($request->filled('id_mon_hoc')) {
                $query->where('id_mon_hoc', $request->id_mon_hoc);
            }

            $data = $query->orderBy('thoi_gian_bat_dau', 'asc')->get();

            // Thêm sĩ số hiện tại cho mỗi lớp
            $data->each(function ($lop) {
                $lop->so_hoc_vien_hien_tai = $lop->so_hoc_vien_hien_tai;
            });

            return response()->json([
                'status' => true,
                'message' => 'Lấy lịch dạy thành công.',
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            Log::error('LichDay error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Đã có lỗi xảy ra: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET /api/giao-vien/lop-hoc
     * Danh sách tất cả lớp của GV
     */
    public function index()
    {
        try {
            $gv = Auth::guard('sanctum')->user();

            $data = LopHoc::with(['monHoc', 'phongHoc'])
                ->where('id_giao_vien', $gv->id)
                ->orderBy('thoi_gian_bat_dau', 'desc')
                ->get();

            $data->each(function ($lop) {
                $lop->so_hoc_vien_hien_tai = $lop->so_hoc_vien_hien_tai;
            });

            return response()->json([
                'status' => true,
                'message' => 'Lấy danh sách lớp học thành công.',
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            Log::error('GiaoVien lop-hoc index error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Đã có lỗi xảy ra: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * POST /api/giao-vien/lop-hoc
     */
    public function store(TaoLopHocRequest $request)
    {
        try {
            $gv = Auth::guard('sanctum')->user();
            $data = $request->validated();
            $data['id_giao_vien'] = $gv->id;
            $data['tinh_trang'] = $data['tinh_trang'] ?? 'sap_mo';

            $lopHoc = LopHoc::create($data);

            // Load quan hệ để trả về đầy đủ
            $lopHoc->load(['monHoc', 'phongHoc']);

            return response()->json([
                'status' => true,
                'message' => 'Tạo lớp học thành công!',
                'data' => $lopHoc,
            ]);
        } catch (\Exception $e) {
            Log::error('GiaoVien tao lop-hoc error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Tạo lớp thất bại: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET /api/giao-vien/lop-hoc/{id}
     * Chi tiết 1 lớp + danh sách học viên
     */
    public function show($id)
    {
        try {
            $gv = Auth::guard('sanctum')->user();

            $lopHoc = LopHoc::with(['monHoc', 'phongHoc', 'dangKyLops.hocVien'])
                ->where('id_giao_vien', $gv->id)
                ->where('id', $id)
                ->first();

            if (!$lopHoc) {
                return response()->json([
                    'status' => false,
                    'message' => 'Không tìm thấy lớp học hoặc bạn không có quyền truy cập.',
                ], 404);
            }

            $lopHoc->so_hoc_vien_hien_tai = $lopHoc->so_hoc_vien_hien_tai;

            return response()->json([
                'status' => true,
                'message' => 'Lấy chi tiết lớp học thành công.',
                'data' => $lopHoc,
            ]);
        } catch (\Exception $e) {
            Log::error('GiaoVien show lop-hoc error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Đã có lỗi xảy ra: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * PUT /api/giao-vien/lop-hoc/{id}
     */
    public function update(TaoLopHocRequest $request, $id)
    {
        try {
            $gv = Auth::guard('sanctum')->user();

            $lopHoc = LopHoc::where('id_giao_vien', $gv->id)
                ->where('id', $id)
                ->first();

            if (!$lopHoc) {
                return response()->json([
                    'status' => false,
                    'message' => 'Không tìm thấy lớp học hoặc bạn không có quyền.',
                ], 404);
            }

            $lopHoc->update($request->validated());
            $lopHoc->load(['monHoc', 'phongHoc']);

            return response()->json([
                'status' => true,
                'message' => 'Cập nhật lớp học thành công!',
                'data' => $lopHoc,
            ]);
        } catch (\Exception $e) {
            Log::error('GiaoVien update lop-hoc error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Cập nhật thất bại: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * DELETE /api/giao-vien/lop-hoc/{id}
     * Chuyển trạng thái thành 'da_huy' (soft cancel)
     */
    public function destroy($id)
    {
        try {
            $gv = Auth::guard('sanctum')->user();

            $lopHoc = LopHoc::where('id_giao_vien', $gv->id)
                ->where('id', $id)
                ->first();

            if (!$lopHoc) {
                return response()->json([
                    'status' => false,
                    'message' => 'Không tìm thấy lớp học hoặc bạn không có quyền.',
                ], 404);
            }

            // Chuyển sang trạng thái hủy + hủy tất cả đăng ký
            DB::beginTransaction();
            $lopHoc->tinh_trang = 'da_huy';
            $lopHoc->save();

            // Hủy các đăng ký của lớp này
            $lopHoc->dangKyLops()->where('trang_thai', '!=', 'da_huy')
                ->update(['trang_thai' => 'da_huy']);
            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Đã hủy lớp học thành công!',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('GiaoVien destroy lop-hoc error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Hủy lớp thất bại: ' . $e->getMessage(),
            ], 500);
        }
    }
}
