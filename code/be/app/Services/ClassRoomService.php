<?php

namespace App\Services;

use App\Models\LopHoc;
use App\Models\PhongHop;
use Illuminate\Support\Str;

class ClassRoomService
{
    public function sync(LopHoc $class): void
    {
        if ($class->hinh_thuc !== 'online') {
            $class->phongHop?->delete();

            return;
        }
        $room = $class->phongHop()->first() ?? new PhongHop(['id_lop_hoc' => $class->id, 'ma_phong' => 'ST-'.Str::uuid()]);
        $room->fill([
            'ten_phong' => $class->monHoc->ten_mon_hoc, 'id_chu_phong' => $class->id_giao_vien,
            'so_nguoi_toi_da' => $class->si_so_toi_da + 1,
            'thoi_gian_bat_dau' => $class->thoi_gian_bat_dau, 'thoi_gian_ket_thuc' => $class->thoi_gian_ket_thuc,
            'trang_thai' => in_array($class->tinh_trang, ['da_huy', 'da_ket_thuc']) ? 0 : 1,
        ])->save();
        if (! $class->link_online) {
            $class->update(['link_online' => '/phong-hoc/'.$room->ma_phong]);
        }
    }
}
