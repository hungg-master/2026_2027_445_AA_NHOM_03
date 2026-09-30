<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dong_gop_y_kiens', function (Blueprint $table) {
            $table->id();
            $table->string('ho_ten');
            $table->string('email')->nullable();
            $table->enum('loai_phan_hoi', ['gop_y', 'bao_loi', 'khen_ngoi', 'khieu_nai', 'khac'])->default('gop_y');
            $table->tinyInteger('diem_danh_gia')->nullable()->comment('1-5 stars');
            $table->text('noi_dung');
            $table->string('trang_thai')->default('chua_xu_ly');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dong_gop_y_kiens');
    }
};
