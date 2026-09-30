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
        Schema::create('giao_viens', function (Blueprint $table) {
            $table->id();
            $table->string('ho_ten');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('so_dien_thoai')->nullable();
            $table->date('ngay_sinh')->nullable();
            $table->integer('gioi_tinh')->default(0)->comment('0: Nam, 1: Nu, 2: Khác');
            $table->integer('so_nam_kinh_nghiem')->default(0);
            $table->string('chuc_danh')->nullable();
            $table->text('mo_ta')->nullable();
            $table->string('hinh_anh')->nullable();
            $table->string('trang_thai_duyet')->default('cho_duyet')->comment('cho_duyet, da_duyet, tu_choi');
            $table->unsignedBigInteger('giao_vien_da_duyet')->nullable()->comment('ID Admin đã duyệt');
            $table->integer('is_active')->default(1);
            $table->integer('is_block')->default(0);
            $table->integer('tinh_trang')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('giao_viens');
    }
};
