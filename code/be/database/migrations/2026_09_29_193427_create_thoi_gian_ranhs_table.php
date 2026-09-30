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
        Schema::create('thoi_gian_ranhs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_giao_vien')->nullable()->constrained('giao_viens')->onDelete('cascade');
            $table->foreignId('id_hoc_vien')->nullable()->constrained('hoc_viens')->onDelete('cascade');
            $table->enum('loai_nguoi_dung', ['giao_vien', 'hoc_vien']);
            $table->tinyInteger('ngay_trong_tuan')->comment('0-6 (Sunday to Saturday)')->nullable();
            $table->time('thoi_gian_bat_dau')->nullable();
            $table->time('thoi_gian_ket_thuc')->nullable();
            $table->string('trang_thai')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thoi_gian_ranhs');
    }
};
