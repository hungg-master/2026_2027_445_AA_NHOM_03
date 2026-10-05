<?php

namespace Tests\Feature;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Tests\Support\CoreFixtures;
use Tests\TestCase;

class SupportingFinanceReviewTest extends TestCase
{
    use CoreFixtures, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->prepareCoreFixtures();
    }

    public function test_test_payment_requires_admin_confirmation_and_duplicate_requests_do_not_double_count(): void
    {
        config(['supporting.payments.mode' => 'test']);
        $student = $this->coreStudent();
        $teacher = $this->coreTeacher();
        $class = $this->coreClass($teacher);
        $enrollment = $this->coreEnrollment($student, $class);
        $obligation = $this->asCoreActor($student)->getJson('/api/hoc-vien/hoc-phi')->assertOk()->json('data.0.id');
        $this->assertNotNull($obligation);
        $request = $this->postJson('/api/hoc-vien/hoc-phi/'.$obligation.'/test-payment', ['idempotency_key' => 'student-attempt-001', 'outcome' => 'success'])->assertCreated();
        $request->assertJsonPath('data.status', 'awaiting_manual');
        $transaction = $request->json('data.id');
        $competing = $this->postJson('/api/hoc-vien/hoc-phi/'.$obligation.'/test-payment', ['idempotency_key' => 'student-attempt-002', 'outcome' => 'success'])->assertCreated()->json('data.id');
        $this->postJson('/api/hoc-vien/hoc-phi/'.$obligation.'/test-payment', ['idempotency_key' => 'student-attempt-001', 'outcome' => 'success'])->assertSuccessful()->assertJsonPath('data.id', $transaction);
        $this->getJson('/api/hoc-vien/thong-ke-tai-chinh')->assertOk()->assertJsonPath('data.confirmed_amount', 0);
        $this->postJson('/api/admin/thanh-toan/'.$transaction.'/settle-test', ['idempotency_key' => 'admin-settle-001', 'reference' => 'TEST-REF'])->assertStatus(401);
        $admin = Admin::create(['ho_ten' => 'Admin', 'email' => 'admin@example.test', 'password' => 'test-password', 'tinh_trang' => 1]);
        Auth::forgetGuards();
        $this->withToken($admin->createToken('test')->plainTextToken);
        $this->getJson('/api/admin/thanh-toan')->assertOk()->assertJsonPath('data.0.currency', 'VND');
        $this->postJson('/api/admin/thanh-toan/'.$transaction.'/settle-test', ['idempotency_key' => 'admin-settle-001', 'reference' => 'TEST-REF'])->assertOk()->assertJsonPath('data.status', 'confirmed');
        $this->postJson('/api/admin/thanh-toan/'.$transaction.'/settle-test', ['idempotency_key' => 'admin-settle-001', 'reference' => 'TEST-REF'])->assertOk();
        $this->postJson('/api/admin/thanh-toan/'.$competing.'/settle-test', ['idempotency_key' => 'admin-settle-002', 'reference' => 'TEST-REF-2'])->assertStatus(409);
        $this->postJson('/api/admin/thanh-toan/'.$transaction.'/settle-test', ['idempotency_key' => 'admin-settle-changed', 'reference' => 'OTHER-REF'])->assertStatus(409);
        $this->getJson('/api/admin/thong-ke-tai-chinh')->assertOk()->assertJsonPath('data.confirmed_amount', 100000)->assertJsonPath('data.confirmed_count', 1);
        $this->asCoreActor($teacher)->getJson('/api/giao-vien/thong-ke-tai-chinh')->assertOk()->assertJsonPath('data.confirmed_amount', 100000);
        $this->assertSame('da_xac_nhan', $enrollment->fresh()->trang_thai);
        $this->assertDatabaseCount('payment_transactions', 2);
        $this->assertDatabaseHas('payment_obligations', ['id' => $obligation, 'status' => 'paid']);
    }

    public function test_payment_cannot_read_other_student_and_failed_attempt_never_confirms(): void
    {
        $student = $this->coreStudent();
        $other = $this->coreStudent();
        $this->coreEnrollment($student, $this->coreClass());
        $id = $this->asCoreActor($student)->getJson('/api/hoc-vien/hoc-phi')->assertOk()->json('data.0.id');
        $failedId = $this->postJson('/api/hoc-vien/hoc-phi/'.$id.'/test-payment', ['idempotency_key' => 'failed-attempt-001', 'outcome' => 'failed'])->assertCreated()->assertJsonPath('data.status', 'failed')->json('data.id');
        $this->postJson('/api/hoc-vien/hoc-phi/'.$id.'/test-payment', ['idempotency_key' => 'failed-attempt-001', 'outcome' => 'failed'])->assertOk()->assertJsonPath('data.id', $failedId);
        $this->postJson('/api/hoc-vien/hoc-phi/'.$id.'/test-payment', ['idempotency_key' => 'failed-attempt-001', 'outcome' => 'success'])->assertStatus(409);
        $this->assertDatabaseCount('payment_transactions', 1);
        $this->asCoreActor($other)->getJson('/api/hoc-vien/hoc-phi')->assertOk()->assertJsonCount(0, 'data');
        $this->postJson('/api/hoc-vien/hoc-phi/'.$id.'/test-payment', ['idempotency_key' => 'forged-attempt-001', 'outcome' => 'success'])->assertStatus(404);
        config(['supporting.payments.mode' => 'disabled']);
        $this->asCoreActor($student)->postJson('/api/hoc-vien/hoc-phi/'.$id.'/test-payment', ['idempotency_key' => 'disabled-attempt-001', 'outcome' => 'success'])->assertStatus(503);
    }

    public function test_review_requires_own_completed_attended_session_and_is_unique(): void
    {
        $student = $this->coreStudent();
        $teacher = $this->coreTeacher();
        $class = $this->coreClass($teacher);
        $session = $this->coreSession($class, ['trang_thai' => 'completed', 'thoi_gian_bat_dau' => now()->subHours(2), 'thoi_gian_ket_thuc' => now()->subHour()]);
        $this->coreEnrollment($student, $class);
        $payload = ['id_buoi_hoc' => $session->id, 'rating' => 5, 'comment' => 'Useful lesson'];
        $this->asCoreActor($student)->postJson('/api/hoc-vien/danh-gia', $payload)->assertStatus(403);
        DB::table('chi_tiet_phong_hops')->insert(['id_phong_hop' => $this->coreRoom($class)->id, 'id_buoi_hoc' => $session->id,
            'id_nguoi_dung' => $student->id, 'loai_nguoi_dung' => 'giao_vien', 'xac_thuc_khuon_mat' => 1, 'created_at' => now(), 'updated_at' => now()]);
        $this->postJson('/api/hoc-vien/danh-gia', $payload)->assertStatus(403);
        DB::table('chi_tiet_phong_hops')->where('id_buoi_hoc', $session->id)->update(['loai_nguoi_dung' => 'hoc_vien']);
        $this->postJson('/api/hoc-vien/danh-gia', $payload)->assertCreated()->assertJsonPath('data.rating', 5);
        $this->postJson('/api/hoc-vien/danh-gia', $payload)->assertStatus(409);
        $this->asCoreActor($teacher)->getJson('/api/giao-vien/danh-gia')->assertOk()->assertJsonCount(1, 'data');
        $this->asCoreActor($this->coreStudent())->postJson('/api/hoc-vien/danh-gia', $payload)->assertStatus(403);
        $this->asCoreActor($student)->getJson('/api/giao-vien/danh-gia')->assertStatus(403);
        $this->assertDatabaseCount('lesson_reviews', 1);
    }

    public function test_offline_review_accepts_teacher_attestation_but_it_cannot_validate_online_attendance(): void
    {
        $student = $this->coreStudent();
        $class = $this->coreClass(null, null, ['hinh_thuc' => 'offline', 'id_phong_hoc' => $this->corePhysicalRoom()->id]);
        $session = $this->coreSession($class, ['trang_thai' => 'completed', 'thoi_gian_bat_dau' => now()->subHours(2), 'thoi_gian_ket_thuc' => now()->subHour()]);
        $this->coreEnrollment($student, $class);
        DB::table('chi_tiet_phong_hops')->insert(['id_phong_hop' => null, 'id_buoi_hoc' => $session->id,
            'id_nguoi_dung' => $student->id, 'loai_nguoi_dung' => 'hoc_vien', 'attendance_source' => 'teacher_attested',
            'xac_thuc_khuon_mat' => 0, 'created_at' => now(), 'updated_at' => now()]);
        $this->asCoreActor($student)->postJson('/api/hoc-vien/danh-gia', ['id_buoi_hoc' => $session->id, 'rating' => 4])->assertCreated();
        $online = $this->coreClass();
        $onlineSession = $this->coreSession($online, ['trang_thai' => 'completed', 'thoi_gian_bat_dau' => now()->subHours(2), 'thoi_gian_ket_thuc' => now()->subHour()]);
        $this->coreEnrollment($student, $online);
        DB::table('chi_tiet_phong_hops')->insert(['id_phong_hop' => $this->coreRoom($online)->id, 'id_buoi_hoc' => $onlineSession->id,
            'id_nguoi_dung' => $student->id, 'loai_nguoi_dung' => 'hoc_vien', 'attendance_source' => 'teacher_attested',
            'xac_thuc_khuon_mat' => 0, 'created_at' => now(), 'updated_at' => now()]);
        $this->postJson('/api/hoc-vien/danh-gia', ['id_buoi_hoc' => $onlineSession->id, 'rating' => 4])->assertStatus(403);
        $this->assertDatabaseCount('lesson_reviews', 1);
    }
}
