<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Tạo bảng lop_hocs (Lớp học)
 *
 * Một giáo viên có thể dạy nhiều lớp.
 * Mỗi lớp thuộc về 1 môn học, có thể online (link) hoặc offline (phòng).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lop_hocs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_giao_vien')->constrained('giao_viens')->onDelete('cascade');
            $table->foreignId('id_mon_hoc')->constrained('mon_hocs')->onDelete('restrict');
            $table->foreignId('id_phong_hoc')->nullable()->constrained('phong_hocs')->onDelete('set null');

            $table->enum('loai_lop', ['dai_tra', 'kem'])->comment('Đại trà (lớn) | Kèm (1-1 hoặc tối đa 5)');
            $table->enum('hinh_thuc', ['online', 'offline']);
            $table->string('link_online')->nullable()->comment('Link Google Meet / Zoom khi online');
            $table->decimal('hoc_phi', 12, 2)->default(0);
            $table->unsignedInteger('si_so_toi_da');

            $table->timestamp('thoi_gian_bat_dau');
            $table->timestamp('thoi_gian_ket_thuc');

            $table->string('tinh_trang')->default('sap_mo')
                ->comment('sap_mo | dang_mo | dang_hoc | da_ket_thuc | da_huy');

            $table->timestamps();

            // Index cho truy vấn nhanh
            $table->index('id_giao_vien');
            $table->index('id_mon_hoc');
            $table->index('thoi_gian_bat_dau');
            $table->index('tinh_trang');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lop_hocs');
    }
};
