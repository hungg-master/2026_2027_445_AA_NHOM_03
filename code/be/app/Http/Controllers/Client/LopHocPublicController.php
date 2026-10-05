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
                },
            ])
                ->whereIn('tinh_trang', ['sap_mo', 'dang_mo'])
                ->whereHas('giaoVien', function ($q) {
                    $q->where('trang_thai_duyet', 'da_duyet')->where('is_block', 0)->where('is_active', 1)->where('tinh_trang', 1);
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
            if ($request->filled('keyword')) {
                $kw = trim($request->keyword);
                $query->where(function ($q) use ($kw) {
                    $q->whereHas('monHoc', function ($sub) use ($kw) {
                        $sub->where('ten_mon_hoc', 'like', "%{$kw}%");
                    })->orWhereHas('giaoVien', function ($sub) use ($kw) {
                        $sub->where('ho_ten', 'like', "%{$kw}%");
                    });
                });
            }

            $perPage = max(1, min(100, (int) $request->get('per_page', 12)));
            $data = $query->orderBy('thoi_gian_bat_dau', 'asc')
                ->paginate($perPage);

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
            report($e);
            Log::error('Public lop-hoc error: '.$e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Không thể xử lý yêu cầu lúc này. Vui lòng thử lại.',
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
            $lopHoc = LopHoc::with(['monHoc', 'phongHoc', 'giaoVien:id,ho_ten,chuc_danh,so_nam_kinh_nghiem,mo_ta,hinh_anh'])
                ->whereHas('giaoVien', fn ($q) => $q->where('trang_thai_duyet', 'da_duyet')->where('tinh_trang', 1)->where('is_block', 0)->where('is_active', 1))
                ->where('id', $id)
                ->whereIn('tinh_trang', ['sap_mo', 'dang_mo', 'dang_hoc', 'da_ket_thuc'])
                ->first();

            if (! $lopHoc) {
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
            report($e);
            Log::error('Public show lop-hoc error: '.$e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Không thể xử lý yêu cầu lúc này. Vui lòng thử lại.',
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
            $data = MonHoc::whereIn('tinh_trang', ['hoat_dong', 'active', 1])
                ->orWhereNull('tinh_trang')
                ->orderBy('ten_mon_hoc')
                ->get();

            return response()->json([
                'status' => true,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'status' => false,
                'message' => 'Không thể xử lý yêu cầu lúc này. Vui lòng thử lại.',
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
            report($e);

            return response()->json([
                'status' => false,
                'message' => 'Không thể xử lý yêu cầu lúc này. Vui lòng thử lại.',
            ], 500);
        }
    }
}
