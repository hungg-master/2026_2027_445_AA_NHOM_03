<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('hoc_viens') && !Schema::hasColumn('hoc_viens', 'du_lieu_khuon_mat')) {
            Schema::table('hoc_viens', function (Blueprint $table) {
                $table->longText('du_lieu_khuon_mat')->nullable()->after('face_id_photo_path');
            });
        }

        if (Schema::hasTable('giao_viens') && !Schema::hasColumn('giao_viens', 'du_lieu_khuon_mat')) {
            Schema::table('giao_viens', function (Blueprint $table) {
                $table->longText('du_lieu_khuon_mat')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('hoc_viens') && Schema::hasColumn('hoc_viens', 'du_lieu_khuon_mat')) {
            Schema::table('hoc_viens', function (Blueprint $table) {
                $table->dropColumn('du_lieu_khuon_mat');
            });
        }

        if (Schema::hasTable('giao_viens') && Schema::hasColumn('giao_viens', 'du_lieu_khuon_mat')) {
            Schema::table('giao_viens', function (Blueprint $table) {
                $table->dropColumn('du_lieu_khuon_mat');
            });
        }
    }
};
