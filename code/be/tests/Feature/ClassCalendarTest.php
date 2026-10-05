<?php

namespace Tests\Feature;

use App\Models\DangKyLop;
use App\Models\LopHoc;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\Support\CoreFixtures;
use Tests\TestCase;

class ClassCalendarTest extends TestCase
{
    use CoreFixtures, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->prepareCoreFixtures();
    }

    public function test_online_class_generates_a_room_without_a_manual_meeting_url(): void
    {
        $teacher = $this->coreTeacher();
        $response = $this->asCoreActor($teacher)->postJson('/api/giao-vien/lop-hoc',
            $this->coreClassPayload($this->coreSubject(), ['si_so_toi_da' => 1])
        )->assertSuccessful()->assertJsonPath('status', true);
        $class = LopHoc::findOrFail($response->json('data.id'));
        $this->assertSame(1, $class->si_so_toi_da);
        $this->assertNotNull($class->phongHop);
        $this->assertSame(2, $class->phongHop->so_nguoi_toi_da);
        $this->assertSame($teacher->id, $class->phongHop->id_chu_phong);
        $this->assertNotEmpty($class->link_online);
        $this->assertDatabaseCount('buoi_hocs', 1);
    }

    public function test_offline_class_uses_physical_room_and_does_not_create_online_room(): void
    {
        $teacher = $this->coreTeacher();
        $room = $this->corePhysicalRoom();
        $response = $this->asCoreActor($teacher)->postJson('/api/giao-vien/lop-hoc',
            $this->coreClassPayload($this->coreSubject(), ['hinh_thuc' => 'offline', 'id_phong_hoc' => $room->id])
        )->assertSuccessful();
        $this->assertDatabaseHas('lop_hocs', [
            'id' => $response->json('data.id'), 'id_phong_hoc' => $room->id,
            'hinh_thuc' => 'offline', 'link_online' => null,
        ]);
        $this->assertDatabaseCount('phong_hops', 0);
        $this->assertDatabaseCount('buoi_hocs', 1);
    }

    public function test_class_creation_rejects_terminal_states_without_writing_sessions(): void
    {
        $teacher = $this->coreTeacher();
        $subject = $this->coreSubject();
        foreach (['da_huy', 'da_ket_thuc', 'dang_hoc'] as $status) {
            $this->asCoreActor($teacher)->postJson('/api/giao-vien/lop-hoc',
                $this->coreClassPayload($subject, ['tinh_trang' => $status])
            )->assertUnprocessable()->assertJsonValidationErrors('tinh_trang');
        }
        $this->assertDatabaseCount('lop_hocs', 0);
        $this->assertDatabaseCount('buoi_hocs', 0);
    }

    public function test_non_string_meeting_link_is_a_validation_error_without_writing_class(): void
    {
        $this->asCoreActor($this->coreTeacher())->postJson('/api/giao-vien/lop-hoc',
            $this->coreClassPayload($this->coreSubject(), ['link_online' => ['malformed']])
        )->assertUnprocessable()->assertJsonValidationErrors('link_online');
        $this->assertDatabaseCount('lop_hocs', 0);
    }

    public function test_class_and_session_normalize_equivalent_input_offsets_to_the_same_instant(): void
    {
        $teacher = $this->coreTeacher();
        $response = $this->asCoreActor($teacher)->postJson('/api/giao-vien/lop-hoc',
            $this->coreClassPayload($this->coreSubject(), [
                'thoi_gian_bat_dau' => '2026-10-11T02:00:00Z',
                'thoi_gian_ket_thuc' => '2026-10-11T04:00:00+01:00',
            ])
        )->assertSuccessful();
        $class = LopHoc::findOrFail($response->json('data.id'));
        $session = $class->buoiHocs()->firstOrFail();
        $expectedStart = Carbon::parse('2026-10-11T09:00:00+07:00')->timestamp;
        $expectedEnd = Carbon::parse('2026-10-11T10:00:00+07:00')->timestamp;
        $this->assertSame($expectedStart, $class->thoi_gian_bat_dau->timestamp);
        $this->assertSame($expectedEnd, $class->thoi_gian_ket_thuc->timestamp);
        $this->assertSame($expectedStart, $session->thoi_gian_bat_dau->timestamp);
        $this->assertSame($expectedEnd, $session->thoi_gian_ket_thuc->timestamp);
    }

    public function test_room_insert_failure_rolls_back_class_and_sessions(): void
    {
        $teacher = $this->coreTeacher();
        $subject = $this->coreSubject();
        DB::unprepared("CREATE TRIGGER fail_core_room_insert BEFORE INSERT ON phong_hops BEGIN SELECT RAISE(ABORT, 'forced room failure'); END");
        $response = $this->asCoreActor($teacher)->postJson('/api/giao-vien/lop-hoc',
            $this->coreClassPayload($subject, ['link_online' => 'https://example.test/meeting'])
        );
        $response->assertServerError()->assertDontSee('forced room failure');
        $this->assertDatabaseCount('lop_hocs', 0);
        $this->assertDatabaseCount('phong_hops', 0);
        $this->assertDatabaseCount('buoi_hocs', 0);
    }

    public function test_ordinary_fee_edit_accepts_unchanged_generated_room_link_and_preserves_sessions(): void
    {
        $teacher = $this->coreTeacher();
        $subject = $this->coreSubject();
        $created = $this->asCoreActor($teacher)->postJson('/api/giao-vien/lop-hoc', $this->coreClassPayload($subject))
            ->assertSuccessful()->json('data');
        $class = LopHoc::findOrFail($created['id']);
        $sessionId = $class->buoiHocs()->firstOrFail()->id;
        $roomId = $class->phongHop->id;
        $this->asCoreActor($teacher)->putJson('/api/giao-vien/lop-hoc/'.$class->id,
            $this->coreClassPayload($subject, ['hoc_phi' => 200000, 'link_online' => $created['link_online']])
        )->assertSuccessful();
        $this->assertEquals(200000, $class->fresh()->hoc_phi);
        $this->assertDatabaseHas('buoi_hocs', ['id' => $sessionId, 'id_lop_hoc' => $class->id]);
        $this->assertDatabaseHas('phong_hops', ['id' => $roomId, 'id_lop_hoc' => $class->id]);
        $this->assertSame($created['link_online'], $class->fresh()->link_online);
        $this->assertDatabaseCount('buoi_hocs', 1);
        $this->assertDatabaseCount('phong_hops', 1);
    }

    public function test_weekly_class_response_round_trips_date_only_when_editing_fee(): void
    {
        $teacher = $this->coreTeacher();
        $subject = $this->coreSubject();
        $created = $this->asCoreActor($teacher)->postJson('/api/giao-vien/lop-hoc',
            $this->coreClassPayload($subject, ['recurrence' => 'weekly', 'recurrence_until' => '2026-10-25'])
        )->assertCreated()->json('data');
        $this->assertSame('2026-10-25', $created['recurrence_until']);
        $this->asCoreActor($teacher)->putJson('/api/giao-vien/lop-hoc/'.$created['id'],
            $this->coreClassPayload($subject, ['hoc_phi' => 200000, 'recurrence' => 'weekly',
                'recurrence_until' => $created['recurrence_until'], 'link_online' => $created['link_online']])
        )->assertSuccessful()->assertJsonPath('data.recurrence_until', '2026-10-25');
        $this->assertDatabaseCount('buoi_hocs', 3);
    }

    public function test_teacher_cannot_reopen_cancelled_or_completed_class(): void
    {
        $teacher = $this->coreTeacher();
        foreach (['da_huy' => 'cancelled', 'da_ket_thuc' => 'completed'] as $classStatus => $sessionStatus) {
            $subject = $this->coreSubject();
            $class = $this->coreClass($teacher, $subject, ['tinh_trang' => $classStatus]);
            $session = $this->coreSession($class, ['trang_thai' => $sessionStatus]);
            $this->asCoreActor($teacher)->putJson('/api/giao-vien/lop-hoc/'.$class->id,
                $this->coreClassPayload($subject, ['tinh_trang' => 'dang_mo'])
            )->assertStatus(409);
            $this->assertSame($classStatus, $class->fresh()->tinh_trang);
            $this->assertSame($sessionStatus, $session->fresh()->trang_thai);
        }
    }

    public function test_teacher_cannot_mark_class_finished_before_last_session_end(): void
    {
        $teacher = $this->coreTeacher();
        $subject = $this->coreSubject();
        $class = $this->coreClass($teacher, $subject);
        $session = $this->coreSession($class);
        $this->asCoreActor($teacher)->putJson('/api/giao-vien/lop-hoc/'.$class->id,
            $this->coreClassPayload($subject, ['tinh_trang' => 'da_ket_thuc'])
        )->assertStatus(409);
        $this->assertSame('dang_mo', $class->fresh()->tinh_trang);
        $this->assertSame('scheduled', $session->fresh()->trang_thai);
    }

    public function test_weekly_recurrence_has_a_finite_end_and_includes_sunday_boundary(): void
    {
        $teacher = $this->coreTeacher();
        $response = $this->asCoreActor($teacher)->postJson('/api/giao-vien/lop-hoc',
            $this->coreClassPayload($this->coreSubject(), ['recurrence' => 'weekly', 'recurrence_until' => '2026-10-25'])
        )->assertSuccessful();
        $starts = DB::table('buoi_hocs')->where('id_lop_hoc', $response->json('data.id'))
            ->orderBy('thoi_gian_bat_dau')->pluck('thoi_gian_bat_dau')
            ->map(fn ($date) => Carbon::parse($date)->format('Y-m-d H:i'))->all();
        $this->assertSame(['2026-10-11 09:00', '2026-10-18 09:00', '2026-10-25 09:00'], $starts);
        $this->assertDatabaseCount('phong_hops', 1);
    }

    public function test_weekly_recurrence_requires_a_valid_end_date_before_any_insert(): void
    {
        $teacher = $this->coreTeacher();
        $subject = $this->coreSubject();
        foreach ([[], ['recurrence_until' => '2026-10-10']] as $recurrence) {
            $this->asCoreActor($teacher)->postJson('/api/giao-vien/lop-hoc',
                $this->coreClassPayload($subject, array_merge([
                    'recurrence' => 'weekly', 'link_online' => 'https://example.test/meeting',
                ], $recurrence))
            )->assertUnprocessable()->assertJsonValidationErrors('recurrence_until');
        }
        $this->assertDatabaseCount('lop_hocs', 0);
        $this->assertDatabaseCount('phong_hops', 0);
    }

    public function test_both_calendars_return_the_same_persisted_dates_and_never_project_extra_weeks(): void
    {
        $teacher = $this->coreTeacher();
        $student = $this->coreStudent();
        $class = $this->coreClass($teacher);
        $first = $this->coreSession($class);
        $last = $this->coreSession($class, [
            'thoi_gian_bat_dau' => '2026-10-25 09:00:00', 'thoi_gian_ket_thuc' => '2026-10-25 10:00:00',
        ]);
        $this->coreEnrollment($student, $class);
        $query = '?from_date=2026-10-11&to_date=2026-10-25';
        $teacherRows = $this->asCoreActor($teacher)->getJson('/api/giao-vien/lich-day'.$query)->assertOk()->json('data');
        $studentRows = $this->asCoreActor($student)->getJson('/api/hoc-vien/lich-hoc'.$query)->assertOk()->json('data');
        $expected = [
            [$first->id, Carbon::parse('2026-10-11 09:00:00')->timestamp, Carbon::parse('2026-10-11 10:00:00')->timestamp],
            [$last->id, Carbon::parse('2026-10-25 09:00:00')->timestamp, Carbon::parse('2026-10-25 10:00:00')->timestamp],
        ];
        foreach ([$teacherRows, $studentRows] as $rows) {
            $this->assertCount(2, $rows);
            $actual = array_map(fn ($row) => [
                $row['id_buoi_hoc'], Carbon::parse($row['thoi_gian_bat_dau'])->timestamp,
                Carbon::parse($row['thoi_gian_ket_thuc'])->timestamp,
            ], $rows);
            $this->assertSame($expected, $actual);
            foreach ($rows as $row) {
                $this->assertSame($class->id, $row['lop_hoc']['id']);
            }
        }
        foreach ([[$teacher, '/api/giao-vien/lich-day'], [$student, '/api/hoc-vien/lich-hoc']] as [$actor, $url]) {
            $this->asCoreActor($actor)->getJson($url.'?from_date=2026-10-18&to_date=2026-10-24')
                ->assertOk()->assertJsonPath('data', []);
        }
    }

    public function test_cancel_then_reenroll_reuses_the_unique_student_class_record(): void
    {
        $student = $this->coreStudent();
        $class = $this->coreClass();
        $this->coreSession($class);
        $enrollment = $this->coreEnrollment($student, $class);
        $this->asCoreActor($student)->deleteJson('/api/hoc-vien/huy-dang-ky/'.$enrollment->id)->assertSuccessful();
        $proof = $this->coreProof($student, 'enrollment', $class->id);
        $this->asCoreActor($student)->postJson('/api/hoc-vien/dang-ky-lop-hoc', [
            'id_lop_hoc' => $class->id, 'verification_id' => $proof['verification_id'],
        ])->assertSuccessful()->assertJsonPath('data.id', $enrollment->id)->assertJsonPath('data.trang_thai', 'da_xac_nhan');
        $this->assertDatabaseCount('dang_ky_lops', 1);
    }

    public function test_repeated_student_cancellation_is_successful_and_does_not_add_records(): void
    {
        $student = $this->coreStudent();
        $enrollment = $this->coreEnrollment($student, $this->coreClass());
        for ($attempt = 0; $attempt < 2; $attempt++) {
            $this->asCoreActor($student)->deleteJson('/api/hoc-vien/huy-dang-ky/'.$enrollment->id)->assertSuccessful();
        }
        $this->assertSame('da_huy', $enrollment->fresh()->trang_thai);
        $this->assertDatabaseCount('dang_ky_lops', 1);
    }

    public function test_teacher_approval_is_idempotent_even_when_class_is_full(): void
    {
        $teacher = $this->coreTeacher();
        $class = $this->coreClass($teacher, attributes: ['si_so_toi_da' => 1]);
        $enrollment = $this->coreEnrollment($this->coreStudent(), $class);
        for ($attempt = 0; $attempt < 2; $attempt++) {
            $this->asCoreActor($teacher)->postJson('/api/giao-vien/lop-hoc/'.$class->id.'/duyet-hoc-vien', [
                'id_dang_ky' => $enrollment->id, 'hanh_dong' => 'duyet',
            ])->assertSuccessful()->assertJsonPath('data.trang_thai', 'da_xac_nhan');
        }
        $this->assertDatabaseCount('dang_ky_lops', 1);
    }

    public function test_last_place_accepts_only_one_student_and_keeps_confirmed_state(): void
    {
        $class = $this->coreClass(attributes: ['si_so_toi_da' => 1]);
        $this->coreSession($class);
        $students = [$this->coreStudent(), $this->coreStudent()];
        foreach ($students as $index => $student) {
            $proof = $this->coreProof($student, 'enrollment', $class->id);
            $response = $this->asCoreActor($student)->postJson('/api/hoc-vien/dang-ky-lop-hoc', [
                'id_lop_hoc' => $class->id, 'verification_id' => $proof['verification_id'],
            ]);
            if ($index === 0) {
                $response->assertSuccessful()->assertJsonPath('data.trang_thai', 'da_xac_nhan');
            } else {
                $this->assertCoreRejected($response);
            }
        }
        $this->assertDatabaseCount('dang_ky_lops', 1);
        $this->assertDatabaseHas('dang_ky_lops', ['id_hoc_vien' => $students[0]->id, 'trang_thai' => 'da_xac_nhan']);
    }

    public function test_active_same_subject_prevents_second_class_but_finished_class_does_not(): void
    {
        $student = $this->coreStudent();
        $subject = $this->coreSubject();
        $old = $this->coreClass(subject: $subject);
        $this->coreSession($old);
        $this->coreEnrollment($student, $old);
        $new = $this->coreClass(subject: $subject, attributes: [
            'thoi_gian_bat_dau' => '2026-10-12 11:00:00', 'thoi_gian_ket_thuc' => '2026-10-12 12:00:00',
        ]);
        $this->coreSession($new);
        $proof = $this->coreProof($student, 'enrollment', $new->id);
        $payload = ['id_lop_hoc' => $new->id, 'verification_id' => $proof['verification_id']];
        $this->assertCoreRejected($this->asCoreActor($student)->postJson('/api/hoc-vien/dang-ky-lop-hoc', $payload));
        $this->assertDatabaseCount('dang_ky_lops', 1);
        $old->update(['tinh_trang' => 'da_ket_thuc']);
        $this->asCoreActor($student)->postJson('/api/hoc-vien/dang-ky-lop-hoc', $payload)->assertSuccessful();
        $this->assertDatabaseCount('dang_ky_lops', 2);
    }

    public function test_enrollment_checks_current_teacher_approval_again(): void
    {
        $teacher = $this->coreTeacher();
        $student = $this->coreStudent();
        $class = $this->coreClass($teacher);
        $this->coreSession($class);
        $proof = $this->coreProof($student, 'enrollment', $class->id);
        $teacher->update(['trang_thai_duyet' => 'cho_duyet']);
        $this->assertCoreRejected($this->asCoreActor($student)->postJson('/api/hoc-vien/dang-ky-lop-hoc', [
            'id_lop_hoc' => $class->id, 'verification_id' => $proof['verification_id'],
        ]));
        $this->assertDatabaseCount('dang_ky_lops', 0);
    }

    public function test_student_conflicts_use_actual_dates_and_allow_adjacent_sessions(): void
    {
        $student = $this->coreStudent();
        $existing = $this->coreClass();
        $this->coreSession($existing);
        $this->coreEnrollment($student, $existing);
        foreach ([
            ['2026-10-11 09:30:00', '2026-10-11 10:30:00', false],
            ['2026-10-11 10:00:00', '2026-10-11 11:00:00', true],
            ['2026-10-18 09:00:00', '2026-10-18 10:00:00', true],
        ] as [$start, $end, $allowed]) {
            $class = $this->coreClass(attributes: ['thoi_gian_bat_dau' => $start, 'thoi_gian_ket_thuc' => $end]);
            $this->coreSession($class);
            $proof = $this->coreProof($student, 'enrollment', $class->id);
            $response = $this->asCoreActor($student)->postJson('/api/hoc-vien/dang-ky-lop-hoc', [
                'id_lop_hoc' => $class->id, 'verification_id' => $proof['verification_id'],
            ]);
            if ($allowed) {
                $response->assertSuccessful();
            } else {
                $this->assertCoreRejected($response);
            }
        }
        $this->assertSame(3, DangKyLop::where('id_hoc_vien', $student->id)->count());
    }

    public function test_teacher_and_physical_room_conflicts_are_checked_on_class_creation(): void
    {
        $teacher = $this->coreTeacher();
        $room = $this->corePhysicalRoom();
        $existing = $this->coreClass($teacher, attributes: ['hinh_thuc' => 'offline', 'id_phong_hoc' => $room->id]);
        $this->coreSession($existing);
        foreach ([$teacher, $this->coreTeacher()] as $actor) {
            $this->assertCoreRejected($this->asCoreActor($actor)->postJson('/api/giao-vien/lop-hoc',
                $this->coreClassPayload($this->coreSubject(), [
                    'hinh_thuc' => 'offline', 'id_phong_hoc' => $room->id,
                    'thoi_gian_bat_dau' => '2026-10-11T09:30:00+07:00',
                    'thoi_gian_ket_thuc' => '2026-10-11T10:30:00+07:00',
                ])
            ));
        }
        $this->assertDatabaseCount('lop_hocs', 1);
        $this->assertDatabaseCount('buoi_hocs', 1);
    }

    public function test_empty_availability_replacement_stays_empty_for_each_account_type(): void
    {
        $teacher = $this->coreTeacher();
        $student = $this->coreStudent();
        $this->assertSame($teacher->id, $student->id);
        $this->coreAvailability($teacher, 0, '09:00', '10:00');
        $this->coreAvailability($student, 1, '11:00', '12:00');
        $this->asCoreActor($teacher)->postJson('/api/giao-vien/thoi-gian-ranh', ['schedules' => []])->assertSuccessful();
        $this->asCoreActor($teacher)->getJson('/api/giao-vien/thoi-gian-ranh')->assertOk()->assertJsonPath('data', []);
        $this->asCoreActor($student)->getJson('/api/hoc-vien/thoi-gian-ranh')->assertOk()->assertJsonCount(1, 'data');
        $this->asCoreActor($student)->postJson('/api/hoc-vien/thoi-gian-ranh', ['schedules' => []])->assertSuccessful();
        $this->asCoreActor($student)->getJson('/api/hoc-vien/thoi-gian-ranh')->assertOk()->assertJsonPath('data', []);
        $this->assertDatabaseCount('thoi_gian_ranhs', 0);
    }

    public function test_invalid_availability_intervals_preserve_existing_schedule(): void
    {
        $student = $this->coreStudent();
        $original = $this->coreAvailability($student, 1, '09:00', '10:00');
        foreach ([['10:00', '09:00'], ['09:00', '09:00'], ['not-time', '10:00'], ['25:00', '26:00']] as [$start, $end]) {
            $this->asCoreActor($student)->postJson('/api/hoc-vien/thoi-gian-ranh', ['schedules' => [[
                'ngay_trong_tuan' => 1, 'thoi_gian_bat_dau' => $start, 'thoi_gian_ket_thuc' => $end,
            ]]])->assertUnprocessable();
            $this->assertDatabaseHas('thoi_gian_ranhs', ['id' => $original->id, 'thoi_gian_bat_dau' => '09:00', 'thoi_gian_ket_thuc' => '10:00']);
            $this->assertDatabaseCount('thoi_gian_ranhs', 1);
        }
    }

    public function test_availability_write_failure_restores_previous_rows(): void
    {
        $teacher = $this->coreTeacher();
        $old = $this->coreAvailability($teacher, 0, '09:00', '10:00');
        DB::unprepared("CREATE TRIGGER fail_core_availability_insert BEFORE INSERT ON thoi_gian_ranhs BEGIN SELECT RAISE(ABORT, 'forced schedule failure'); END");
        $this->asCoreActor($teacher)->postJson('/api/giao-vien/thoi-gian-ranh', ['schedules' => [[
            'ngay_trong_tuan' => 1, 'thoi_gian_bat_dau' => '11:00', 'thoi_gian_ket_thuc' => '12:00',
        ]]])->assertServerError();
        $this->assertDatabaseHas('thoi_gian_ranhs', ['id' => $old->id, 'ngay_trong_tuan' => 0]);
        $this->assertDatabaseCount('thoi_gian_ranhs', 1);
    }
}
