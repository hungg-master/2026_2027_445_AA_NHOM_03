<?php

use App\Http\Controllers\Admin\LopHocAdminController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Client\LopHocPublicController;
use App\Http\Controllers\DongGopYKienController;
use App\Http\Controllers\FaceIdController;
use App\Http\Controllers\FaceIdPhotoController;
use App\Http\Controllers\GiaoVien\DuyetHocVienController;
use App\Http\Controllers\GiaoVien\LopHocController;
use App\Http\Controllers\GiaoVienController;
use App\Http\Controllers\HocVien\LichHocController;
use App\Http\Controllers\HocVienController;
use App\Http\Controllers\LessonCompletionController;
use App\Http\Controllers\PhongHopController;
use App\Http\Controllers\TeacherMatchingController;
use App\Http\Controllers\ThoiGianRanhController;
use App\Http\Controllers\TrialBookingController;
use Illuminate\Support\Facades\Route;

// =========================================================================
// 1. API ADMIN
// =========================================================================
Route::post('admin/login', [AdminController::class, 'loginAdmin']);

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
    Route::post('profile/face-id', [FaceIdPhotoController::class, 'store']);
    Route::post('profile/change-password', [GiaoVienController::class, 'changePassword']);
    Route::post('face-id/sample', [FaceIdController::class, 'sample'])->middleware('throttle:20,1');
    Route::post('face-id/verify', [FaceIdController::class, 'verify'])->middleware('throttle:20,1');

    // Lịch dạy + Quản lý lớp học (MỚI)
    Route::middleware('ApprovedTeacherMiddleware')->group(function () {
        Route::post('buoi-hoc/{id}/complete', [LessonCompletionController::class, 'complete']);
        Route::get('lich-day', [LopHocController::class, 'lichDay']);
        Route::get('lop-hoc', [LopHocController::class, 'index']);
        Route::post('lop-hoc', [LopHocController::class, 'store']);
        Route::get('lop-hoc/{id}', [LopHocController::class, 'show']);
        Route::put('lop-hoc/{id}', [LopHocController::class, 'update']);
        Route::delete('lop-hoc/{id}', [LopHocController::class, 'destroy']);
        Route::post('lop-hoc/{id}/duyet-hoc-vien', [DuyetHocVienController::class, 'duyet']);

        // Lịch rảnh & Lớp học
        Route::get('thoi-gian-ranh', [ThoiGianRanhController::class, 'getGiaoVienSchedule']);
        Route::post('thoi-gian-ranh', [ThoiGianRanhController::class, 'updateGiaoVienSchedule']);
    });
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
    Route::post('profile/face-id', [FaceIdPhotoController::class, 'store']);
    Route::post('xac-thuc-khuon-mat', [FaceIdController::class, 'sample']);
    Route::post('face-id/sample', [FaceIdController::class, 'sample'])->middleware('throttle:20,1');
    Route::post('face-id/verify', [FaceIdController::class, 'verify'])->middleware('throttle:20,1');
    Route::post('profile/change-password', [HocVienController::class, 'changePassword']);

    // Lịch học + Lớp của tôi (MỚI)
    Route::get('lich-hoc', [LichHocController::class, 'lichHoc']);
    Route::get('goi-y-giao-vien', [TeacherMatchingController::class, 'index']);
    Route::get('lop-hoc-cua-toi', [LichHocController::class, 'lopHocCuaToi']);
    Route::post('dang-ky-lop-hoc', [LichHocController::class, 'dangKy']);
    Route::delete('huy-dang-ky/{id}', [LichHocController::class, 'huyDangKy']);

    // Lịch rảnh
    Route::get('thoi-gian-ranh', [ThoiGianRanhController::class, 'getHocVienSchedule']);
    Route::post('thoi-gian-ranh', [ThoiGianRanhController::class, 'updateHocVienSchedule']);
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

// =========================================================================
// 5. ĐÓNG GÓP Ý KIẾN (PUBLIC - không cần đăng nhập)
// =========================================================================
Route::post('dong-gop-y-kien', [DongGopYKienController::class, 'store']);
Route::post('nguoi-dung/xac-thuc-khuon-mat', [FaceIdController::class, 'sample'])->middleware('ParticipantMiddleware');
Route::post('hoc-thu', [TrialBookingController::class, 'store'])->middleware('throttle:10,1');

// Xem danh sách phản hồi (Admin only)
Route::group(['prefix' => 'admin', 'middleware' => 'AdminMiddleware'], function () {
    Route::get('dong-gop-y-kien', [DongGopYKienController::class, 'index']);
});

// =========================================================================
// 6. PHÒNG HỌP TRỰC TUYẾN (EDULINK ONLINE ROOMS)
// =========================================================================
Route::middleware('ParticipantMiddleware')->group(function () {
    Route::post('phong-hop/create', [PhongHopController::class, 'create']);
    Route::post('phong-hop/kiem-tra-phong-hop', [PhongHopController::class, 'kiemTraPhongHop']);
    Route::post('phong-hop/tao-token', [PhongHopController::class, 'taoToken']);
    Route::get('phong-hop/ma-phong', [PhongHopController::class, 'maPhong']);
    Route::post('phong-hop/roi-phong', [PhongHopController::class, 'roiPhong']);
    Route::get('phong-hop/lich-su-tham-gia', [PhongHopController::class, 'lichSuThamGia']);

    Route::get('chi-tiet-phong-hop/data', [PhongHopController::class, 'chiTietPhongHopData']);
    Route::post('chi-tiet-phong-hop/create', [PhongHopController::class, 'chiTietPhongHopCreate']);
    Route::get('nguoi-dung/phong-hop-lien-quan', [PhongHopController::class, 'phongHopLienQuan']);
});

require __DIR__.'/supporting.php';

foreach (['hoc-vien' => 'HocVienMiddleware', 'giao-vien' => ['GiaoVienMiddleware', 'ApprovedTeacherMiddleware'], 'admin' => 'AdminMiddleware'] as $prefix => $middleware) {
    Route::prefix($prefix)->middleware($middleware)->group(function () {
        Route::get('hoc-thu', [TrialBookingController::class, 'index']);
        Route::post('hoc-thu/{id}/confirm', [TrialBookingController::class, 'confirm']);
        Route::post('hoc-thu/{id}/cancel', [TrialBookingController::class, 'cancel']);
    });
}
