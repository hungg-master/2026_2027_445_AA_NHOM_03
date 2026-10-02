<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChiTietPhongHop extends Model
{
    use HasFactory;

    protected $table = 'chi_tiet_phong_hops';

    protected $fillable = [
        'id_phong_hop',
        'id_nguoi_dung',
        'xac_thuc_khuon_mat',
        'is_vi_pham',
        'is_nguoi_dung',
        'is_active',
        'trang_thai',
        'thoi_gian_tham_gia',
        'thoi_gian_roi',
    ];

    protected $casts = [
        'thoi_gian_tham_gia' => 'datetime',
        'thoi_gian_roi' => 'datetime',
        'xac_thuc_khuon_mat' => 'integer',
        'is_active' => 'integer',
        'trang_thai' => 'integer',
    ];

    public function phongHop()
    {
        return $this->belongsTo(PhongHop::class, 'id_phong_hop');
    }

    public function hocVien()
    {
        return $this->belongsTo(HocVien::class, 'id_nguoi_dung');
    }
}
