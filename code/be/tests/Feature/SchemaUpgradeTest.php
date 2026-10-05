<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SchemaUpgradeTest extends TestCase
{
    public function test_session_upgrade_preserves_old_classes_enrollments_and_samples(): void
    {
        config(['database.connections.upgrade' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '', 'foreign_key_constraints' => true,
            'transaction_mode' => 'IMMEDIATE', 'busy_timeout' => 5000,
        ]]);
        $oldPaths = array_values(array_filter(glob(database_path('migrations/*.php')), fn ($path) => basename($path) < '2026_10_04_100000'));
        $this->artisan('migrate', ['--database' => 'upgrade', '--path' => $oldPaths, '--realpath' => true, '--force' => true])->assertSuccessful();
        $db = DB::connection('upgrade');
        $teacher = $db->table('giao_viens')->insertGetId(['ho_ten' => 'Legacy teacher', 'email' => 'teacher@example.test', 'password' => 'existing-hash', 'du_lieu_khuon_mat' => '[0.01]']);
        $student = $db->table('hoc_viens')->insertGetId(['ho_ten' => 'Legacy student', 'email' => 'student@example.test', 'password' => 'existing-student-hash']);
        $subject = $db->table('mon_hocs')->insertGetId(['ten_mon_hoc' => 'Legacy subject']);
        $class = $db->table('lop_hocs')->insertGetId(['id_giao_vien' => $teacher, 'id_mon_hoc' => $subject,
            'loai_lop' => 'kem', 'hinh_thuc' => 'online', 'si_so_toi_da' => 3,
            'thoi_gian_bat_dau' => '2026-10-11 09:00:00', 'thoi_gian_ket_thuc' => '2026-10-11 10:00:00']);
        $enrollment = $db->table('dang_ky_lops')->insertGetId(['id_lop_hoc' => $class, 'id_hoc_vien' => $student, 'trang_thai' => 'da_xac_nhan']);
        $this->artisan('migrate', ['--database' => 'upgrade', '--path' => [database_path('migrations/2026_10_04_100000_add_sessions_and_identity_proofs.php')], '--realpath' => true, '--force' => true])->assertSuccessful();
        $this->assertSame(1, $db->table('lop_hocs')->count());
        $this->assertSame('da_xac_nhan', $db->table('dang_ky_lops')->find($enrollment)->trang_thai);
        $this->assertSame('existing-hash', $db->table('giao_viens')->find($teacher)->password);
        $this->assertSame('[0.01]', $db->table('giao_viens')->find($teacher)->du_lieu_khuon_mat);
        $this->assertSame(1, $db->table('buoi_hocs')->count());
        $this->assertSame($class, $db->table('buoi_hocs')->first()->id_lop_hoc);
        $this->assertSame('2026-10-11 09:00:00', $db->table('buoi_hocs')->first()->thoi_gian_bat_dau);
        DB::purge('upgrade');
    }
}
