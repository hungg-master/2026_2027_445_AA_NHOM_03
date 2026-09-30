<?php

namespace App\Http\Controllers;

use App\Http\Requests\HocVien\DangKyHocVienRequest;
use App\Http\Requests\HocVien\DangNhapHocVienRequest;
use App\Http\Requests\HocVien\UpdateProfileHocVienRequest;
use App\Http\Requests\HocVien\ChangePasswordHocVienRequest;
use App\Models\HocVien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class HocVienController extends Controller
{
    // =========================================================================
    // AUTHENTICATION & PROFILE (DÀNH CHO HỌC VIÊN)
    // =========================================================================

    public function register(DangKyHocVienRequest $request)
    {
        try {
            $data = $request->all();
            $data['password']   = Hash::make($request->password);
            $data['tinh_trang'] = 1;
            $data['is_active']  = 1;
            $data['is_block']   = 0;

            $hocVien = HocVien::create($data);

            return response()->json([
                'status'  => true,
                'message' => 'Đăng ký tài khoản học viên thành công! Bạn có thể đăng nhập ngay bây giờ.',
                'data'    => $hocVien,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Đã có lỗi xảy ra: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function login(DangNhapHocVienRequest $request)
    {
        try {
            $check = Auth::guard('hoc_vien')->attempt([
                'email'    => $request->email,
                'password' => $request->password,
            ]);

            if ($check) {
                $hocVien = Auth::guard('hoc_vien')->user();

                if ($hocVien->is_block == 1) {
                    return response()->json([
                        'status'  => false,
                        'message' => 'Tài khoản học viên của bạn đã bị khóa!',
                    ], 403);
                }

                $token = $hocVien->createToken('token_hoc_vien')->plainTextToken;

                return response()->json([
                    'status'  => true,
                    'message' => 'Đăng nhập thành công',
                    'token'   => $token,
                    'user'    => $hocVien,
                ]);
            }

            return response()->json([
                'status'  => false,
                'message' => 'Tài khoản hoặc mật khẩu không chính xác',
            ], 401);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Đã có lỗi xảy ra: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        if ($user) {
            $user->currentAccessToken()->delete();
        }

        return response()->json([
            'status'  => true,
            'message' => 'Đăng xuất thành công',
        ]);
    }

    public function checkToken()
    {
        $user = Auth::guard('sanctum')->user();
        if ($user && $user instanceof HocVien) {
            return response()->json([
                'status' => true,
                'user'   => $user,
            ]);
        }

        return response()->json([
            'status'  => false,
            'message' => 'Token không hợp lệ hoặc đã hết hạn!',
        ], 401);
    }

    public function getProfile()
    {
        $user = Auth::guard('sanctum')->user();
        return response()->json([
            'status' => true,
            'data'   => $user,
        ]);
    }

    public function updateProfile(UpdateProfileHocVienRequest $request)
    {
        try {
            $user = Auth::guard('sanctum')->user();
            $user->update($request->only([
                'ho_ten',
                'so_dien_thoai',
                'ngay_sinh',
                'gioi_tinh',
                'dia_chi',
                'hinh_anh',
            ]));

            return response()->json([
                'status'  => true,
                'message' => 'Cập nhật thông tin học viên thành công!',
                'data'    => $user,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Cập nhật thất bại: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function changePassword(ChangePasswordHocVienRequest $request)
    {
        try {
            $user = Auth::guard('sanctum')->user();

            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Mật khẩu hiện tại không chính xác!',
                ], 400);
            }

            $user->password = Hash::make($request->password);
            $user->save();

            return response()->json([
                'status'  => true,
                'message' => 'Đổi mật khẩu thành công!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Đổi mật khẩu thất bại: ' . $e->getMessage(),
            ], 500);
        }
    }

    // =========================================================================
    // ADMIN QUẢN LÝ HỌC VIÊN
    // =========================================================================

    public function getDataAdmin()
    {
        $data = HocVien::orderBy('id', 'desc')->get();
        return response()->json([
            'status' => true,
            'data'   => $data,
        ]);
    }

    public function changeStatus(Request $request)
    {
        $hocVien = HocVien::find($request->id);
        if ($hocVien) {
            $hocVien->tinh_trang = $hocVien->tinh_trang == 1 ? 0 : 1;
            $hocVien->save();

            return response()->json([
                'status'  => true,
                'message' => 'Cập nhật trạng thái học viên thành công!',
            ]);
        }

        return response()->json([
            'status'  => false,
            'message' => 'Không tìm thấy học viên!',
        ], 404);
    }

    public function search(Request $request)
    {
        $timKiem = trim($request->noi_dung_tim);
        $data    = HocVien::where('ho_ten', 'like', '%' . $timKiem . '%')
            ->orWhere('email', 'like', '%' . $timKiem . '%')
            ->orWhere('so_dien_thoai', 'like', '%' . $timKiem . '%')
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'status' => true,
            'data'   => $data,
        ]);
    }
}
