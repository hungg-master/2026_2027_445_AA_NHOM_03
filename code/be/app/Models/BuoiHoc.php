<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BuoiHoc extends Model
{
    protected $fillable = ['id_lop_hoc', 'thoi_gian_bat_dau', 'thoi_gian_ket_thuc', 'trang_thai'];

    protected $casts = ['thoi_gian_bat_dau' => 'datetime', 'thoi_gian_ket_thuc' => 'datetime'];

    public function lopHoc(): BelongsTo
    {
        return $this->belongsTo(LopHoc::class, 'id_lop_hoc');
    }
}
