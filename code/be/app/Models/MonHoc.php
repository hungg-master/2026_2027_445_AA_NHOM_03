<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonHoc extends Model
{
    protected $table = 'mon_hocs';

    protected $fillable = [
        'ten_mon_hoc',
        'mo_ta',
        'lop',
        'tinh_trang',
    ];

    public function lopHocs()
    {
        return $this->hasMany(LopHoc::class, 'id_mon_hoc');
    }
}
