<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Tạo bảng mon_hocs (Môn học)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mon_hocs', function (Blueprint $table) {
            $table->id();
            $table->string('ten_mon_hoc');
            $table->text('mo_ta')->nullable();
            $table->string('lop')->nullable()->comment('Cấp lớp / Trình độ');
            $table->string('tinh_trang')->default('hoat_dong')->comment('hoat_dong | tam_ngung');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mon_hocs');
    }
};
