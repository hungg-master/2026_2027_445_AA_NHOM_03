<?php

namespace App\Http\Middleware;

use App\Models\HocVien;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class HocVienMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::guard('sanctum')->user();
        if ($user && $user instanceof HocVien) {
            if ($user->is_block == 1) {
                return response()->json([
                    'status' => false,
                    'message' => 'Tài khoản học viên của bạn đã bị khóa!',
                ], 403);
            }
            if ($user->tinh_trang != 1 || ! $user->is_active) {
                return response()->json([
                    'status' => false,
                    'message' => 'Tài khoản học viên hiện đang tạm ngưng hoạt động!',
                ], 403);
            }

            return $next($request);
        }

        return response()->json([
            'status' => false,
            'message' => 'Bạn cần đăng nhập tài khoản Học viên để thực hiện chức năng này!',
        ], 401);
    }
}
