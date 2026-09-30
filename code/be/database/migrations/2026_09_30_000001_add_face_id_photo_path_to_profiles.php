<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['hoc_viens', 'giao_viens'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->string('face_id_photo_path')->nullable();
            });
        }
    }

    public function down(): void
    {
        foreach (['hoc_viens', 'giao_viens'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropColumn('face_id_photo_path');
            });
        }
    }
};
