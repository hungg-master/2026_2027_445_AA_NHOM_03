<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhongHop extends Model
{
    use HasFactory;

    protected $table = 'phong_hops';

    protected $fillable = [
        'ma_phong',
        'ten_phong',
        'id_chu_phong',
        'id_lop_hoc',
        'so_nguoi_toi_da',
        'mo_ta',
        'email_khach_moi',
        'thoi_gian_bat_dau',
        'thoi_gian_ket_thuc',
        'trang_thai',
    ];

    protected $casts = [
        'thoi_gian_bat_dau' => 'datetime',
        'thoi_gian_ket_thuc' => 'datetime',
        'trang_thai' => 'integer',
        'so_nguoi_toi_da' => 'integer',
    ];

    public function chuPhong()
    {
        return $this->belongsTo(GiaoVien::class, 'id_chu_phong');
    }

    public function lopHoc()
    {
        return $this->belongsTo(LopHoc::class, 'id_lop_hoc');
    }

    public function chiTiet()
    {
        return $this->hasMany(ChiTietPhongHop::class, 'id_phong_hop');
    }
}
