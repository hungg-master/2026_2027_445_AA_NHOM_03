<?php

namespace App\Http\Controllers;

use App\Http\Requests\GiaoVien\DangKyGiaoVienRequest;
use App\Http\Requests\GiaoVien\DangNhapGiaoVienRequest;
use App\Http\Requests\GiaoVien\UpdateProfileGiaoVienRequest;
use App\Http\Requests\GiaoVien\ChangePasswordGiaoVienRequest;
use App\Models\GiaoVien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class GiaoVienController extends Controller
{
    // =========================================================================
    // AUTHENTICATION & PROFILE (DÀNH CHO GIÁO VIÊN)
    // =========================================================================

    public function register(DangKyGiaoVienRequest $request)
    {
        try {
            $data = $request->all();
            $data['password']          = Hash::make($request->password);
            $data['trang_thai_duyet']  = 'cho_duyet';
            $data['tinh_trang']        = 1;
            $data['is_active']         = 1;
            $data['is_block']          = 0;

            $giaoVien = GiaoVien::create($data);

            return response()->json([
                'status'  => true,
                'message' => 'Đăng ký tài khoản giáo viên thành công! Vui lòng chờ quản trị viên phê duyệt hồ sơ.',
                'data'    => $giaoVien,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Đã có lỗi xảy ra: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function login(DangNhapGiaoVienRequest $request)
    {
        try {
            $check = Auth::guard('giao_vien')->attempt([
                'email'    => $request->email,
                'password' => $request->password,
            ]);

            if ($check) {
                $giaoVien = Auth::guard('giao_vien')->user();

                if ($giaoVien->is_block == 1) {
                    return response()->json([
                        'status'  => false,
                        'message' => 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ quản trị viên!',
                    ], 403);
                }

                $token = $giaoVien->createToken('token_giao_vien')->plainTextToken;

                return response()->json([
                    'status'  => true,
                    'message' => 'Đăng nhập thành công',
                    'token'   => $token,
                    'user'    => $giaoVien,
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
        if ($user && $user instanceof GiaoVien) {
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

    public function updateProfile(UpdateProfileGiaoVienRequest $request)
    {
        try {
            $user = Auth::guard('sanctum')->user();
            $user->update($request->only([
                'ho_ten',
                'so_dien_thoai',
                'ngay_sinh',
                'gioi_tinh',
                'chuc_danh',
                'so_nam_kinh_nghiem',
                'mo_ta',
                'hinh_anh',
            ]));

            return response()->json([
                'status'  => true,
                'message' => 'Cập nhật hồ sơ giáo viên thành công!',
                'data'    => $user,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Cập nhật thất bại: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function changePassword(ChangePasswordGiaoVienRequest $request)
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
    // ADMIN QUẢN LÝ GIÁO VIÊN
    // =========================================================================

    public function getDataAdmin()
    {
        $data = GiaoVien::orderBy('id', 'desc')->get();
        return response()->json([
            'status' => true,
            'data'   => $data,
        ]);
    }

    public function changeStatus(Request $request)
    {
        $giaoVien = GiaoVien::find($request->id);
        if ($giaoVien) {
            $giaoVien->tinh_trang = $giaoVien->tinh_trang == 1 ? 0 : 1;
            $giaoVien->save();

            return response()->json([
                'status'  => true,
                'message' => 'Cập nhật trạng thái giáo viên thành công!',
            ]);
        }

        return response()->json([
            'status'  => false,
            'message' => 'Không tìm thấy giáo viên!',
        ], 404);
    }

    public function duyetGiaoVien(Request $request)
    {
        $admin    = Auth::guard('sanctum')->user();
        $giaoVien = GiaoVien::find($request->id);

        if ($giaoVien) {
            $giaoVien->trang_thai_duyet   = $request->trang_thai_duyet; // 'da_duyet' | 'tu_choi'
            $giaoVien->giao_vien_da_duyet = $admin ? $admin->id : null;
            $giaoVien->save();

            $msg = $request->trang_thai_duyet == 'da_duyet' ? 'Đã duyệt hồ sơ giáo viên thành công!' : 'Đã từ chối hồ sơ giáo viên!';
            return response()->json([
                'status'  => true,
                'message' => $msg,
            ]);
        }

        return response()->json([
            'status'  => false,
            'message' => 'Không tìm thấy giáo viên!',
        ], 404);
    }

    public function search(Request $request)
    {
        $timKiem = trim($request->noi_dung_tim);
        $data    = GiaoVien::where('ho_ten', 'like', '%' . $timKiem . '%')
            ->orWhere('email', 'like', '%' . $timKiem . '%')
            ->orWhere('so_dien_thoai', 'like', '%' . $timKiem . '%')
            ->orWhere('chuc_danh', 'like', '%' . $timKiem . '%')
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'status' => true,
            'data'   => $data,
        ]);
    }

    // =========================================================================
    // CLIENT (PUBLIC API CHO HỌC VIÊN TÌM GIA SƯ)
    // =========================================================================

    public function getClientData()
    {
        $data = GiaoVien::where('trang_thai_duyet', 'da_duyet')
            ->where('tinh_trang', 1)
            ->where('is_block', 0)
            ->select('id', 'ho_ten', 'chuc_danh', 'so_nam_kinh_nghiem', 'mo_ta', 'hinh_anh')
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'status' => true,
            'data'   => $data,
        ]);
    }

    public function getClientDetail($id)
    {
        $giaoVien = GiaoVien::where('id', $id)
            ->where('trang_thai_duyet', 'da_duyet')
            ->where('tinh_trang', 1)
            ->first();

        if ($giaoVien) {
            return response()->json([
                'status' => true,
                'data'   => $giaoVien,
            ]);
        }

        return response()->json([
            'status'  => false,
            'message' => 'Không tìm thấy thông tin giáo viên!',
        ], 404);
    }
}
