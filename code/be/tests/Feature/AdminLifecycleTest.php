<?php

namespace Tests\Feature;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\Support\CoreFixtures;
use Tests\TestCase;

class AdminLifecycleTest extends TestCase
{
    use CoreFixtures, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->prepareCoreFixtures();
    }

    public function test_admin_cancellation_updates_all_class_dependents_and_is_idempotent(): void
    {
        $admin = Admin::create(['ho_ten' => 'Administrator', 'email' => 'admin@example.test', 'password' => 'test-password', 'tinh_trang' => 1]);
        $class = $this->coreClass();
        $session = $this->coreSession($class);
        $room = $this->coreRoom($class);
        $enrollment = $this->coreEnrollment($this->coreStudent(), $class);
        foreach ([1, 2] as $attempt) {
            Auth::forgetGuards();
            $this->withToken($admin->createToken('test')->plainTextToken)->postJson('/api/admin/lop-hoc/duyet', ['id' => $class->id, 'tinh_trang' => 'da_huy'])->assertSuccessful();
        }
        $this->assertSame('cancelled', $session->fresh()->trang_thai);
        $this->assertSame('da_huy', $enrollment->fresh()->trang_thai);
        $this->assertSame(0, $room->fresh()->trang_thai);
        Auth::forgetGuards();
        $this->withToken($admin->createToken('test')->plainTextToken)->postJson('/api/admin/lop-hoc/duyet', ['id' => $class->id, 'tinh_trang' => 'dang_mo'])->assertStatus(409);
        $this->assertSame('da_huy', $class->fresh()->tinh_trang);
    }

    public function test_registration_normalizes_email_and_rejects_case_variants(): void
    {
        $data = ['ho_ten' => 'New Student', 'email' => 'Mixed.Case@Example.Test', 'password' => 'test-password', 're_password' => 'test-password', 'so_dien_thoai' => '0901234567'];
        $this->postJson('/api/hoc-vien/register', $data)->assertSuccessful();
        $this->assertDatabaseHas('hoc_viens', ['email' => 'mixed.case@example.test']);
        $data['email'] = 'MIXED.CASE@EXAMPLE.TEST';
        $this->postJson('/api/hoc-vien/register', $data)->assertUnprocessable()->assertJsonValidationErrors('email');
        $this->assertDatabaseCount('hoc_viens', 1);
    }
}
