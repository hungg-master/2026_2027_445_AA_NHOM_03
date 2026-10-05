<?php

use App\Http\Controllers\AccountResetController;
use App\Http\Controllers\AdminCatalogController;
use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\LessonReviewController;
use App\Http\Controllers\StudyAssistantController;
use App\Http\Controllers\TeacherBankController;
use Illuminate\Support\Facades\Route;

Route::post('auth/forgot-password', [AccountResetController::class, 'forgot'])->middleware('throttle:5,1');
Route::post('auth/reset-password', [AccountResetController::class, 'reset'])->middleware('throttle:10,1');

Route::middleware('HocVienMiddleware')->prefix('hoc-vien')->group(function () {
    Route::get('hoc-phi', [FinanceController::class, 'obligations']);
    Route::post('hoc-phi/{id}/test-payment', [FinanceController::class, 'requestTest']);
    Route::get('danh-gia', [LessonReviewController::class, 'studentIndex']);
    Route::post('danh-gia', [LessonReviewController::class, 'store']);
    Route::get('thong-ke-tai-chinh', [FinanceController::class, 'statistics']);
});
Route::middleware('GiaoVienMiddleware')->prefix('giao-vien')->group(function () {
    Route::get('ngan-hang', [TeacherBankController::class, 'index']);
    Route::post('ngan-hang', [TeacherBankController::class, 'store']);
});
Route::middleware('ParticipantMiddleware')->group(function () {
    Route::get('giao-vien/danh-gia', [LessonReviewController::class, 'teacherIndex'])->middleware('ApprovedTeacherMiddleware');
    Route::get('giao-vien/thong-ke-tai-chinh', [FinanceController::class, 'statistics'])->middleware('ApprovedTeacherMiddleware');
    Route::get('chat/contacts', [ConsultationController::class, 'contacts']);
    Route::get('chat/conversations', [ConsultationController::class, 'index']);
    Route::post('chat/conversations', [ConsultationController::class, 'store']);
    Route::get('chat/conversations/{id}/messages', [ConsultationController::class, 'messages']);
    Route::post('chat/conversations/{id}/messages', [ConsultationController::class, 'send'])->middleware('throttle:60,1');
    Route::post('ai/assist', [StudyAssistantController::class, 'assist'])->middleware('throttle:10,1');
});
Route::middleware('AdminMiddleware')->prefix('admin')->group(function () {
    Route::get('thanh-toan', [FinanceController::class, 'adminTransactions']);
    Route::post('thanh-toan/{id}/settle-test', [FinanceController::class, 'settleTest']);
    Route::get('thong-ke-tai-chinh', [FinanceController::class, 'statistics']);
    Route::get('mon-hoc', [AdminCatalogController::class, 'subjects']);
    Route::post('mon-hoc', [AdminCatalogController::class, 'storeSubject']);
    Route::put('mon-hoc/{id}', [AdminCatalogController::class, 'updateSubject']);
    Route::delete('mon-hoc/{id}', [AdminCatalogController::class, 'deleteSubject']);
    Route::get('phong-hoc', [AdminCatalogController::class, 'rooms']);
    Route::post('phong-hoc', [AdminCatalogController::class, 'storeRoom']);
    Route::put('phong-hoc/{id}', [AdminCatalogController::class, 'updateRoom']);
    Route::delete('phong-hoc/{id}', [AdminCatalogController::class, 'deleteRoom']);
});
