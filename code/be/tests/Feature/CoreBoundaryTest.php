<?php

namespace Tests\Feature;

use App\Models\GiaoVien;
use App\Models\HocVien;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Support\CoreFixtures;
use Tests\TestCase;

class CoreBoundaryTest extends TestCase
{
    use CoreFixtures, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->prepareCoreFixtures();
    }

    public function test_teacher_registration_ignores_injected_approval_approver_and_biometric_data(): void
    {
        $this->postJson('/api/giao-vien/register', [
            'ho_ten' => 'New teacher', 'email' => 'new-teacher@example.test',
            'password' => 'test-password', 're_password' => 'test-password', 'so_dien_thoai' => '0901234567',
            'trang_thai_duyet' => 'da_duyet', 'giao_vien_da_duyet' => 999,
            'du_lieu_khuon_mat' => json_encode($this->coreDescriptor()),
            'face_id_photo_path' => 'private/injected.png', 'is_block' => 1, 'tinh_trang' => 0, 'is_active' => 0,
        ])->assertSuccessful()->assertJsonPath('status', true);
        $teacher = GiaoVien::where('email', 'new-teacher@example.test')->firstOrFail();
        $this->assertSame('cho_duyet', $teacher->trang_thai_duyet);
        $this->assertNull($teacher->giao_vien_da_duyet);
        $this->assertEmpty($teacher->du_lieu_khuon_mat);
        $this->assertEmpty($teacher->face_id_photo_path);
        $this->assertSame(0, (int) $teacher->is_block);
        $this->assertSame(1, (int) $teacher->tinh_trang);
        $this->assertSame(1, (int) $teacher->is_active);
    }

    public function test_student_registration_ignores_biometric_injection(): void
    {
        $this->postJson('/api/hoc-vien/register', [
            'ho_ten' => 'New student', 'email' => 'new-student@example.test',
            'password' => 'test-password', 're_password' => 'test-password', 'so_dien_thoai' => '0901234567',
            'du_lieu_khuon_mat' => json_encode($this->coreDescriptor()),
            'face_id_photo_path' => 'private/injected.png',
        ])->assertSuccessful();
        $student = HocVien::where('email', 'new-student@example.test')->firstOrFail();
        $this->assertEmpty($student->du_lieu_khuon_mat);
        $this->assertEmpty($student->face_id_photo_path);
        $this->assertDatabaseCount('face_verifications', 0);
    }

    public function test_authentication_database_failure_returns_generic_error_without_sql_or_parameters(): void
    {
        $student = $this->coreStudent();
        DB::unprepared("CREATE TRIGGER fail_core_login_token_insert BEFORE INSERT ON personal_access_tokens BEGIN SELECT RAISE(ABORT, 'forced auth failure'); END");
        $this->postJson('/api/hoc-vien/login', ['email' => $student->email, 'password' => 'test-password'])
            ->assertServerError()->assertJsonPath('status', false)
            ->assertDontSee('forced auth failure')->assertDontSee('SQLSTATE')->assertDontSee('personal_access_tokens');
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_face_sample_replacement_requires_password_and_invalidates_old_proofs(): void
    {
        $student = $this->coreStudent();
        $class = $this->coreClass();
        $proof = $this->coreProof($student, 'enrollment', $class->id);
        $oldSample = $student->fresh()->du_lieu_khuon_mat;
        $replacement = $this->coreDescriptor(1.0);
        $this->asCoreActor($student)->postJson('/api/hoc-vien/face-id/sample', [
            'descriptor' => $replacement,
        ])->assertUnprocessable();
        $this->assertSame($oldSample, $student->fresh()->du_lieu_khuon_mat);
        $this->asCoreActor($student)->postJson('/api/hoc-vien/face-id/sample', [
            'descriptor' => $replacement, 'current_password' => 'test-password',
        ])->assertSuccessful()->assertJsonPath('data.has_face_id', true);
        $this->assertCoreRejected($this->asCoreActor($student)->postJson('/api/hoc-vien/dang-ky-lop-hoc', [
            'id_lop_hoc' => $class->id, 'verification_id' => $proof['verification_id'],
        ]));
        $this->assertDatabaseCount('dang_ky_lops', 0);
    }

    public function test_public_teacher_and_class_catalogs_exclude_disabled_teachers(): void
    {
        foreach ([['is_block' => 1], ['tinh_trang' => 0], ['is_active' => 0]] as $disabled) {
            $teacher = $this->coreTeacher($disabled);
            $class = $this->coreClass($teacher);
            $this->getJson('/api/client/giao-vien/data')->assertOk()->assertJsonPath('data', []);
            $this->getJson('/api/client/lop-hoc/data')->assertOk()->assertJsonPath('data.data', []);
            $this->getJson('/api/client/giao-vien/chi-tiet/'.$teacher->id)->assertNotFound();
            $this->getJson('/api/client/lop-hoc/'.$class->id)->assertNotFound();
        }
    }

    public static function disabledAccounts(): array
    {
        return [
            'blocked student' => ['hoc-vien', 'is_block', 1],
            'suspended student' => ['hoc-vien', 'tinh_trang', 0],
            'inactive student' => ['hoc-vien', 'is_active', 0],
            'blocked teacher' => ['giao-vien', 'is_block', 1],
            'suspended teacher' => ['giao-vien', 'tinh_trang', 0],
            'inactive teacher' => ['giao-vien', 'is_active', 0],
        ];
    }

    #[DataProvider('disabledAccounts')]
    public function test_disabled_account_cannot_login_or_use_an_existing_token(string $role, string $field, int $value): void
    {
        $actor = $role === 'hoc-vien' ? $this->coreStudent() : $this->coreTeacher();
        $token = $actor->createToken('issued-before-account-disabled')->plainTextToken;
        $actor->update([$field => $value]);
        Auth::forgetGuards();
        $this->withToken($token)->getJson('/api/'.$role.'/profile/data')->assertForbidden();
        Auth::forgetGuards();
        $this->withToken('')->postJson('/api/'.$role.'/login', [
            'email' => $actor->email, 'password' => 'test-password',
        ])->assertForbidden()->assertJsonMissingPath('token');
        $this->assertDatabaseCount('personal_access_tokens', 1);
    }

    private function sessionFixture(string $format = 'online', string $end = '2026-10-04 09:00:00'): array
    {
        $teacher = $this->coreTeacher();
        $student = $this->coreStudent();
        $class = $this->coreClass($teacher, attributes: [
            'hinh_thuc' => $format, 'id_phong_hoc' => $format === 'offline' ? $this->corePhysicalRoom()->id : null,
            'thoi_gian_bat_dau' => '2026-10-04 07:00:00', 'thoi_gian_ket_thuc' => $end, 'tinh_trang' => 'dang_hoc',
        ]);
        $session = $this->coreSession($class);
        $this->coreEnrollment($student, $class);
        $room = $format === 'online' ? $this->coreRoom($class) : null;

        return [$teacher, $student, $class, $session, $room];
    }

    private function roomGrant(HocVien|GiaoVien $actor, int $session): string
    {
        config([
            'services.livekit.url' => 'wss://video.example.test',
            'services.livekit.api_key' => 'test-room-key',
            'services.livekit.api_secret' => 'test-room-secret-that-stays-in-tests',
        ]);
        $proof = $this->coreProof($actor, 'room', $session);

        return $this->asCoreActor($actor)->postJson('/api/phong-hop/tao-token', [
            'id_buoi_hoc' => $session, 'verification_id' => $proof['verification_id'],
        ])->assertSuccessful()->json('data.attendance_token');
    }

    public function test_attendance_grant_cannot_be_replayed_after_first_use(): void
    {
        [, $student, , $session] = $this->sessionFixture();
        $grant = $this->roomGrant($student, $session->id);
        $payload = ['id_buoi_hoc' => $session->id, 'attendance_token' => $grant];
        $this->asCoreActor($student)->postJson('/api/chi-tiet-phong-hop/create', $payload)->assertSuccessful();
        $this->assertCoreRejected($this->asCoreActor($student)->postJson('/api/chi-tiet-phong-hop/create', $payload));
        $this->assertDatabaseCount('chi_tiet_phong_hops', 1);
    }

    public function test_room_time_window_opens_fifteen_minutes_before_start_and_closes_at_end(): void
    {
        [, $student, , $session] = $this->sessionFixture();
        Carbon::setTestNow(Carbon::parse('2026-10-04 06:44:30', 'Asia/Ho_Chi_Minh'));
        $proof = $this->coreProof($student, 'room', $session->id);
        config(['services.livekit.url' => 'wss://video.example.test', 'services.livekit.api_key' => 'test-key', 'services.livekit.api_secret' => 'test-secret']);
        $payload = ['id_buoi_hoc' => $session->id, 'verification_id' => $proof['verification_id']];
        Carbon::setTestNow(Carbon::parse('2026-10-04 06:44:59', 'Asia/Ho_Chi_Minh'));
        $this->asCoreActor($student)->postJson('/api/phong-hop/tao-token', $payload)->assertStatus(409);
        Carbon::setTestNow(Carbon::parse('2026-10-04 06:45:00', 'Asia/Ho_Chi_Minh'));
        $this->asCoreActor($student)->postJson('/api/phong-hop/tao-token', $payload)->assertSuccessful();
        Carbon::setTestNow(Carbon::parse('2026-10-04 08:59:30', 'Asia/Ho_Chi_Minh'));
        $grant = $this->roomGrant($student, $session->id);
        Carbon::setTestNow(Carbon::parse('2026-10-04 09:00:00', 'Asia/Ho_Chi_Minh'));
        $this->asCoreActor($student)->postJson('/api/chi-tiet-phong-hop/create', [
            'id_buoi_hoc' => $session->id, 'attendance_token' => $grant,
        ])->assertStatus(409);
        $this->assertDatabaseCount('chi_tiet_phong_hops', 0);
    }

    public function test_session_completion_rejects_outsider_and_does_not_complete_early(): void
    {
        [$teacher, $student, , $session] = $this->sessionFixture('offline');
        $stranger = $this->coreTeacher();
        $url = '/api/giao-vien/buoi-hoc/'.$session->id.'/complete';
        $this->asCoreActor($stranger)->postJson($url, ['attended_student_ids' => [$student->id]])->assertNotFound();
        $this->asCoreActor($student)->postJson($url, ['attended_student_ids' => [$student->id]])->assertUnauthorized();
        $this->asCoreActor($teacher)->postJson($url, ['attended_student_ids' => [$student->id]])->assertStatus(409);
        $this->assertSame('scheduled', $session->fresh()->trang_thai);
        $this->assertDatabaseCount('chi_tiet_phong_hops', 0);
    }

    public function test_offline_completion_creates_typed_teacher_attested_attendance_for_selected_enrolled_students(): void
    {
        [$teacher, $student, $class, $session] = $this->sessionFixture('offline', '2026-10-04 08:00:00');
        $absent = $this->coreStudent();
        $this->coreEnrollment($absent, $class);
        $this->assertSame($teacher->id, $student->id);
        $this->asCoreActor($teacher)->postJson('/api/giao-vien/buoi-hoc/'.$session->id.'/complete', [
            'attended_student_ids' => [$student->id],
        ])->assertSuccessful();
        $this->assertSame('completed', $session->fresh()->trang_thai);
        foreach ([[$teacher, 'giao_vien'], [$student, 'hoc_vien']] as [$actor, $role]) {
            $this->assertDatabaseHas('chi_tiet_phong_hops', [
                'id_buoi_hoc' => $session->id, 'id_nguoi_dung' => $actor->id, 'loai_nguoi_dung' => $role,
                'is_active' => 0, 'attendance_source' => 'teacher_attested', 'xac_thuc_khuon_mat' => 0,
            ]);
        }
        $this->assertDatabaseMissing('chi_tiet_phong_hops', ['id_buoi_hoc' => $session->id, 'id_nguoi_dung' => $absent->id, 'loai_nguoi_dung' => 'hoc_vien']);
        $this->assertDatabaseCount('chi_tiet_phong_hops', 2);
        $this->assertDatabaseCount('phong_hops', 0);
    }

    public function test_offline_completion_rejects_unenrolled_student_without_partial_attendance(): void
    {
        [$teacher, $student, , $session] = $this->sessionFixture('offline', '2026-10-04 08:00:00');
        $outsider = $this->coreStudent();
        $this->asCoreActor($teacher)->postJson('/api/giao-vien/buoi-hoc/'.$session->id.'/complete', [
            'attended_student_ids' => [$student->id, $outsider->id],
        ])->assertUnprocessable();
        $this->assertSame('scheduled', $session->fresh()->trang_thai);
        $this->assertDatabaseCount('chi_tiet_phong_hops', 0);
    }

    public function test_offline_completion_failure_rolls_back_session_and_all_attendance(): void
    {
        [$teacher, $student, , $session] = $this->sessionFixture('offline', '2026-10-04 08:00:00');
        DB::unprepared("CREATE TRIGGER fail_core_completion_attendance BEFORE INSERT ON chi_tiet_phong_hops WHEN NEW.loai_nguoi_dung = 'hoc_vien' BEGIN SELECT RAISE(ABORT, 'forced completion failure'); END");
        $this->asCoreActor($teacher)->postJson('/api/giao-vien/buoi-hoc/'.$session->id.'/complete', [
            'attended_student_ids' => [$student->id],
        ])->assertServerError()->assertDontSee('forced completion failure');
        $this->assertSame('scheduled', $session->fresh()->trang_thai);
        $this->assertDatabaseCount('chi_tiet_phong_hops', 0);
    }

    public function test_online_completion_preserves_only_existing_verified_attendance(): void
    {
        [$teacher, $student, $class, $session] = $this->sessionFixture();
        $absent = $this->coreStudent();
        $this->coreEnrollment($absent, $class);
        $grant = $this->roomGrant($student, $session->id);
        $this->asCoreActor($student)->postJson('/api/chi-tiet-phong-hop/create', [
            'id_buoi_hoc' => $session->id, 'attendance_token' => $grant,
        ])->assertSuccessful();
        Carbon::setTestNow(Carbon::parse('2026-10-04 09:00:00', 'Asia/Ho_Chi_Minh'));
        $this->asCoreActor($teacher)->postJson('/api/giao-vien/buoi-hoc/'.$session->id.'/complete', [
            'attended_student_ids' => [$student->id, $absent->id],
        ])->assertSuccessful();
        $this->assertSame('completed', $session->fresh()->trang_thai);
        $this->assertDatabaseHas('chi_tiet_phong_hops', [
            'id_buoi_hoc' => $session->id, 'id_nguoi_dung' => $student->id,
            'loai_nguoi_dung' => 'hoc_vien', 'xac_thuc_khuon_mat' => 1, 'is_active' => 0,
        ]);
        $this->assertDatabaseMissing('chi_tiet_phong_hops', ['id_buoi_hoc' => $session->id, 'id_nguoi_dung' => $absent->id, 'loai_nguoi_dung' => 'hoc_vien']);
        $this->assertDatabaseMissing('chi_tiet_phong_hops', ['id_buoi_hoc' => $session->id, 'loai_nguoi_dung' => 'giao_vien']);
        $this->assertDatabaseCount('chi_tiet_phong_hops', 1);
    }
}
