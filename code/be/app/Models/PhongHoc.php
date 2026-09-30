<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhongHoc extends Model
{
    protected $table = 'phong_hocs';

    protected $fillable = [
        'so_phong',
        'dia_chi',
        'mo_ta',
    ];

    public function lopHocs()
    {
        return $this->hasMany(LopHoc::class, 'id_phong_hoc');
    }
}
