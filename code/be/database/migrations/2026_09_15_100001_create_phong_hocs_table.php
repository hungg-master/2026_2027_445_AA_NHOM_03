<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Tạo bảng phong_hocs (Phòng học)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('phong_hocs', function (Blueprint $table) {
            $table->id();
            $table->string('so_phong')->comment('Ví dụ: P.302');
            $table->string('dia_chi')->comment('Ví dụ: Cơ sở 1 - 254 Nguyễn Văn Linh');
            $table->text('mo_ta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('phong_hocs');
    }
};
