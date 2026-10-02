<?php

namespace App\Http\Controllers;

use App\Models\ChiTietPhongHop;
use App\Models\PhongHop;
use App\Models\LopHoc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PhongHopController extends Controller
{
    /**
     * Tạo mã phòng ngẫu nhiên định dạng XXX-XXX-XXX
     */
    private function generateRoomCode()
    {
        do {
            $code = sprintf("%03d-%03d-%03d", mt_rand(100, 999), mt_rand(100, 999), mt_rand(100, 999));
        } while (PhongHop::where('ma_phong', $code)->exists());

        return $code;
    }

    /**
     * POST /api/phong-hop/create
     * Tạo phòng họp mới
     */
    public function create(Request $request)
    {
        $request->validate([
            'ten_phong' => 'required|string|max:255',
            'id_chu_phong' => 'nullable',
            'so_nguoi_toi_da' => 'nullable|integer',
            'email_khach_moi' => 'nullable|string',
            'id_lop_hoc' => 'nullable|integer',
        ]);

        try {
            $maPhong = $this->generateRoomCode();

            $phongHop = PhongHop::create([
                'ma_phong' => $maPhong,
                'ten_phong' => $request->ten_phong,
                'id_chu_phong' => $request->id_chu_phong,
                'id_lop_hoc' => $request->id_lop_hoc,
                'so_nguoi_toi_da' => $request->so_nguoi_toi_da ?? 100,
                'mo_ta' => $request->mo_ta,
                'email_khach_moi' => $request->email_khach_moi,
                'thoi_gian_bat_dau' => now(),
                'trang_thai' => 1,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Khởi tạo phòng họp thành công!',
                'data' => $phongHop,
            ], 201);
        } catch (\Exception $e) {
            Log::error('Loi tao phong hop: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Không thể tạo phòng họp: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * POST /api/phong-hop/kiem-tra-phong-hop
     * Kiểm tra phòng họp có tồn tại và đang hoạt động không
     */
    public function kiemTraPhongHop(Request $request)
    {
        $maPhong = trim($request->input('ma_phong', ''));

        if (!$maPhong) {
            return response()->json([
                'status' => false,
                'message' => 'Vui lòng cung cấp mã phòng họp!',
            ], 400);
        }

        $phong = PhongHop::where('ma_phong', $maPhong)->first();

        // Nếu chưa có, kiểm tra xem có phải mã phòng gắn với lớp học không (ví dụ EDU-101, PH-1)
        if (!$phong) {
            // Tự động tìm lớp học hoặc tạo phòng sẵn cho mã phòng này
            if (preg_match('/(?:EDU|PH|ROOM)-(\d+)/i', $maPhong, $matches)) {
                $lopId = $matches[1];
                $lop = LopHoc::find($lopId);
                $tenPhong = $lop ? "Phòng học: " . ($lop->monHoc?->ten_mon_hoc ?? "Lớp $lopId") : "Phòng học $maPhong";
                $phong = PhongHop::create([
                    'ma_phong' => $maPhong,
                    'ten_phong' => $tenPhong,
                    'id_lop_hoc' => $lop ? $lop->id : null,
                    'id_chu_phong' => $lop ? $lop->id_giao_vien : null,
                    'thoi_gian_bat_dau' => now(),
                    'trang_thai' => 1,
                ]);
            }
        }

        if (!$phong) {
            return response()->json([
                'status' => false,
                'message' => 'Mã phòng không tồn tại hoặc đã kết thúc!',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Mã phòng họp hợp lệ.',
            'data' => $phong,
        ]);
    }

    /**
     * POST /api/phong-hop/tao-token
     * Sinh token truy cập phòng video LiveKit / WebRTC
     */
    public function taoToken(Request $request)
    {
        $maPhong = trim($request->input('ma_phong', ''));
        $userName = trim($request->input('user_name', 'Học viên'));

        if (!$maPhong) {
            return response()->json([
                'status' => false,
                'message' => 'Mã phòng không được để trống!',
            ], 400);
        }

        $phong = PhongHop::where('ma_phong', $maPhong)->first();
        if (!$phong) {
            $phong = PhongHop::create([
                'ma_phong' => $maPhong,
                'ten_phong' => "Lớp học: $maPhong",
                'thoi_gian_bat_dau' => now(),
                'trang_thai' => 1,
            ]);
        }

        // Tạo token phiên bảo mật
        $token = 'edulink_jwt_' . bin2hex(random_bytes(24)) . '_' . time();

        return response()->json([
            'status' => true,
            'token' => $token,
            'id_phong_hop' => $phong->id,
            'ma_phong' => $phong->ma_phong,
            'ten_phong' => $phong->ten_phong,
        ]);
    }

    /**
     * GET /api/phong-hop/ma-phong
     */
    public function maPhong(Request $request)
    {
        $maPhong = trim($request->input('ma_phong', ''));
        $phong = PhongHop::where('ma_phong', $maPhong)->first();

        if (!$phong) {
            return response()->json(['status' => false, 'message' => 'Không tìm thấy phòng'], 404);
        }

        return response()->json(['status' => true, 'data' => $phong]);
    }

    /**
     * GET /api/phong-hop/by-ma-phong/{maPhong}
     */
    public function getByMaPhong($maPhong)
    {
        $phong = PhongHop::where('ma_phong', $maPhong)->first();
        if ($phong) {
            return response()->json([
                'status' => true,
                'data' => $phong
            ]);
        }
        return response()->json([
            'status' => false,
            'message' => 'Không tìm thấy phòng họp với mã: ' . $maPhong
        ], 404);
    }

    /**
     * POST /api/phong-hop/roi-phong
     */
    public function roiPhong(Request $request)
    {
        $idNguoiDung = $request->input('id_nguoi_dung');
        $idPhongHop = $request->input('id_phong_hop');

        if ($idNguoiDung && $idPhongHop) {
            ChiTietPhongHop::where('id_nguoi_dung', $idNguoiDung)
                ->where('id_phong_hop', $idPhongHop)
                ->where('is_active', 1)
                ->update([
                    'is_active' => 0,
                    'thoi_gian_roi' => now(),
                ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Đã lưu lịch sử rời phòng.',
        ]);
    }

    /**
     * GET /api/chi-tiet-phong-hop/data
     */
    public function chiTietPhongHopData(Request $request)
    {
        $idNguoiDung = $request->input('id_nguoi_dung');

        // Lấy danh sách các phòng họp gắn với lịch học hoặc phòng đã tham gia
        $data = PhongHop::where('trang_thai', 1)
            ->orderBy('thoi_gian_bat_dau', 'asc')
            ->limit(10)
            ->get();

        return response()->json([
            'status' => true,
            'data' => $data,
        ]);
    }

    /**
     * POST /api/chi-tiet-phong-hop/create
     * Lưu chi tiết điểm danh / tham gia phòng
     */
    public function chiTietPhongHopCreate(Request $request)
    {
        $record = ChiTietPhongHop::create([
            'id_phong_hop' => $request->id_phong_hop,
            'id_nguoi_dung' => $request->id_nguoi_dung,
            'xac_thuc_khuon_mat' => $request->xac_thuc_khuon_mat ?? 1,
            'is_vi_pham' => $request->is_vi_pham ?? 0,
            'is_nguoi_dung' => $request->is_nguoi_dung ?? 1,
            'is_active' => $request->is_active ?? 1,
            'trang_thai' => $request->trang_thai ?? 1,
            'thoi_gian_tham_gia' => now(),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Đã ghi nhận điểm danh phòng học.',
            'data' => $record,
        ]);
    }

    /**
     * GET /api/phong-hop/lich-su-tham-gia
     */
    public function lichSuThamGia(Request $request)
    {
        $idNguoiDung = $request->input('id_nguoi_dung');

        $query = ChiTietPhongHop::with('phongHop')
            ->where('id_nguoi_dung', $idNguoiDung)
            ->orderBy('created_at', 'desc')
            ->limit(15);

        $list = $query->get()->map(function ($item) {
            $durationMinutes = 0;
            if ($item->thoi_gian_tham_gia && $item->thoi_gian_roi) {
                $durationMinutes = round($item->thoi_gian_roi->diffInMinutes($item->thoi_gian_tham_gia));
            }
            return [
                'id' => $item->id,
                'ten_phong' => $item->phongHop?->ten_phong ?? 'Phòng học trực tuyến',
                'chu_phong' => 'Giảng viên EduLink',
                'thoi_gian_bat_dau' => $item->thoi_gian_tham_gia ?? $item->created_at,
                'thoi_luong' => $durationMinutes > 0 ? "{$durationMinutes} phút" : "Đang tham gia",
                'vai_tro' => $item->is_nguoi_dung ? 'Học viên' : 'Thành viên',
            ];
        });

        return response()->json([
            'status' => true,
            'data' => $list,
        ]);
    }

    /**
     * GET /api/nguoi-dung/phong-hop-lien-quan
     */
    public function phongHopLienQuan(Request $request)
    {
        $data = PhongHop::where('trang_thai', 1)
            ->orderBy('thoi_gian_bat_dau', 'asc')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $data,
        ]);
    }
}
