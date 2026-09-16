<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\GiaoVienController;
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
});

// =========================================================================
// 2. API GIÁO VIÊN
// =========================================================================
Route::post('giao-vien/register', [GiaoVienController::class, 'register']);
Route::post('giao-vien/login', [GiaoVienController::class, 'login']);

Route::group(['prefix' => 'giao-vien', 'middleware' => 'GiaoVienMiddleware'], function () {
    Route::post('logout', [GiaoVienController::class, 'logout']);
    Route::get('check-token', [GiaoVienController::class, 'checkToken']);
    Route::get('profile/data', [GiaoVienController::class, 'getProfile']);
    Route::post('profile/update', [GiaoVienController::class, 'updateProfile']);
    Route::post('profile/change-password', [GiaoVienController::class, 'changePassword']);
});

// =========================================================================
// 3. API HỌC VIÊN
// =========================================================================
Route::post('hoc-vien/register', [HocVienController::class, 'register']);
Route::post('hoc-vien/login', [HocVienController::class, 'login']);

Route::group(['prefix' => 'hoc-vien', 'middleware' => 'HocVienMiddleware'], function () {
    Route::post('logout', [HocVienController::class, 'logout']);
    Route::get('check-token', [HocVienController::class, 'checkToken']);
    Route::get('profile/data', [HocVienController::class, 'getProfile']);
    Route::post('profile/update', [HocVienController::class, 'updateProfile']);
    Route::post('profile/change-password', [HocVienController::class, 'changePassword']);
});

// =========================================================================
// 4. API CLIENT (PUBLIC)
// =========================================================================
Route::get('client/giao-vien/data', [GiaoVienController::class, 'getClientData']);
Route::get('client/giao-vien/chi-tiet/{id}', [GiaoVienController::class, 'getClientDetail']);
