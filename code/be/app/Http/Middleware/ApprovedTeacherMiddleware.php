<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApprovedTeacherMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        abort_unless(Auth::guard('sanctum')->user()?->trang_thai_duyet === 'da_duyet', 403, 'Hồ sơ giáo viên chưa được duyệt.');

        return $next($request);
    }
}
