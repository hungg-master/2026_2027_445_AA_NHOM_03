<?php

namespace App\Http\Controllers;

use App\Models\GiaoVien;
use App\Models\ThoiGianRanh;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ThoiGianRanhController extends Controller
{
    public function getGiaoVienSchedule()
    {
        return $this->read();
    }

    public function getHocVienSchedule()
    {
        return $this->read();
    }

    public function updateGiaoVienSchedule(Request $request)
    {
        return $this->replace($request);
    }

    public function updateHocVienSchedule(Request $request)
    {
        return $this->replace($request);
    }

    private function scope()
    {
        $actor = Auth::guard('sanctum')->user();
        $teacher = $actor instanceof GiaoVien;

        return ThoiGianRanh::where('loai_nguoi_dung', $teacher ? 'giao_vien' : 'hoc_vien')
            ->where($teacher ? 'id_giao_vien' : 'id_hoc_vien', $actor->id);
    }

    private function read()
    {
        return response()->json(['status' => true, 'data' => $this->scope()->orderBy('ngay_trong_tuan')->orderBy('thoi_gian_bat_dau')->get()]);
    }

    private function replace(Request $request)
    {
        $data = $request->validate([
            'schedules' => 'present|array|max:100',
            'schedules.*.ngay_trong_tuan' => 'required|integer|between:0,6',
            'schedules.*.thoi_gian_bat_dau' => ['required', 'regex:/^([01][0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/'],
            'schedules.*.thoi_gian_ket_thuc' => ['required', 'regex:/^([01][0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/'],
        ]);
        foreach ($data['schedules'] as $i => $slot) {
            if (strtotime($slot['thoi_gian_bat_dau']) >= strtotime($slot['thoi_gian_ket_thuc'])) {
                throw ValidationException::withMessages(["schedules.$i.thoi_gian_ket_thuc" => 'Thời gian kết thúc phải sau bắt đầu.']);
            }
        }
        $actor = Auth::guard('sanctum')->user();
        $teacher = $actor instanceof GiaoVien;
        DB::transaction(function () use ($actor, $teacher, $data) {
            $actor->newQuery()->whereKey($actor->id)->lockForUpdate()->firstOrFail();
            $this->scope()->delete();
            foreach ($data['schedules'] as $slot) {
                ThoiGianRanh::create([
                    ...$slot, 'loai_nguoi_dung' => $teacher ? 'giao_vien' : 'hoc_vien',
                    'id_giao_vien' => $teacher ? $actor->id : null,
                    'id_hoc_vien' => $teacher ? null : $actor->id, 'trang_thai' => 'hoat_dong',
                ]);
            }
        }, 5);

        return response()->json(['status' => true, 'message' => 'Đã lưu lịch rảnh.']);
    }
}
