<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model: Phòng học (dành cho lớp Offline)
 * Bảng: phong_hocs
 */
class PhongHoc extends Model
{
    use HasFactory;

    protected $table = 'phong_hocs';

    protected $fillable = [
        'so_phong',
        'dia_chi',
        'mo_ta',
    ];

    /**
     * Lấy danh sách lớp học diễn ra tại phòng này
     */
    public function lopHocs(): HasMany
    {
        return $this->hasMany(LopHoc::class, 'id_phong_hoc');
    }
}
