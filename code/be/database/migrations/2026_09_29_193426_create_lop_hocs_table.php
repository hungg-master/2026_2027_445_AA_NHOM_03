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
        Schema::create('lop_hocs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_giao_vien')->constrained('giao_viens')->onDelete('cascade');
            $table->foreignId('id_mon_hoc')->constrained('mon_hocs')->onDelete('cascade');
            $table->foreignId('id_phong_hoc')->nullable()->constrained('phong_hocs')->onDelete('set null');
            $table->enum('loai_lop', ['dai_tra', 'kem'])->nullable();
            $table->enum('hinh_thuc', ['online', 'offline'])->nullable();
            $table->string('link_online')->nullable();
            $table->decimal('hoc_phi', 12, 2)->default(0);
            $table->integer('si_so_toi_da')->nullable();
            $table->dateTime('thoi_gian_bat_dau')->nullable();
            $table->dateTime('thoi_gian_ket_thuc')->nullable();
            $table->string('tinh_trang')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lop_hocs');
    }
};
