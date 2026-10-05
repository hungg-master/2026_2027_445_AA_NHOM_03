<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supporting_write_locks', function (Blueprint $table) {
            $table->unsignedInteger('id')->primary();
            $table->unsignedBigInteger('revision')->default(0);
        });
        DB::table('supporting_write_locks')->insert(['id' => 1, 'revision' => 0]);
        Schema::create('account_password_resets', function (Blueprint $table) {
            $table->id();
            $table->string('role', 20);
            $table->string('email');
            $table->string('token_hash', 64);
            $table->timestamp('expires_at');
            $table->timestamp('consumed_at')->nullable();
            $table->timestamps();
            $table->unique(['role', 'email']);
        });
        Schema::create('payment_obligations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_dang_ky_lop')->unique()->constrained('dang_ky_lops')->restrictOnDelete();
            $table->foreignId('id_hoc_vien')->constrained('hoc_viens')->restrictOnDelete();
            $table->foreignId('id_giao_vien')->constrained('giao_viens')->restrictOnDelete();
            $table->foreignId('id_lop_hoc')->constrained('lop_hocs')->restrictOnDelete();
            $table->unsignedBigInteger('amount');
            $table->string('currency', 3)->default('VND');
            $table->string('status', 20)->default('pending');
            $table->timestamps();
        });
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('obligation_id')->constrained('payment_obligations')->restrictOnDelete();
            $table->string('idempotency_key', 100)->unique();
            $table->string('settlement_key', 100)->nullable()->unique();
            $table->string('reference', 150)->nullable()->unique();
            $table->unsignedBigInteger('amount');
            $table->string('mode', 10)->default('test');
            $table->string('status', 30);
            $table->foreignId('confirmed_by')->nullable()->constrained('admins')->restrictOnDelete();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamps();
        });
        Schema::create('teacher_bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_giao_vien')->constrained('giao_viens')->cascadeOnDelete();
            $table->string('bank_name', 150);
            $table->string('account_holder', 150);
            $table->text('account_number_encrypted');
            $table->string('account_last_four', 4);
            $table->boolean('is_current')->default(true);
            $table->timestamps();
        });
        Schema::create('lesson_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_buoi_hoc')->constrained('buoi_hocs')->restrictOnDelete();
            $table->foreignId('id_hoc_vien')->constrained('hoc_viens')->restrictOnDelete();
            $table->foreignId('id_giao_vien')->constrained('giao_viens')->restrictOnDelete();
            $table->unsignedTinyInteger('rating');
            $table->text('comment')->nullable();
            $table->timestamps();
            $table->unique(['id_buoi_hoc', 'id_hoc_vien']);
        });
        Schema::create('consultation_conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_hoc_vien')->constrained('hoc_viens')->cascadeOnDelete();
            $table->foreignId('id_giao_vien')->constrained('giao_viens')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['id_hoc_vien', 'id_giao_vien']);
        });
        Schema::create('consultation_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('consultation_conversations')->cascadeOnDelete();
            $table->string('sender_role', 20);
            $table->unsignedBigInteger('sender_id');
            $table->text('body');
            $table->timestamps();
            $table->index(['conversation_id', 'id']);
        });
        Schema::create('ai_daily_usage', function (Blueprint $table) {
            $table->id();
            $table->string('actor_role', 20);
            $table->unsignedBigInteger('actor_id');
            $table->date('usage_date');
            $table->unsignedInteger('requests')->default(0);
            $table->timestamps();
            $table->unique(['actor_role', 'actor_id', 'usage_date']);
        });
        Schema::create('business_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('dedupe_key')->unique();
            $table->string('recipient');
            $table->string('event_type', 50);
            $table->text('body');
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('claimed_at')->nullable();
            $table->string('claim_token', 36)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['business_notifications', 'ai_daily_usage', 'consultation_messages', 'consultation_conversations', 'lesson_reviews', 'teacher_bank_accounts', 'payment_transactions', 'payment_obligations', 'account_password_resets', 'supporting_write_locks'] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
