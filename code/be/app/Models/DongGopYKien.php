<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DongGopYKien extends Model
{
    protected $table = 'dong_gop_y_kiens';

    protected $fillable = [
        'ho_ten',
        'email',
        'loai_phan_hoi',
        'diem_danh_gia',
        'noi_dung',
        'trang_thai',
    ];
}
