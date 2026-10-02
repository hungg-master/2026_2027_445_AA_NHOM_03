<?php

namespace App\Http\Controllers;

use App\Models\ThoiGianRanh;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ThoiGianRanhController extends Controller
{
    public function getGiaoVienSchedule(Request $request)
    {
        // Middleware đã xác thực, lấy user từ request
        $giaoVien = Auth::guard('sanctum')->user();
        if (!$giaoVien) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 401);
        }

        $schedules = ThoiGianRanh::where('id_giao_vien', $giaoVien->id)
            ->where('loai_nguoi_dung', 'giao_vien')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $schedules
        ]);
    }

    public function updateGiaoVienSchedule(Request $request)
    {
        $giaoVien = Auth::guard('sanctum')->user();
        if (!$giaoVien) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 401);
        }

        $request->validate([
            'schedules' => 'present|array',
            'schedules.*.ngay_trong_tuan' => 'required|integer|min:0|max:6',
            'schedules.*.thoi_gian_bat_dau' => 'required',
            'schedules.*.thoi_gian_ket_thuc' => 'required',
        ]);

        ThoiGianRanh::where('id_giao_vien', $giaoVien->id)
            ->where('loai_nguoi_dung', 'giao_vien')
            ->delete();

        $insertData = [];
        foreach ($request->schedules as $schedule) {
            $insertData[] = [
                'id_giao_vien'       => $giaoVien->id,
                'id_hoc_vien'        => null,
                'loai_nguoi_dung'    => 'giao_vien',
                'ngay_trong_tuan'    => $schedule['ngay_trong_tuan'],
                'thoi_gian_bat_dau'  => $schedule['thoi_gian_bat_dau'],
                'thoi_gian_ket_thuc' => $schedule['thoi_gian_ket_thuc'],
                'trang_thai'         => 'active',
                'created_at'         => now(),
                'updated_at'         => now(),
            ];
        }

        if (!empty($insertData)) {
            ThoiGianRanh::insert($insertData);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Cập nhật lịch rảnh thành công'
        ]);
    }

    public function getHocVienSchedule(Request $request)
    {
        $hocVien = Auth::guard('hoc_vien')->user();

        // Fallback: thử lấy từ sanctum guard nếu hoc_vien guard không nhận
        if (!$hocVien) {
            $sanctumUser = Auth::guard('sanctum')->user();
            if ($sanctumUser instanceof \App\Models\HocVien) {
                $hocVien = $sanctumUser;
            }
        }

        if (!$hocVien) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 401);
        }

        $schedules = ThoiGianRanh::where('id_hoc_vien', $hocVien->id)
            ->where('loai_nguoi_dung', 'hoc_vien')
            ->get();

        return response()->json([
            'status' => true,
            'data'   => $schedules
        ]);
    }

    public function updateHocVienSchedule(Request $request)
    {
        $hocVien = Auth::guard('hoc_vien')->user();

        if (!$hocVien) {
            $sanctumUser = Auth::guard('sanctum')->user();
            if ($sanctumUser instanceof \App\Models\HocVien) {
                $hocVien = $sanctumUser;
            }
        }

        if (!$hocVien) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 401);
        }

        $request->validate([
            'schedules' => 'present|array',
            'schedules.*.ngay_trong_tuan' => 'required|integer|min:0|max:6',
            'schedules.*.thoi_gian_bat_dau' => 'required',
            'schedules.*.thoi_gian_ket_thuc' => 'required',
        ]);

        ThoiGianRanh::where('id_hoc_vien', $hocVien->id)
            ->where('loai_nguoi_dung', 'hoc_vien')
            ->delete();

        $insertData = [];
        foreach ($request->schedules as $schedule) {
            $insertData[] = [
                'id_giao_vien'       => null,
                'id_hoc_vien'        => $hocVien->id,
                'loai_nguoi_dung'    => 'hoc_vien',
                'ngay_trong_tuan'    => $schedule['ngay_trong_tuan'],
                'thoi_gian_bat_dau'  => $schedule['thoi_gian_bat_dau'],
                'thoi_gian_ket_thuc' => $schedule['thoi_gian_ket_thuc'],
                'trang_thai'         => 'active',
                'created_at'         => now(),
                'updated_at'         => now(),
            ];
        }

        if (!empty($insertData)) {
            ThoiGianRanh::insert($insertData);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Cập nhật lịch rảnh thành công'
        ]);
    }
}
