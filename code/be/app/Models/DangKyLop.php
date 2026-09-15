<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model: Đăng ký lớp học
 * Bảng: dang_ky_lops
 *
 * Mỗi học viên có thể đăng ký nhiều lớp, mỗi lớp có nhiều học viên.
 * Trạng thái:
 *  - cho_thanh_toan: Vừa đăng ký, chờ thanh toán
 *  - da_thanh_toan: Đã thanh toán học phí
 *  - da_xac_nhan: Đã được giáo viên duyệt (cuối cùng)
 *  - da_huy: Học viên hoặc admin hủy
 */
class DangKyLop extends Model
{
    use HasFactory;

    protected $table = 'dang_ky_lops';

    protected $fillable = [
        'id_lop_hoc',
        'id_hoc_vien',
        'ngay_dang_ky',
        'trang_thai',
    ];

    protected $casts = [
        'ngay_dang_ky' => 'datetime',
    ];

    /**
     * Lớp học mà học viên đăng ký
     */
    public function lopHoc(): BelongsTo
    {
        return $this->belongsTo(LopHoc::class, 'id_lop_hoc');
    }

    /**
     * Học viên đăng ký
     */
    public function hocVien(): BelongsTo
    {
        return $this->belongsTo(HocVien::class, 'id_hoc_vien');
    }
}
