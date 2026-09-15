<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Tạo bảng thoi_gian_ranhs (Thời gian rảnh)
 *
 * Mỗi slot thuộc về 1 người dùng (GV hoặc HV), phục vụ thuật toán gợi ý.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('thoi_gian_ranhs', function (Blueprint $table) {
            $table->id();
            $table->enum('loai_nguoi_dung', ['giao_vien', 'hoc_vien']);
            $table->foreignId('id_giao_vien')->nullable()->constrained('giao_viens')->onDelete('cascade');
            $table->foreignId('id_hoc_vien')->nullable()->constrained('hoc_viens')->onDelete('cascade');

            // 0: Chủ nhật, 1: Thứ 2, ..., 6: Thứ 7
            $table->unsignedTinyInteger('ngay_trong_tuan');
            $table->time('thoi_gian_bat_dau');
            $table->time('thoi_gian_ket_thuc');
            $table->string('trang_thai')->default('hoat_dong');

            $table->timestamps();

            $table->index('id_giao_vien');
            $table->index('id_hoc_vien');
            $table->index('loai_nguoi_dung');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('thoi_gian_ranhs');
    }
};
