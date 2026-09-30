<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model: Lớp học
 * Bảng: lop_hocs
 *
 * Quan hệ:
 * - belongsTo GiaoVien (id_giao_vien)
 * - belongsTo MonHoc (id_mon_hoc)
 * - belongsTo PhongHoc (id_phong_hoc, nullable khi online)
 * - hasMany DangKyLop (id_lop_hoc)
 */
class LopHoc extends Model
{
    use HasFactory;

    protected $table = 'lop_hocs';

    protected $fillable = [
        'id_giao_vien',
        'id_mon_hoc',
        'id_phong_hoc',
        'loai_lop',
        'hinh_thuc',
        'link_online',
        'hoc_phi',
        'si_so_toi_da',
        'thoi_gian_bat_dau',
        'thoi_gian_ket_thuc',
        'tinh_trang',
    ];

    protected $casts = [
        'hoc_phi' => 'decimal:2',
        'si_so_toi_da' => 'integer',
        'thoi_gian_bat_dau' => 'datetime',
        'thoi_gian_ket_thuc' => 'datetime',
    ];

    /**
     * Giáo viên phụ trách lớp
     */
    public function giaoVien(): BelongsTo
    {
        return $this->belongsTo(GiaoVien::class, 'id_giao_vien');
    }

    /**
     * Môn học của lớp
     */
    public function monHoc(): BelongsTo
    {
        return $this->belongsTo(MonHoc::class, 'id_mon_hoc');
    }

    /**
     * Phòng học (nullable khi lớp online)
     */
    public function phongHoc(): BelongsTo
    {
        return $this->belongsTo(PhongHoc::class, 'id_phong_hoc');
    }

    /**
     * Danh sách đăng ký của lớp
     */
    public function dangKyLops(): HasMany
    {
        return $this->hasMany(DangKyLop::class, 'id_lop_hoc');
    }

    /**
     * Đếm số học viên đã đăng ký và được duyệt
     */
    public function getSoHocVienHienTaiAttribute(): int
    {
        return $this->dangKyLops()
            ->whereIn('trang_thai', ['da_thanh_toan', 'da_xac_nhan'])
            ->count();
    }
}
