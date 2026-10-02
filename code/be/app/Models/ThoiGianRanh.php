<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model: Thời gian rảnh (dùng cho thuật toán gợi ý lịch)
 * Bảng: thoi_gian_ranhs
 *
 * Mỗi bản ghi thuộc về hoặc giáo viên, hoặc học viên (loai_nguoi_dung)
 */
class ThoiGianRanh extends Model
{
    use HasFactory;

    protected $table = 'thoi_gian_ranhs';

    protected $fillable = [
        'loai_nguoi_dung',
        'id_giao_vien',
        'id_hoc_vien',
        'ngay_trong_tuan',
        'thoi_gian_bat_dau',
        'thoi_gian_ket_thuc',
        'trang_thai',
    ];

    /**
     * Giáo viên sở hữu khung giờ rảnh (nullable)
     */
    public function giaoVien(): BelongsTo
    {
        return $this->belongsTo(GiaoVien::class, 'id_giao_vien');
    }

    /**
     * Học viên sở hữu khung giờ rảnh (nullable)
     */
    public function hocVien(): BelongsTo
    {
        return $this->belongsTo(HocVien::class, 'id_hoc_vien');
    }
}
