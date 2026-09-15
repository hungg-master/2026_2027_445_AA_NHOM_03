<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function loginAdmin(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ], [
            'email.required'    => 'Email không được để trống',
            'email.email'       => 'Email không đúng định dạng',
            'password.required' => 'Mật khẩu không được để trống',
        ]);

        $check = Auth::guard('admin')->attempt([
            'email'    => $request->email,
            'password' => $request->password,
        ]);

        if ($check) {
            $admin = Auth::guard('admin')->user();
            if ($admin->tinh_trang == 0) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Tài khoản quản trị viên đã bị khóa!',
                ], 403);
            }

            $token = $admin->createToken('token_admin')->plainTextToken;

            return response()->json([
                'status'  => true,
                'message' => 'Đăng nhập trang quản trị thành công',
                'token'   => $token,
                'admin'   => $admin,
            ]);
        }

        return response()->json([
            'status'  => false,
            'message' => 'Email hoặc mật khẩu không chính xác',
        ], 401);
    }

    public function logoutAdmin(Request $request)
    {
        $admin = Auth::guard('sanctum')->user();
        if ($admin) {
            $admin->currentAccessToken()->delete();
        }

        return response()->json([
            'status'  => true,
            'message' => 'Đăng xuất quản trị thành công',
        ]);
    }

    public function checkTokenAdmin()
    {
        $admin = Auth::guard('sanctum')->user();
        if ($admin && $admin instanceof Admin && $admin->tinh_trang == 1) {
            return response()->json([
                'status' => true,
                'admin'  => $admin,
            ]);
        }

        return response()->json([
            'status'  => false,
            'message' => 'Bạn chưa đăng nhập quản trị viên!',
        ], 401);
    }

    public function getProfile()
    {
        $admin = Auth::guard('sanctum')->user();
        return response()->json([
            'status' => true,
            'data'   => $admin,
        ]);
    }

    // Tiện ích tạo tài khoản Admin mặc định ban đầu nếu chưa có
    public function taoAdminMacDinh()
    {
        $tonTai = Admin::where('email', 'admin@edulink.vn')->first();
        if ($tonTai) {
            return response()->json([
                'status'  => true,
                'message' => 'Tài khoản admin đã tồn tại',
            ]);
        }

        $admin = Admin::create([
            'ho_ten'        => 'Quản trị viên EduLink',
            'email'         => 'admin@edulink.vn',
            'password'      => Hash::make('123456'),
            'so_dien_thoai' => '0905123456',
            'tinh_trang'    => 1,
            'is_master'     => 1,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Đã tạo tài khoản admin mặc định: admin@edulink.vn / 123456',
            'data'    => $admin,
        ]);
    }
}
