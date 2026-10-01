<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model: Môn học
 * Bảng: mon_hocs
 */
class MonHoc extends Model
{
    use HasFactory;

    protected $table = 'mon_hocs';

    protected $fillable = [
        'ten_mon_hoc',
        'mo_ta',
        'lop',
        'tinh_trang',
    ];

    /**
     * Lấy danh sách lớp học thuộc môn này
     */
    public function lopHocs(): HasMany
    {
        return $this->hasMany(LopHoc::class, 'id_mon_hoc');
    }
}
