<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LopHoc extends Model
{
    protected $table = 'lop_hocs';

    protected $fillable = [
        'id_giao_vien',
        'id_mon_hoc',
        'id_phong_hoc',
        'loai_lop',
        'hinh_thuc',
        'hoc_phi',
        'si_so_toi_da',
        'thoi_gian_bat_dau',
        'thoi_gian_ket_thuc',
        'tinh_trang',
    ];

    public function giaoVien()
    {
        return $this->belongsTo(GiaoVien::class, 'id_giao_vien');
    }

    public function monHoc()
    {
        return $this->belongsTo(MonHoc::class, 'id_mon_hoc');
    }

    public function phongHoc()
    {
        return $this->belongsTo(PhongHoc::class, 'id_phong_hoc');
    }
}
