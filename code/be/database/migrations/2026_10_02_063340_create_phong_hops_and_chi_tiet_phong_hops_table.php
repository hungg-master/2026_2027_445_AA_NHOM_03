<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('phong_hops')) {
            Schema::create('phong_hops', function (Blueprint $table) {
                $table->id();
                $table->string('ma_phong', 50)->unique();
                $table->string('ten_phong');
                $table->unsignedBigInteger('id_chu_phong')->nullable();
                $table->unsignedBigInteger('id_lop_hoc')->nullable()->index();
                $table->unsignedInteger('so_nguoi_toi_da')->default(100);
                $table->text('mo_ta')->nullable();
                $table->text('email_khach_moi')->nullable();
                $table->timestamp('thoi_gian_bat_dau')->nullable();
                $table->timestamp('thoi_gian_ket_thuc')->nullable();
                $table->integer('trang_thai')->default(1)->comment('1: Đang mở, 0: Đã kết thúc');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('chi_tiet_phong_hops')) {
            Schema::create('chi_tiet_phong_hops', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('id_phong_hop')->index();
                $table->unsignedBigInteger('id_nguoi_dung')->index();
                $table->integer('xac_thuc_khuon_mat')->default(1);
                $table->integer('is_vi_pham')->default(0);
                $table->integer('is_nguoi_dung')->default(1);
                $table->integer('is_active')->default(1)->comment('1: Đang tham gia, 0: Đã rời');
                $table->integer('trang_thai')->default(1);
                $table->timestamp('thoi_gian_tham_gia')->nullable();
                $table->timestamp('thoi_gian_roi')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chi_tiet_phong_hops');
        Schema::dropIfExists('phong_hops');
    }
};
