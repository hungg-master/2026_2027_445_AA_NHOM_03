<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class GiaoVien extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'giao_viens';

    protected $fillable = [
        'ho_ten',
        'email',
        'password',
        'so_dien_thoai',
        'ngay_sinh',
        'gioi_tinh',
        'so_nam_kinh_nghiem',
        'chuc_danh',
        'mo_ta',
        'hinh_anh',
        'trang_thai_duyet',
        'giao_vien_da_duyet',
        'is_active',
        'is_block',
        'tinh_trang',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }
}
