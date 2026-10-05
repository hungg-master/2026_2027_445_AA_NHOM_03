<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\LopHoc;
use Database\Seeders\AdminSeeder;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\DemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SchemaSetupTest extends TestCase
{
    use RefreshDatabase;

    public function test_migrations_build_complete_schema_and_can_be_run_again(): void
    {
        $this->artisan('migrate', ['--force' => true])->assertExitCode(0);
        foreach (['mon_hocs', 'phong_hocs', 'lop_hocs', 'thoi_gian_ranhs', 'dang_ky_lops', 'trial_bookings', 'phong_hops'] as $table) {
            $this->assertTrue(Schema::hasTable($table), $table);
        }
        $this->assertTrue(Schema::hasColumns('lop_hocs', ['si_so_toi_da', 'id_giao_vien', 'thoi_gian_bat_dau']));
        $this->assertTrue(Schema::hasColumns('hoc_viens', ['face_id_photo_path', 'du_lieu_khuon_mat']));
        $this->assertTrue(Schema::hasColumns('giao_viens', ['face_id_photo_path', 'du_lieu_khuon_mat']));
    }

    public function test_seeding_does_not_create_or_change_an_admin_password(): void
    {
        $this->seed(AdminSeeder::class);
        $this->assertDatabaseCount('admins', 0);
        $admin = Admin::create(['ho_ten' => 'Existing', 'email' => 'owner@example.test', 'password' => 'existing-long-password']);
        $hash = $admin->password;
        $this->seed(AdminSeeder::class);
        $this->assertSame($hash, $admin->fresh()->password);
        $this->assertDatabaseCount('admins', 1);
    }

    public function test_explicit_demo_seed_respects_class_capacity_rules(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->assertDatabaseCount('hoc_viens', 0);
        $this->assertDatabaseCount('giao_viens', 0);
        $this->seed(DemoSeeder::class);
        $this->assertDatabaseCount('admins', 0);
        $this->assertGreaterThan(0, LopHoc::count());
        foreach (LopHoc::all() as $class) {
            $this->assertGreaterThan(0, $class->buoiHocs()->count(), 'Demo class needs dated lessons');
            if ($class->hinh_thuc === 'online') {
                $this->assertNotNull($class->phongHop, 'Online demo class needs its room');
                $this->assertSame($class->si_so_toi_da + 1, $class->phongHop->so_nguoi_toi_da);
            } else {
                $this->assertNull($class->phongHop);
            }
            $this->assertGreaterThanOrEqual(1, $class->si_so_toi_da);
            $this->assertLessThanOrEqual($class->loai_lop === 'kem' ? 5 : 30, $class->si_so_toi_da);
        }
    }
}
