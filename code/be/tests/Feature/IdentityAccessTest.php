<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\Support\CoreFixtures;
use Tests\TestCase;

class IdentityAccessTest extends TestCase
{
    use CoreFixtures, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->prepareCoreFixtures();
    }

    public function test_anonymous_legacy_vector_room_and_attendance_routes_are_denied(): void
    {
        $student = $this->coreStudent();
        $original = $student->du_lieu_khuon_mat;
        $routes = [
            ['POST', '/api/nguoi-dung/xac-thuc-khuon-mat', ['id_hoc_vien' => $student->id, 'du_lieu_khuon_mat' => $this->coreDescriptor()]],
            ['POST', '/api/phong-hop/create', ['ten_phong' => 'Forged room', 'id_chu_phong' => 1]],
            ['POST', '/api/phong-hop/kiem-tra-phong-hop', ['ma_phong' => 'EDU-999']],
            ['POST', '/api/phong-hop/tao-token', ['ma_phong' => 'FORGED']],
            ['POST', '/api/chi-tiet-phong-hop/create', ['id_phong_hop' => 1, 'id_nguoi_dung' => $student->id, 'xac_thuc_khuon_mat' => 1]],
            ['GET', '/api/phong-hop/lich-su-tham-gia', []],
            ['GET', '/api/nguoi-dung/phong-hop-lien-quan', []],
        ];
        foreach ($routes as [$method, $route, $payload]) {
            Auth::forgetGuards();
            $this->json($method, $route, $payload)->assertUnauthorized();
        }
        $this->assertSame($original, $student->fresh()->du_lieu_khuon_mat);
        $this->assertDatabaseCount('phong_hops', 0);
        $this->assertDatabaseCount('chi_tiet_phong_hops', 0);
    }

    public function test_pending_teacher_can_read_profile_but_cannot_create_a_class(): void
    {
        $teacher = $this->coreTeacher(['trang_thai_duyet' => 'cho_duyet']);
        $this->asCoreActor($teacher)->getJson('/api/giao-vien/profile/data')
            ->assertOk()->assertJsonPath('data.id', $teacher->id);
        $this->asCoreActor($teacher)->postJson('/api/giao-vien/face-id/sample', [
            'descriptor' => $this->coreDescriptor(),
        ])->assertSuccessful();
        $this->asCoreActor($teacher)->postJson('/api/giao-vien/lop-hoc',
            $this->coreClassPayload($this->coreSubject(), ['link_online' => 'https://example.test/meeting'])
        )->assertForbidden();
        $this->assertDatabaseCount('lop_hocs', 0);
        $this->assertDatabaseCount('phong_hops', 0);
    }

    public function test_public_class_detail_never_exposes_teacher_biometric_data(): void
    {
        $teacher = $this->coreTeacher(['du_lieu_khuon_mat' => json_encode($this->coreDescriptor())]);
        $teacher->face_id_photo_path = 'private/teacher-face.png';
        $teacher->save();
        $class = $this->coreClass($teacher);
        $this->getJson('/api/client/lop-hoc/'.$class->id)->assertOk()
            ->assertJsonPath('data.giao_vien.id', $teacher->id)
            ->assertJsonMissingPath('data.giao_vien.du_lieu_khuon_mat')
            ->assertJsonMissingPath('data.giao_vien.face_id_photo_path');
        $this->assertArrayNotHasKey('du_lieu_khuon_mat', $teacher->fresh()->toArray());
    }

    public function test_pending_teachers_are_not_available_through_public_class_detail(): void
    {
        $class = $this->coreClass($this->coreTeacher(['trang_thai_duyet' => 'cho_duyet']));
        $this->getJson('/api/client/lop-hoc/'.$class->id)->assertNotFound();
    }

    public function test_sample_is_bound_to_token_type_and_id_despite_submitted_account_ids(): void
    {
        $student = $this->coreStudent();
        $teacher = $this->coreTeacher();
        $this->assertSame($student->id, $teacher->id);
        $other = $this->coreStudent();
        $this->asCoreActor($student)->postJson('/api/hoc-vien/face-id/sample', [
            'descriptor' => $this->coreDescriptor(), 'id_hoc_vien' => $other->id,
            'id_giao_vien' => $teacher->id,
        ])->assertSuccessful()->assertJsonPath('data.has_face_id', true);
        $this->assertNotEmpty($student->fresh()->du_lieu_khuon_mat);
        $this->assertEmpty($teacher->fresh()->du_lieu_khuon_mat);
        $this->assertEmpty($other->fresh()->du_lieu_khuon_mat);
        $this->asCoreActor($student)->postJson('/api/giao-vien/face-id/sample', [
            'descriptor' => $this->coreDescriptor(0.02),
        ])->assertUnauthorized();
    }

    public function test_sample_requires_exactly_128_numeric_finite_values_and_preserves_the_old_sample(): void
    {
        $student = $this->coreStudent();
        $this->asCoreActor($student)->postJson('/api/hoc-vien/face-id/sample', [
            'descriptor' => $this->coreDescriptor(),
        ])->assertSuccessful();
        $old = $student->fresh()->du_lieu_khuon_mat;
        $nonNumeric = $this->coreDescriptor();
        $nonNumeric[5] = 'not-a-number';
        foreach ([array_fill(0, 127, 0.01), array_fill(0, 129, 0.01), $nonNumeric] as $descriptor) {
            $this->asCoreActor($student)->postJson('/api/hoc-vien/face-id/sample', compact('descriptor'))
                ->assertUnprocessable();
            $this->assertSame($old, $student->fresh()->du_lieu_khuon_mat);
        }
        Auth::forgetGuards();
        $rawToken = $student->createToken('raw-descriptor-regression')->plainTextToken;
        $this->call('POST', '/api/hoc-vien/face-id/sample', [], [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$rawToken,
            'CONTENT_TYPE' => 'application/json', 'HTTP_ACCEPT' => 'application/json',
        ], '{"descriptor":['.str_repeat('0.01,', 127).'1e999]}')->assertUnprocessable();
        $this->assertSame($old, $student->fresh()->du_lieu_khuon_mat);
    }

    public function test_verification_rejects_missing_or_mismatched_sample(): void
    {
        $student = $this->coreStudent();
        $class = $this->coreClass();
        $payload = ['descriptor' => $this->coreDescriptor(), 'purpose' => 'enrollment', 'id_lop_hoc' => $class->id];
        $this->assertCoreRejected($this->asCoreActor($student)->postJson('/api/hoc-vien/face-id/verify', $payload));
        $this->asCoreActor($student)->postJson('/api/hoc-vien/face-id/sample', [
            'descriptor' => $this->coreDescriptor(),
        ])->assertSuccessful();
        $payload['descriptor'] = $this->coreDescriptor(1.0);
        $this->assertCoreRejected($this->asCoreActor($student)->postJson('/api/hoc-vien/face-id/verify', $payload));
        $this->assertDatabaseCount('dang_ky_lops', 0);
    }

    public function test_enrollment_does_not_accept_a_client_verification_flag(): void
    {
        $student = $this->coreStudent();
        $class = $this->coreClass();
        $this->assertCoreRejected($this->asCoreActor($student)->postJson('/api/hoc-vien/dang-ky-lop-hoc', [
            'id_lop_hoc' => $class->id, 'xac_thuc_khuon_mat' => 1, 'face_verified' => true,
        ]));
        $this->assertDatabaseCount('dang_ky_lops', 0);
    }

    public function test_enrollment_proof_cannot_be_used_by_another_actor_or_for_another_class(): void
    {
        $student = $this->coreStudent();
        $other = $this->coreStudent();
        $class = $this->coreClass();
        $differentClass = $this->coreClass();
        $proof = $this->coreProof($student, 'enrollment', $class->id);
        foreach ([[$other, $class], [$student, $differentClass]] as [$actor, $target]) {
            $this->assertCoreRejected($this->asCoreActor($actor)->postJson('/api/hoc-vien/dang-ky-lop-hoc', [
                'id_lop_hoc' => $target->id, 'verification_id' => $proof['verification_id'],
            ]));
        }
        $this->assertDatabaseCount('dang_ky_lops', 0);
        $this->asCoreActor($student)->postJson('/api/hoc-vien/dang-ky-lop-hoc', [
            'id_lop_hoc' => $class->id, 'verification_id' => $proof['verification_id'],
        ])->assertSuccessful()->assertJsonPath('data.trang_thai', 'da_xac_nhan');
    }

    public function test_expired_enrollment_proof_is_rejected_without_creating_enrollment(): void
    {
        $student = $this->coreStudent();
        $class = $this->coreClass();
        $proof = $this->coreProof($student, 'enrollment', $class->id);
        Carbon::setTestNow(Carbon::parse($proof['expires_at']));
        $this->assertCoreRejected($this->asCoreActor($student)->postJson('/api/hoc-vien/dang-ky-lop-hoc', [
            'id_lop_hoc' => $class->id, 'verification_id' => $proof['verification_id'],
        ]));
        $this->assertDatabaseCount('dang_ky_lops', 0);
    }

    public function test_consumed_proof_cannot_be_reused_after_cancelling_enrollment(): void
    {
        $student = $this->coreStudent();
        $class = $this->coreClass();
        $proof = $this->coreProof($student, 'enrollment', $class->id);
        $response = $this->asCoreActor($student)->postJson('/api/hoc-vien/dang-ky-lop-hoc', [
            'id_lop_hoc' => $class->id, 'verification_id' => $proof['verification_id'],
        ])->assertSuccessful();
        $this->asCoreActor($student)->deleteJson('/api/hoc-vien/huy-dang-ky/'.$response->json('data.id'))->assertSuccessful();
        $this->assertCoreRejected($this->asCoreActor($student)->postJson('/api/hoc-vien/dang-ky-lop-hoc', [
            'id_lop_hoc' => $class->id, 'verification_id' => $proof['verification_id'],
        ]));
        $this->assertDatabaseHas('dang_ky_lops', ['id' => $response->json('data.id'), 'trang_thai' => 'da_huy']);
        $this->assertDatabaseCount('dang_ky_lops', 1);
    }
}
