<?php

namespace Tests\Feature;

use App\Mail\BusinessEventMail;
use App\Services\SupportEvents;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Tests\Support\CoreFixtures;
use Tests\TestCase;

class SupportingMailTest extends TestCase
{
    use CoreFixtures, DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        $this->prepareCoreFixtures();
    }

    public function test_enrollment_mail_and_obligation_wait_for_commit_and_rollback_sends_nothing(): void
    {
        $student = $this->coreStudent();
        $class = $this->coreClass();
        Mail::fake();
        DB::beginTransaction();
        $this->coreEnrollment($student, $class);
        Mail::assertNothingSent();
        DB::rollBack();
        Mail::assertNothingSent();
        $this->assertDatabaseCount('payment_obligations', 0);
        DB::beginTransaction();
        $enrollment = $this->coreEnrollment($student, $class);
        Mail::assertNothingSent();
        DB::commit();
        $this->assertDatabaseHas('payment_obligations', ['id_dang_ky_lop' => $enrollment->id, 'amount' => 100000]);
        Mail::assertSent(BusinessEventMail::class, fn ($mail) => $mail->hasTo($student->email) && $mail->eventType === 'enrollment');
        $enrollment->update(['trang_thai' => 'da_huy']);
        Mail::assertSent(BusinessEventMail::class, fn ($mail) => $mail->hasTo($student->email) && $mail->eventType === 'enrollment_cancelled');
    }

    public function test_reminders_are_persistent_and_only_for_scheduled_member_sessions(): void
    {
        $student = $this->coreStudent();
        $teacher = $this->coreTeacher();
        $class = $this->coreClass($teacher);
        $this->coreEnrollment($student, $class);
        $this->coreSession($class, ['thoi_gian_bat_dau' => now()->addHours(2), 'thoi_gian_ket_thuc' => now()->addHours(3)]);
        $this->coreSession($class, ['trang_thai' => 'cancelled', 'thoi_gian_bat_dau' => now()->addHours(4), 'thoi_gian_ket_thuc' => now()->addHours(5)]);
        Mail::fake();
        $this->artisan('supporting:remind', ['--hours' => 24])->assertExitCode(0);
        $this->artisan('supporting:remind', ['--hours' => 24])->assertExitCode(0);
        Mail::assertSentCount(2);
        $this->assertSame(2, DB::table('business_notifications')->where('event_type', 'session_reminder')->count());
    }

    public function test_an_inflight_mail_cannot_be_sent_again_by_another_delivery_pass(): void
    {
        DB::table('business_notifications')->insert(['dedupe_key' => 'test-concurrent-reminder', 'recipient' => 'student@example.test',
            'event_type' => 'session_reminder', 'body' => 'A lesson reminder', 'created_at' => now(), 'updated_at' => now()]);
        $entered = false;
        Event::listen(MessageSending::class, function () use (&$entered) {
            if ($entered) {
                return;
            }
            $entered = true;
            app(SupportEvents::class)->retryPending();
        });
        app(SupportEvents::class)->retryPending();
        $this->assertSame(1, Mail::mailer('array')->getSymfonyTransport()->messages()->count());
        $this->assertNotNull(DB::table('business_notifications')->first()->sent_at);
    }

    public function test_failed_delivery_keeps_notification_retryable_without_claiming_sent(): void
    {
        DB::table('business_notifications')->insert(['dedupe_key' => 'retry-reminder', 'recipient' => 'student@example.test',
            'event_type' => 'session_reminder', 'body' => 'A lesson reminder', 'created_at' => now(), 'updated_at' => now()]);
        Event::listen(MessageSending::class, fn () => throw new \RuntimeException('Simulated mail unavailable'));
        app(SupportEvents::class)->retryPending();
        $notification = DB::table('business_notifications')->first();
        $this->assertNull($notification->sent_at);
        $this->assertNull($notification->claimed_at);
        $this->assertSame(0, Mail::mailer('array')->getSymfonyTransport()->messages()->count());
        Event::forget(MessageSending::class);
        app(SupportEvents::class)->retryPending();
        $this->assertNotNull(DB::table('business_notifications')->first()->sent_at);
        $this->assertSame(1, Mail::mailer('array')->getSymfonyTransport()->messages()->count());
    }
}
