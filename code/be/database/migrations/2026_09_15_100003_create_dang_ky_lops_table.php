<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Tạo bảng dang_ky_lops (Đăng ký lớp học)
 *
 * Mỗi quan hệ: 1 học viên đăng ký 1 lớp (unique).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dang_ky_lops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_lop_hoc')->constrained('lop_hocs')->onDelete('cascade');
            $table->foreignId('id_hoc_vien')->constrained('hoc_viens')->onDelete('cascade');
            $table->timestamp('ngay_dang_ky')->useCurrent();
            $table->string('trang_thai')->default('cho_thanh_toan')
                ->comment('cho_thanh_toan | da_thanh_toan | da_xac_nhan | da_huy');

            $table->timestamps();

            // Mỗi học viên chỉ đăng ký 1 lần cho 1 lớp
            $table->unique(['id_lop_hoc', 'id_hoc_vien'], 'uq_dk_lop_hv');

            $table->index('id_lop_hoc');
            $table->index('id_hoc_vien');
            $table->index('trang_thai');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dang_ky_lops');
    }
};
