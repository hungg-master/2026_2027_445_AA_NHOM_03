<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::guard('admin')->user() ?: Auth::guard('sanctum')->user();
        if ($user && $user instanceof \App\Models\Admin && $user->tinh_trang == 1) {
            return $next($request);
        }

        return response()->json([
            'status' => false,
            'message' => 'Bạn cần đăng nhập tài khoản Quản trị viên để thực hiện chức năng này!',
        ], 401);
    }
}
