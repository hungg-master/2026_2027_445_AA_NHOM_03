<?php

namespace App\Services;

use App\Models\GiaoVien;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ConsultationAccess
{
    public static function role(): string
    {
        return Auth::guard('sanctum')->user() instanceof GiaoVien ? 'giao_vien' : 'hoc_vien';
    }

    public static function owned(): Builder
    {
        return DB::table('consultation_conversations')->where('id_'.self::role(), Auth::guard('sanctum')->id());
    }
}
