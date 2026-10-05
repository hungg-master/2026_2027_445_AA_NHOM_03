<?php

use Illuminate\Database\Migrations\Migration;

// Historical duplicate: the canonical table is owned by its 15/09 migration.
// Retain this name for existing ledgers; rollback must not drop another migration's table.
return new class extends Migration
{
    public function up(): void {}

    public function down(): void {}
};
