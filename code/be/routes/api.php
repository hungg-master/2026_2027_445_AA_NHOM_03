<?php

use App\Http\Controllers\Admin\LopHocAdminController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Client\LopHocPublicController;
use App\Http\Controllers\GiaoVien\DuyetHocVienController;
use App\Http\Controllers\GiaoVien\LopHocController;
use App\Http\Controllers\GiaoVienController;
use App\Http\Controllers\HocVien\LichHocController;
use App\Http\Controllers\HocVienController;
use Illuminate\Support\Facades\Route;

// =========================================================================
// 1. API ADMIN
// =========================================================================
Route::post('admin/login', [AdminController::class, 'loginAdmin']);
Route::get('admin/tao-admin-mac-dinh', [AdminController::class, 'taoAdminMacDinh']);

Route::group(['prefix' => 'admin', 'middleware' => 'AdminMiddleware'], function () {
    Route::post('logout', [AdminController::class, 'logoutAdmin']);
    Route::get('check-token', [AdminController::class, 'checkTokenAdmin']);
    Route::get('profile', [AdminController::class, 'getProfile']);

    // Quản lý giáo viên
    Route::get('giao-vien/data', [GiaoVienController::class, 'getDataAdmin']);
    Route::post('giao-vien/change-status', [GiaoVienController::class, 'changeStatus']);
    Route::post('giao-vien/duyet', [GiaoVienController::class, 'duyetGiaoVien']);
    Route::post('giao-vien/search', [GiaoVienController::class, 'search']);

    // Quản lý học viên
    Route::get('hoc-vien/data', [HocVienController::class, 'getDataAdmin']);
    Route::post('hoc-vien/change-status', [HocVienController::class, 'changeStatus']);
    Route::post('hoc-vien/search', [HocVienController::class, 'search']);

    // Quản lý lớp học (MỚI)
    Route::get('lop-hoc', [LopHocAdminController::class, 'index']);
    Route::get('lop-hoc/{id}', [LopHocAdminController::class, 'show']);
    Route::post('lop-hoc/duyet', [LopHocAdminController::class, 'duyet']);
    Route::get('thong-ke', [LopHocAdminController::class, 'thongKe']);
});

// =========================================================================
// 2. API GIÁO VIÊN
// =========================================================================
Route::post('giao-vien/register', [GiaoVienController::class, 'register']);
Route::post('giao-vien/login', [GiaoVienController::class, 'login']);

Route::group(['prefix' => 'giao-vien', 'middleware' => 'GiaoVienMiddleware'], function () {
    // Profile
    Route::post('logout', [GiaoVienController::class, 'logout']);
    Route::get('check-token', [GiaoVienController::class, 'checkToken']);
    Route::get('profile/data', [GiaoVienController::class, 'getProfile']);
    Route::post('profile/update', [GiaoVienController::class, 'updateProfile']);
    Route::post('profile/change-password', [GiaoVienController::class, 'changePassword']);

    // Lịch dạy + Quản lý lớp học (MỚI)
    Route::get('lich-day', [LopHocController::class, 'lichDay']);
    Route::get('lop-hoc', [LopHocController::class, 'index']);
    Route::post('lop-hoc', [LopHocController::class, 'store']);
    Route::get('lop-hoc/{id}', [LopHocController::class, 'show']);
    Route::put('lop-hoc/{id}', [LopHocController::class, 'update']);
    Route::delete('lop-hoc/{id}', [LopHocController::class, 'destroy']);
    Route::post('lop-hoc/{id}/duyet-hoc-vien', [DuyetHocVienController::class, 'duyet']);
});

// =========================================================================
// 3. API HỌC VIÊN
// =========================================================================
Route::post('hoc-vien/register', [HocVienController::class, 'register']);
Route::post('hoc-vien/login', [HocVienController::class, 'login']);

Route::group(['prefix' => 'hoc-vien', 'middleware' => 'HocVienMiddleware'], function () {
    // Profile
    Route::post('logout', [HocVienController::class, 'logout']);
    Route::get('check-token', [HocVienController::class, 'checkToken']);
    Route::get('profile/data', [HocVienController::class, 'getProfile']);
    Route::post('profile/update', [HocVienController::class, 'updateProfile']);
    Route::post('profile/change-password', [HocVienController::class, 'changePassword']);

    // Lịch học + Lớp của tôi (MỚI)
    Route::get('lich-hoc', [LichHocController::class, 'lichHoc']);
    Route::get('lop-hoc-cua-toi', [LichHocController::class, 'lopHocCuaToi']);
    Route::post('dang-ky-lop-hoc', [LichHocController::class, 'dangKy']);
    Route::delete('huy-dang-ky/{id}', [LichHocController::class, 'huyDangKy']);
});

// =========================================================================
// 4. API CLIENT (PUBLIC)
// =========================================================================
Route::get('client/giao-vien/data', [GiaoVienController::class, 'getClientData']);
Route::get('client/giao-vien/chi-tiet/{id}', [GiaoVienController::class, 'getClientDetail']);

// Lớp học public (MỚI)
Route::get('client/lop-hoc/data', [LopHocPublicController::class, 'index']);
Route::get('client/lop-hoc/{id}', [LopHocPublicController::class, 'show']);
Route::get('client/mon-hoc', [LopHocPublicController::class, 'monHoc']);
Route::get('client/phong-hoc', [LopHocPublicController::class, 'phongHoc']);
