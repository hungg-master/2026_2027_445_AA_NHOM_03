<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lop_hocs', function (Blueprint $table) {
            $table->string('recurrence')->default('once');
            $table->date('recurrence_until')->nullable();
        });
        Schema::create('buoi_hocs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_lop_hoc')->constrained('lop_hocs')->cascadeOnDelete();
            $table->dateTime('thoi_gian_bat_dau');
            $table->dateTime('thoi_gian_ket_thuc');
            $table->string('trang_thai')->default('scheduled');
            $table->timestamps();
            $table->unique(['id_lop_hoc', 'thoi_gian_bat_dau']);
            $table->index(['thoi_gian_bat_dau', 'thoi_gian_ket_thuc']);
        });
        DB::table('lop_hocs')->orderBy('id')->chunkById(200, function ($classes) {
            foreach ($classes as $class) {
                if (! $class->thoi_gian_bat_dau || ! $class->thoi_gian_ket_thuc) {
                    continue;
                }
                DB::table('buoi_hocs')->insert([
                    'id_lop_hoc' => $class->id,
                    'thoi_gian_bat_dau' => $class->thoi_gian_bat_dau,
                    'thoi_gian_ket_thuc' => $class->thoi_gian_ket_thuc,
                    'trang_thai' => $class->tinh_trang === 'da_huy' ? 'cancelled' : ($class->tinh_trang === 'da_ket_thuc' ? 'completed' : 'scheduled'),
                    'created_at' => now(), 'updated_at' => now(),
                ]);
            }
        });
        Schema::create('face_verifications', function (Blueprint $table) {
            $table->id();
            $table->string('token_hash', 64)->unique();
            $table->string('actor_type');
            $table->unsignedBigInteger('actor_id');
            $table->string('purpose');
            $table->unsignedBigInteger('target_id');
            $table->dateTime('expires_at');
            $table->dateTime('consumed_at')->nullable();
            $table->timestamps();
            $table->index(['actor_type', 'actor_id']);
        });
        Schema::table('trial_bookings', function (Blueprint $table) {
            $table->foreignId('id_buoi_hoc')->nullable()->constrained('buoi_hocs')->nullOnDelete();
            $table->foreignId('id_hoc_vien')->nullable()->constrained('hoc_viens')->nullOnDelete();
            $table->foreignId('id_giao_vien')->nullable()->constrained('giao_viens')->nullOnDelete();
            $table->foreignId('id_mon_hoc')->nullable()->constrained('mon_hocs')->restrictOnDelete();
            $table->foreignId('id_lop_hoc')->nullable()->constrained('lop_hocs')->nullOnDelete();
            $table->dateTime('thoi_gian_bat_dau')->nullable();
            $table->dateTime('thoi_gian_ket_thuc')->nullable();
        });
        Schema::table('chi_tiet_phong_hops', function (Blueprint $table) {
            $table->unsignedBigInteger('id_phong_hop')->nullable()->change();
            $table->string('attendance_source')->nullable();
            $table->foreignId('id_buoi_hoc')->nullable()->constrained('buoi_hocs')->nullOnDelete();
            $table->string('loai_nguoi_dung')->nullable();
            $table->integer('xac_thuc_khuon_mat')->default(0)->change();
            $table->unique(['id_buoi_hoc', 'loai_nguoi_dung', 'id_nguoi_dung'], 'attendance_actor_session');
        });
        // Historic browser flags are not a server verification or attendance proof.
        DB::table('chi_tiet_phong_hops')->update(['xac_thuc_khuon_mat' => 0, 'is_active' => 0]);
    }

    public function down(): void
    {
        Schema::table('chi_tiet_phong_hops', function (Blueprint $table) {
            $table->dropUnique('attendance_actor_session');
            $table->dropConstrainedForeignId('id_buoi_hoc');
            $table->dropColumn(['loai_nguoi_dung', 'attendance_source']);
        });
        Schema::table('trial_bookings', function (Blueprint $table) {
            foreach (['id_hoc_vien', 'id_giao_vien', 'id_mon_hoc', 'id_lop_hoc', 'id_buoi_hoc'] as $column) {
                $table->dropConstrainedForeignId($column);
            }
            $table->dropColumn(['thoi_gian_bat_dau', 'thoi_gian_ket_thuc']);
        });
        Schema::dropIfExists('face_verifications');
        Schema::dropIfExists('buoi_hocs');
        Schema::table('lop_hocs', fn (Blueprint $table) => $table->dropColumn(['recurrence', 'recurrence_until']));
    }
};
