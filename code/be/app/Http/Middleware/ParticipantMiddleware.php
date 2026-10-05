<?php

namespace App\Http\Middleware;

use App\Models\GiaoVien;
use App\Models\HocVien;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ParticipantMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $actor = Auth::guard('sanctum')->user();
        if (! $actor instanceof HocVien && ! $actor instanceof GiaoVien) {
            return response()->json(['status' => false, 'message' => 'Vui lòng đăng nhập.'], 401);
        }
        abort_if($actor->is_block || $actor->tinh_trang != 1 || ! $actor->is_active, 403, 'Tài khoản không hoạt động.');
        abort_if($actor instanceof GiaoVien && $actor->trang_thai_duyet !== 'da_duyet', 403, 'Hồ sơ giáo viên chưa được duyệt.');

        return $next($request);
    }
}
