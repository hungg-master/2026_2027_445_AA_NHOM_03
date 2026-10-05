<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class HocVien extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'hoc_viens';

    public function setEmailAttribute(string $value): void
    {
        $this->attributes['email'] = mb_strtolower(trim($value));
    }

    protected $fillable = [
        'ho_ten',
        'email',
        'password',
        'so_dien_thoai',
        'ngay_sinh',
        'gioi_tinh',
        'dia_chi',
        'hinh_anh',
        'is_active',
        'is_block',
        'tinh_trang',
        'du_lieu_khuon_mat',
    ];

    protected $hidden = [
        'du_lieu_khuon_mat',
        'face_id_photo_path',
        'password',
        'remember_token',
    ];

    protected $appends = ['has_face_id'];

    public function getHasFaceIdAttribute(): bool
    {
        return ! empty($this->attributes['du_lieu_khuon_mat']);
    }

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }
}
