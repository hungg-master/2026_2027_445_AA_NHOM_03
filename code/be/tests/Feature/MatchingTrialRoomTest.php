<?php

namespace Tests\Feature;

use App\Models\GiaoVien;
use App\Models\HocVien;
use App\Models\LopHoc;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\Support\CoreFixtures;
use Tests\TestCase;

class MatchingTrialRoomTest extends TestCase
{
    use CoreFixtures, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->prepareCoreFixtures();
    }

    private function configuredLiveKit(): void
    {
        config([
            'services.livekit.url' => 'wss://video.example.test',
            'services.livekit.api_key' => 'test-room-key',
            'services.livekit.api_secret' => 'test-room-secret-that-stays-in-tests',
        ]);
    }

    private function currentRoom(?GiaoVien $teacher = null, ?HocVien $student = null): array
    {
        $teacher ??= $this->coreTeacher();
        $student ??= $this->coreStudent();
        $class = $this->coreClass($teacher, attributes: [
            'thoi_gian_bat_dau' => '2026-10-04 08:00:00',
            'thoi_gian_ket_thuc' => '2026-10-04 09:00:00', 'tinh_trang' => 'dang_mo',
        ]);
        $session = $this->coreSession($class);
        $room = $this->coreRoom($class);
        $this->coreEnrollment($student, $class);

        return [$teacher, $student, $class, $session, $room];
    }

    private function requestTrial(HocVien $student, GiaoVien $teacher, ?LopHoc $eligibility = null, array $attributes = []): int
    {
        $eligibility ??= $this->coreClass($teacher, attributes: [
            'thoi_gian_bat_dau' => '2026-10-12 16:00:00', 'thoi_gian_ket_thuc' => '2026-10-12 17:00:00',
        ]);
        $payload = array_merge([
            'name' => $student->ho_ten, 'phone' => '0901234567',
            'subject' => $eligibility->monHoc->ten_mon_hoc, 'schedules' => ['Sun_9:00 AM'],
            'id_mon_hoc' => $eligibility->id_mon_hoc, 'id_giao_vien' => $teacher->id,
            'thoi_gian_bat_dau' => '2026-10-11T09:00:00+07:00',
            'thoi_gian_ket_thuc' => '2026-10-11T10:00:00+07:00',
        ], $attributes);
        $response = $this->asCoreActor($student)->postJson('/api/hoc-thu', $payload)->assertCreated();
        $id = $response->json('data.id') ?? DB::table('trial_bookings')->max('id');
        $this->assertNotNull($id);

        return (int) $id;
    }

    public function test_matching_reads_subject_approval_intersected_availability_duration_and_teacher_busy_sessions(): void
    {
        $student = $this->coreStudent();
        $subject = $this->coreSubject();
        $eligible = $this->coreTeacher();
        $pending = $this->coreTeacher(['trang_thai_duyet' => 'cho_duyet']);
        $wrongSubject = $this->coreTeacher();
        $tooShort = $this->coreTeacher();
        $blocked = $this->coreTeacher(['is_block' => 1]);
        foreach ([$eligible, $pending, $tooShort, $blocked] as $teacher) {
            $this->coreClass($teacher, $subject, [
                'thoi_gian_bat_dau' => '2026-10-12 16:00:00', 'thoi_gian_ket_thuc' => '2026-10-12 17:00:00',
            ]);
        }
        $this->coreClass($wrongSubject);
        $this->coreAvailability($student, 0, '09:00', '12:00');
        foreach ([$eligible, $pending, $wrongSubject, $blocked] as $teacher) {
            $this->coreAvailability($teacher, 0, '08:00', '12:00');
        }
        $this->coreAvailability($tooShort, 0, '09:00', '09:30');
        $busy = $this->coreClass($eligible, attributes: [
            'thoi_gian_bat_dau' => '2026-10-11 10:00:00', 'thoi_gian_ket_thuc' => '2026-10-11 11:00:00', 'tinh_trang' => 'dang_hoc',
        ]);
        $this->coreSession($busy);
        $url = '/api/hoc-vien/goi-y-giao-vien?id_mon_hoc='.$subject->id.'&date=2026-10-11&duration_minutes=60';
        $rows = $this->asCoreActor($student)->getJson($url)->assertOk()->assertJsonPath('status', true)->json('data');
        $this->assertSame([$eligible->id], array_column($rows, 'id_giao_vien'));
        $this->assertSame($subject->id, $rows[0]['id_mon_hoc']);
        $this->assertNotEmpty($rows[0]['slots']);
        $starts = [];
        foreach ($rows[0]['slots'] as $slot) {
            $start = Carbon::parse($slot['start'])->setTimezone('Asia/Ho_Chi_Minh');
            $end = Carbon::parse($slot['end'])->setTimezone('Asia/Ho_Chi_Minh');
            $this->assertSame('2026-10-11', $start->format('Y-m-d'));
            $this->assertSame(3600, $end->timestamp - $start->timestamp);
            $this->assertGreaterThanOrEqual(Carbon::parse('2026-10-11 09:00:00')->timestamp, $start->timestamp);
            $this->assertLessThanOrEqual(Carbon::parse('2026-10-11 12:00:00')->timestamp, $end->timestamp);
            $this->assertFalse($start->lt(Carbon::parse('2026-10-11 11:00:00')) && $end->gt(Carbon::parse('2026-10-11 10:00:00')));
            $starts[] = $start->format('H:i');
        }
        $this->assertContains('09:00', $starts);
        $this->assertContains('11:00', $starts);
    }

    public function test_matching_removes_student_busy_time_and_returns_empty_after_schedule_is_cleared(): void
    {
        $student = $this->coreStudent();
        $teacher = $this->coreTeacher();
        $source = $this->coreClass($teacher, attributes: [
            'thoi_gian_bat_dau' => '2026-10-12 16:00:00', 'thoi_gian_ket_thuc' => '2026-10-12 17:00:00',
        ]);
        $this->coreAvailability($student, 0, '09:00', '12:00');
        $this->coreAvailability($teacher, 0, '09:00', '12:00');
        $busy = $this->coreClass(attributes: [
            'thoi_gian_bat_dau' => '2026-10-11 09:00:00', 'thoi_gian_ket_thuc' => '2026-10-11 11:00:00',
        ]);
        $this->coreSession($busy);
        $this->coreEnrollment($student, $busy);
        $url = '/api/hoc-vien/goi-y-giao-vien?id_mon_hoc='.$source->id_mon_hoc.'&date=2026-10-11&duration_minutes=60';
        $rows = $this->asCoreActor($student)->getJson($url)->assertOk()->json('data');
        $this->assertCount(1, $rows);
        foreach ($rows[0]['slots'] as $slot) {
            $this->assertGreaterThanOrEqual(Carbon::parse('2026-10-11 11:00:00')->timestamp, Carbon::parse($slot['start'])->timestamp);
        }
        $this->asCoreActor($student)->postJson('/api/hoc-vien/thoi-gian-ranh', ['schedules' => []])->assertSuccessful();
        $this->asCoreActor($student)->getJson($url)->assertOk()->assertJsonPath('data', []);
    }

    public function test_matching_and_trial_join_adjacent_ui_hour_slots_without_bridging_a_gap(): void
    {
        $student = $this->coreStudent();
        $teacher = $this->coreTeacher();
        $source = $this->coreClass($teacher, attributes: [
            'thoi_gian_bat_dau' => '2026-10-12 16:00:00', 'thoi_gian_ket_thuc' => '2026-10-12 17:00:00',
        ]);
        $slots = [];
        foreach ([['09:00', '10:00'], ['10:00', '11:00'], ['11:15', '12:15']] as [$start, $end]) {
            $slots[] = ['ngay_trong_tuan' => 0, 'thoi_gian_bat_dau' => $start, 'thoi_gian_ket_thuc' => $end];
        }
        $this->asCoreActor($teacher)->postJson('/api/giao-vien/thoi-gian-ranh', ['schedules' => $slots])->assertSuccessful();
        $this->asCoreActor($student)->postJson('/api/hoc-vien/thoi-gian-ranh', ['schedules' => $slots])->assertSuccessful();
        $base = '/api/hoc-vien/goi-y-giao-vien?id_mon_hoc='.$source->id_mon_hoc.'&date=2026-10-11&duration_minutes=';
        $rows = $this->asCoreActor($student)->getJson($base.'90')->assertOk()->json('data');
        $this->assertCount(1, $rows);
        $starts = array_map(fn ($slot) => Carbon::parse($slot['start'])->format('H:i'), $rows[0]['slots']);
        $this->assertSame(['09:00', '09:15', '09:30'], $starts);
        $short = $this->asCoreActor($student)->getJson($base.'60')->assertOk()->json('data');
        $shortStarts = array_map(fn ($slot) => Carbon::parse($slot['start'])->format('H:i'), $short[0]['slots']);
        $this->assertContains('09:15', $shortStarts);
        $this->assertNotContains('10:15', $shortStarts);
        $id = $this->requestTrial($student, $teacher, $source, ['thoi_gian_ket_thuc' => '2026-10-11T10:30:00+07:00']);
        $this->asCoreActor($teacher)->postJson('/api/giao-vien/hoc-thu/'.$id.'/confirm')->assertSuccessful();
        $this->assertDatabaseHas('trial_bookings', ['id' => $id, 'status' => 'confirmed']);
    }

    public function test_matching_rejects_invalid_date_or_duration(): void
    {
        $student = $this->coreStudent();
        $subject = $this->coreSubject();
        foreach ([['not-date', 60], ['2026-10-11', 0], ['2026-10-11', -1]] as [$date, $duration]) {
            $this->asCoreActor($student)->getJson('/api/hoc-vien/goi-y-giao-vien?id_mon_hoc='.$subject->id.'&date='.$date.'&duration_minutes='.$duration)
                ->assertUnprocessable();
        }
    }

    public function test_matching_excludes_teacher_whose_only_subject_class_is_finished(): void
    {
        $student = $this->coreStudent();
        $teacher = $this->coreTeacher();
        $subject = $this->coreSubject();
        $this->coreClass($teacher, $subject, ['tinh_trang' => 'da_ket_thuc']);
        $this->coreAvailability($student, 0, '09:00', '10:00');
        $this->coreAvailability($teacher, 0, '09:00', '10:00');
        $this->asCoreActor($student)->getJson('/api/hoc-vien/goi-y-giao-vien?id_mon_hoc='.$subject->id.'&date=2026-10-11&duration_minutes=60')
            ->assertOk()->assertJsonPath('data', []);
    }

    public function test_trial_confirmation_requires_assigned_approved_teacher_and_is_idempotent(): void
    {
        $student = $this->coreStudent();
        $teacher = $this->coreTeacher();
        $stranger = $this->coreTeacher();
        $this->coreAvailability($teacher, 0, '09:00', '12:00');
        $id = $this->requestTrial($student, $teacher);
        $this->assertDatabaseHas('trial_bookings', ['id' => $id, 'id_hoc_vien' => $student->id, 'id_giao_vien' => $teacher->id, 'status' => 'pending']);
        $this->assertCoreRejected($this->asCoreActor($student)->postJson('/api/hoc-vien/hoc-thu/'.$id.'/confirm'));
        $this->assertCoreRejected($this->asCoreActor($stranger)->postJson('/api/giao-vien/hoc-thu/'.$id.'/confirm'));
        $teacher->update(['trang_thai_duyet' => 'cho_duyet']);
        $this->asCoreActor($teacher)->postJson('/api/giao-vien/hoc-thu/'.$id.'/confirm')->assertForbidden();
        $this->assertDatabaseHas('trial_bookings', ['id' => $id, 'status' => 'pending']);
        $this->assertDatabaseCount('buoi_hocs', 0);
        $teacher->update(['trang_thai_duyet' => 'da_duyet']);
        for ($attempt = 0; $attempt < 2; $attempt++) {
            $this->asCoreActor($teacher)->postJson('/api/giao-vien/hoc-thu/'.$id.'/confirm')->assertSuccessful();
        }
        $booking = DB::table('trial_bookings')->find($id);
        $this->assertSame('confirmed', $booking->status);
        $this->assertNotNull($booking->id_buoi_hoc);
        $this->assertDatabaseHas('buoi_hocs', ['id' => $booking->id_buoi_hoc, 'trang_thai' => 'scheduled']);
        $this->assertDatabaseCount('buoi_hocs', 1);
        $this->assertDatabaseCount('lop_hocs', 2);
        $this->assertDatabaseCount('phong_hops', 1);
        $this->assertDatabaseHas('dang_ky_lops', ['id_hoc_vien' => $student->id, 'id_lop_hoc' => $booking->id_lop_hoc, 'trang_thai' => 'da_xac_nhan']);
    }

    public function test_normalized_trial_accepts_actual_times_without_legacy_weekly_labels(): void
    {
        $student = $this->coreStudent();
        $teacher = $this->coreTeacher();
        $id = $this->requestTrial($student, $teacher, attributes: ['schedules' => []]);
        $this->assertDatabaseHas('trial_bookings', [
            'id' => $id, 'id_hoc_vien' => $student->id, 'id_giao_vien' => $teacher->id, 'status' => 'pending',
        ]);
        $this->assertDatabaseCount('trial_bookings', 1);
    }

    public function test_trial_lists_are_owner_scoped_and_owner_cancel_is_idempotent(): void
    {
        $student = $this->coreStudent();
        $other = $this->coreStudent();
        $teacher = $this->coreTeacher();
        $id = $this->requestTrial($student, $teacher);
        $this->asCoreActor($other)->getJson('/api/hoc-vien/hoc-thu')->assertOk()->assertJsonPath('data', []);
        $this->asCoreActor($student)->getJson('/api/hoc-vien/hoc-thu')->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.id', $id);
        $this->assertCoreRejected($this->asCoreActor($other)->postJson('/api/hoc-vien/hoc-thu/'.$id.'/cancel'));
        $this->assertDatabaseHas('trial_bookings', ['id' => $id, 'status' => 'pending']);
        for ($attempt = 0; $attempt < 2; $attempt++) {
            $this->asCoreActor($student)->postJson('/api/hoc-vien/hoc-thu/'.$id.'/cancel')->assertSuccessful();
        }
        $this->assertDatabaseHas('trial_bookings', ['id' => $id, 'status' => 'cancelled']);
        $this->assertCoreRejected($this->asCoreActor($teacher)->postJson('/api/giao-vien/hoc-thu/'.$id.'/confirm'));
        $this->assertDatabaseCount('buoi_hocs', 0);
    }

    public function test_trial_slot_competition_rechecks_busy_time_before_creating_second_session(): void
    {
        $teacher = $this->coreTeacher();
        $source = $this->coreClass($teacher, attributes: [
            'thoi_gian_bat_dau' => '2026-10-12 16:00:00', 'thoi_gian_ket_thuc' => '2026-10-12 17:00:00',
        ]);
        $this->coreAvailability($teacher, 0, '09:00', '12:00');
        $ids = [$this->requestTrial($this->coreStudent(), $teacher, $source), $this->requestTrial($this->coreStudent(), $teacher, $source)];
        $this->asCoreActor($teacher)->postJson('/api/giao-vien/hoc-thu/'.$ids[0].'/confirm')->assertSuccessful();
        $this->assertCoreRejected($this->asCoreActor($teacher)->postJson('/api/giao-vien/hoc-thu/'.$ids[1].'/confirm'));
        $this->assertDatabaseHas('trial_bookings', ['id' => $ids[0], 'status' => 'confirmed']);
        $this->assertDatabaseHas('trial_bookings', ['id' => $ids[1], 'status' => 'pending', 'id_buoi_hoc' => null]);
        $this->assertDatabaseCount('buoi_hocs', 1);
        $this->assertDatabaseCount('lop_hocs', 2);
        $this->assertDatabaseCount('phong_hops', 1);
        $this->assertDatabaseCount('dang_ky_lops', 1);
    }

    public function test_confirmed_trial_cancel_updates_session_and_enrollment_together(): void
    {
        $student = $this->coreStudent();
        $teacher = $this->coreTeacher();
        $this->coreAvailability($teacher, 0, '09:00', '12:00');
        $id = $this->requestTrial($student, $teacher);
        $this->asCoreActor($teacher)->postJson('/api/giao-vien/hoc-thu/'.$id.'/confirm')->assertSuccessful();
        $booking = DB::table('trial_bookings')->find($id);
        $this->asCoreActor($student)->postJson('/api/hoc-vien/hoc-thu/'.$id.'/cancel')->assertSuccessful();
        $this->assertDatabaseHas('trial_bookings', ['id' => $id, 'status' => 'cancelled']);
        $this->assertDatabaseHas('buoi_hocs', ['id' => $booking->id_buoi_hoc, 'trang_thai' => 'cancelled']);
        $this->assertDatabaseHas('dang_ky_lops', ['id_lop_hoc' => $booking->id_lop_hoc, 'id_hoc_vien' => $student->id, 'trang_thai' => 'da_huy']);
    }

    public function test_trial_session_insert_failure_rolls_back_confirmation_class_room_and_enrollment(): void
    {
        $student = $this->coreStudent();
        $teacher = $this->coreTeacher();
        $this->coreAvailability($teacher, 0, '09:00', '12:00');
        $id = $this->requestTrial($student, $teacher);
        DB::unprepared("CREATE TRIGGER fail_core_trial_session_insert BEFORE INSERT ON buoi_hocs BEGIN SELECT RAISE(ABORT, 'forced trial failure'); END");
        $this->asCoreActor($teacher)->postJson('/api/giao-vien/hoc-thu/'.$id.'/confirm')
            ->assertServerError()->assertDontSee('forced trial failure');
        $this->assertDatabaseHas('trial_bookings', ['id' => $id, 'status' => 'pending', 'id_lop_hoc' => null, 'id_buoi_hoc' => null]);
        $this->assertDatabaseCount('lop_hocs', 1);
        $this->assertDatabaseCount('buoi_hocs', 0);
        $this->assertDatabaseCount('phong_hops', 0);
        $this->assertDatabaseCount('dang_ky_lops', 0);
    }

    public function test_room_without_provider_configuration_returns_503_and_keeps_proof_usable(): void
    {
        [, $student, , $session] = $this->currentRoom();
        config(['services.livekit.url' => null, 'services.livekit.api_key' => null, 'services.livekit.api_secret' => null]);
        $proof = $this->coreProof($student, 'room', $session->id);
        $payload = ['id_buoi_hoc' => $session->id, 'verification_id' => $proof['verification_id']];
        $this->asCoreActor($student)->postJson('/api/phong-hop/tao-token', $payload)
            ->assertStatus(503)->assertJsonPath('status', false)->assertJsonMissingPath('data.token');
        $this->assertDatabaseCount('chi_tiet_phong_hops', 0);
        $this->configuredLiveKit();
        $this->asCoreActor($student)->postJson('/api/phong-hop/tao-token', $payload)->assertSuccessful();
    }

    public function test_room_token_requires_membership_and_rejects_client_flags(): void
    {
        [, $student, , $session] = $this->currentRoom();
        $outsider = $this->coreStudent();
        $this->configuredLiveKit();
        foreach ([$student, $outsider] as $actor) {
            $this->assertCoreRejected($this->asCoreActor($actor)->postJson('/api/phong-hop/tao-token', [
                'id_buoi_hoc' => $session->id, 'id_nguoi_dung' => $student->id,
                'xac_thuc_khuon_mat' => 1, 'face_verified' => true,
            ]));
        }
        $this->assertDatabaseCount('chi_tiet_phong_hops', 0);
    }

    public function test_room_proof_is_bound_to_account_type_purpose_and_session(): void
    {
        [$teacher, $student, $class, $session] = $this->currentRoom();
        [, $otherStudent, , $otherSession] = $this->currentRoom();
        $this->assertSame($teacher->id, $student->id);
        $this->configuredLiveKit();
        $roomProof = $this->coreProof($student, 'room', $session->id);
        $enrollmentProof = $this->coreProof($student, 'enrollment', $class->id);
        foreach ([
            [$teacher, $session->id, $roomProof['verification_id']],
            [$student, $otherSession->id, $roomProof['verification_id']],
            [$student, $session->id, $enrollmentProof['verification_id']],
            [$otherStudent, $session->id, $roomProof['verification_id']],
        ] as [$actor, $target, $verification]) {
            $this->assertCoreRejected($this->asCoreActor($actor)->postJson('/api/phong-hop/tao-token', [
                'id_buoi_hoc' => $target, 'verification_id' => $verification,
            ]));
        }
        $this->asCoreActor($student)->postJson('/api/phong-hop/tao-token', [
            'id_buoi_hoc' => $session->id, 'verification_id' => $roomProof['verification_id'],
        ])->assertSuccessful();
        $this->assertDatabaseCount('chi_tiet_phong_hops', 0);
    }

    public function test_room_proof_expires_and_is_single_use(): void
    {
        [, $student, , $session] = $this->currentRoom();
        $this->configuredLiveKit();
        $expired = $this->coreProof($student, 'room', $session->id);
        Carbon::setTestNow(Carbon::parse($expired['expires_at'])->addSecond());
        $this->assertCoreRejected($this->asCoreActor($student)->postJson('/api/phong-hop/tao-token', [
            'id_buoi_hoc' => $session->id, 'verification_id' => $expired['verification_id'],
        ]));
        Carbon::setTestNow(Carbon::parse('2026-10-04 08:00:00', 'Asia/Ho_Chi_Minh'));
        $proof = $this->coreProof($student, 'room', $session->id);
        $payload = ['id_buoi_hoc' => $session->id, 'verification_id' => $proof['verification_id']];
        $this->asCoreActor($student)->postJson('/api/phong-hop/tao-token', $payload)->assertSuccessful();
        $this->assertCoreRejected($this->asCoreActor($student)->postJson('/api/phong-hop/tao-token', $payload));
    }

    public function test_room_token_is_hs256_signed_with_server_identity_and_room_scoped_claims(): void
    {
        [$teacher, $student, , $session, $room] = $this->currentRoom();
        $this->configuredLiveKit();
        $subjects = [];
        foreach ([$student, $teacher] as $actor) {
            $proof = $this->coreProof($actor, 'room', $session->id);
            $response = $this->asCoreActor($actor)->postJson('/api/phong-hop/tao-token', [
                'id_buoi_hoc' => $session->id, 'verification_id' => $proof['verification_id'],
                'user_name' => 'Forged name', 'identity' => 'admin:999',
            ])->assertSuccessful()->assertJsonPath('data.server_url', 'wss://video.example.test')
                ->assertJsonPath('data.id_phong_hop', $room->id)->assertJsonPath('data.ma_phong', $room->ma_phong);
            $parts = explode('.', $response->json('data.token'));
            $this->assertCount(3, $parts);
            $header = json_decode(base64_decode(strtr($parts[0], '-_', '+/')), true, 512, JSON_THROW_ON_ERROR);
            $claims = json_decode(base64_decode(strtr($parts[1], '-_', '+/')), true, 512, JSON_THROW_ON_ERROR);
            $expectedSignature = rtrim(strtr(base64_encode(hash_hmac('sha256', $parts[0].'.'.$parts[1], 'test-room-secret-that-stays-in-tests', true)), '+/', '-_'), '=');
            $this->assertSame('HS256', $header['alg']);
            $this->assertSame($expectedSignature, $parts[2]);
            $this->assertSame('test-room-key', $claims['iss']);
            $this->assertSame(($actor instanceof GiaoVien ? 'giao_vien:' : 'hoc_vien:').$actor->id, $claims['sub']);
            $this->assertSame($actor->ho_ten, $claims['name']);
            $this->assertSame($room->ma_phong, $claims['video']['room']);
            $this->assertTrue($claims['video']['roomJoin']);
            $this->assertGreaterThan(now()->timestamp, $claims['exp']);
            $this->assertLessThanOrEqual(now()->timestamp + 3600, $claims['exp']);
            $this->assertArrayNotHasKey('roomAdmin', $claims['video']);
            $subjects[] = $claims['sub'];
        }
        $this->assertNotSame($subjects[0], $subjects[1]);
    }

    public function test_attendance_uses_grant_actor_and_session_and_separates_equal_numeric_ids(): void
    {
        [$teacher, $student, , $session, $room] = $this->currentRoom();
        $this->assertSame($teacher->id, $student->id);
        $outsider = $this->coreStudent();
        $this->configuredLiveKit();
        foreach ([$teacher, $student] as $actor) {
            $proof = $this->coreProof($actor, 'room', $session->id);
            $response = $this->asCoreActor($actor)->postJson('/api/phong-hop/tao-token', [
                'id_buoi_hoc' => $session->id, 'verification_id' => $proof['verification_id'],
            ])->assertSuccessful();
            $grant = $response->json('data.attendance_token');
            $this->assertNotEmpty($grant);
            $payload = [
                'id_buoi_hoc' => $session->id, 'attendance_token' => $grant,
                'id_nguoi_dung' => $outsider->id, 'id_phong_hop' => 999,
                'loai_nguoi_dung' => 'admin', 'xac_thuc_khuon_mat' => 0,
            ];
            $this->assertCoreRejected($this->asCoreActor($outsider)->postJson('/api/chi-tiet-phong-hop/create', $payload));
            $this->asCoreActor($actor)->postJson('/api/chi-tiet-phong-hop/create', $payload)->assertSuccessful();
            $this->assertDatabaseHas('chi_tiet_phong_hops', [
                'id_buoi_hoc' => $session->id, 'id_phong_hop' => $room->id, 'id_nguoi_dung' => $actor->id,
                'loai_nguoi_dung' => $actor instanceof GiaoVien ? 'giao_vien' : 'hoc_vien',
                'xac_thuc_khuon_mat' => 1, 'is_active' => 1,
            ]);
        }
        $this->assertDatabaseCount('chi_tiet_phong_hops', 2);
        $this->asCoreActor($teacher)->postJson('/api/phong-hop/roi-phong', [
            'id_buoi_hoc' => $session->id, 'id_nguoi_dung' => $student->id,
        ])->assertSuccessful();
        $this->assertDatabaseHas('chi_tiet_phong_hops', ['id_nguoi_dung' => $student->id, 'loai_nguoi_dung' => 'hoc_vien', 'is_active' => 1]);
        $this->assertDatabaseHas('chi_tiet_phong_hops', ['id_nguoi_dung' => $teacher->id, 'loai_nguoi_dung' => 'giao_vien', 'is_active' => 0]);
        $this->asCoreActor($outsider)->getJson('/api/phong-hop/lich-su-tham-gia?id_nguoi_dung='.$student->id)
            ->assertOk()->assertJsonPath('data', []);
    }

    public function test_attendance_cannot_be_forged_without_server_grant(): void
    {
        [, $student, , $session, $room] = $this->currentRoom();
        $this->assertCoreRejected($this->asCoreActor($student)->postJson('/api/chi-tiet-phong-hop/create', [
            'id_buoi_hoc' => $session->id, 'id_phong_hop' => $room->id,
            'id_nguoi_dung' => $student->id, 'xac_thuc_khuon_mat' => 1,
        ]));
        $this->assertDatabaseCount('chi_tiet_phong_hops', 0);
    }

    public function test_arbitrary_room_code_does_not_create_a_room(): void
    {
        $student = $this->coreStudent();
        $this->assertCoreRejected($this->asCoreActor($student)->postJson('/api/phong-hop/kiem-tra-phong-hop', ['ma_phong' => 'EDU-999']));
        $this->assertCoreRejected($this->asCoreActor($student)->postJson('/api/phong-hop/tao-token', ['ma_phong' => 'ROOM-999']));
        $this->assertDatabaseCount('phong_hops', 0);
    }
}
