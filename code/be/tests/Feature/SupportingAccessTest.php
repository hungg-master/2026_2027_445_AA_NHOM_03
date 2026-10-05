<?php

namespace Tests\Feature;

use App\Mail\AccountResetMail;
use App\Models\Admin;
use App\Models\MonHoc;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\Support\CoreFixtures;
use Tests\TestCase;

class SupportingAccessTest extends TestCase
{
    use CoreFixtures, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->prepareCoreFixtures();
    }

    public function test_password_reset_is_role_bound_one_use_and_does_not_expose_token(): void
    {
        Mail::fake();
        $student = $this->coreStudent(['email' => 'shared@example.test']);
        $teacher = $this->coreTeacher(['email' => 'shared@example.test']);
        $oldToken = $student->createToken('before-reset')->plainTextToken;
        $response = $this->postJson('/api/auth/forgot-password', ['role' => 'hoc_vien', 'email' => $student->email]);
        $response->assertOk()->assertJsonPath('status', true)->assertJsonMissingPath('data.token');
        $mail = Mail::sent(AccountResetMail::class)->first();
        $this->assertNotNull($mail);
        $payload = ['role' => 'hoc_vien', 'email' => $student->email, 'token' => $mail->token,
            'password' => 'new-password-123', 'password_confirmation' => 'new-password-123'];
        $this->postJson('/api/auth/reset-password', array_merge($payload, ['role' => 'giao_vien']))->assertStatus(422);
        $this->postJson('/api/auth/reset-password', $payload)->assertOk()->assertJsonPath('status', true);
        $this->assertTrue(Hash::check('new-password-123', $student->fresh()->password));
        $this->assertTrue(Hash::check('test-password', $teacher->fresh()->password));
        $this->postJson('/api/auth/reset-password', $payload)->assertStatus(422);
        Auth::forgetGuards();
        $this->withToken($oldToken)->getJson('/api/hoc-vien/profile/data')->assertStatus(401);
    }

    public function test_expired_reset_cannot_change_password_and_unknown_email_has_same_response(): void
    {
        Mail::fake();
        $student = $this->coreStudent();
        $known = $this->postJson('/api/auth/forgot-password', ['role' => 'hoc_vien', 'email' => $student->email]);
        $unknown = $this->postJson('/api/auth/forgot-password', ['role' => 'hoc_vien', 'email' => 'missing@example.test']);
        $this->assertSame($known->json(), $unknown->json());
        $mail = Mail::sent(AccountResetMail::class)->first();
        $this->assertNotNull($mail);
        $this->travel(61)->minutes();
        $this->postJson('/api/auth/reset-password', ['role' => 'hoc_vien', 'email' => $student->email,
            'token' => $mail->token, 'password' => 'new-password-123', 'password_confirmation' => 'new-password-123'])->assertStatus(422);
        $this->assertTrue(Hash::check('test-password', $student->fresh()->password));
    }

    public function test_legacy_case_ambiguous_emails_never_reset_an_arbitrary_account(): void
    {
        Mail::fake();
        $student = $this->coreStudent(['email' => 'legacy@example.test']);
        $this->postJson('/api/auth/forgot-password', ['role' => 'hoc_vien', 'email' => $student->email])->assertOk();
        $token = Mail::sent(AccountResetMail::class)->first()->token;
        DB::table('hoc_viens')->insert(['ho_ten' => 'Legacy case duplicate', 'email' => 'Legacy@Example.Test',
            'password' => Hash::make('another-password'), 'created_at' => now(), 'updated_at' => now()]);
        Mail::fake();
        $ambiguous = $this->postJson('/api/auth/forgot-password', ['role' => 'hoc_vien', 'email' => 'LEGACY@EXAMPLE.TEST'])->assertOk();
        $unknown = $this->postJson('/api/auth/forgot-password', ['role' => 'hoc_vien', 'email' => 'missing@example.test'])->assertOk();
        $this->assertSame($unknown->json(), $ambiguous->json());
        Mail::assertNothingSent();
        $this->postJson('/api/auth/reset-password', ['role' => 'hoc_vien', 'email' => $student->email, 'token' => $token,
            'password' => 'new-password-123', 'password_confirmation' => 'new-password-123'])->assertStatus(422);
        $this->assertTrue(Hash::check('test-password', $student->fresh()->password));
    }

    public function test_bank_history_is_private_masked_and_ignores_submitted_teacher_id(): void
    {
        $teacher = $this->coreTeacher(['trang_thai_duyet' => 'cho_duyet']);
        $other = $this->coreTeacher();
        $student = $this->coreStudent();
        $this->asCoreActor($teacher)->postJson('/api/giao-vien/ngan-hang', [
            'id_giao_vien' => $other->id, 'bank_name' => 'Test bank', 'account_holder' => 'Teacher',
            'account_number' => '123456789012',
        ])->assertCreated()->assertJsonPath('data.account_last_four', '9012')->assertJsonMissingPath('data.account_number');
        $this->asCoreActor($other)->getJson('/api/giao-vien/ngan-hang')->assertOk()->assertJsonCount(0, 'data');
        $this->asCoreActor($student)->getJson('/api/giao-vien/ngan-hang')->assertStatus(401);
        $this->asCoreActor($teacher)->getJson('/api/giao-vien/ngan-hang')->assertOk()->assertJsonCount(1, 'data');
        $this->assertStringNotContainsString('123456789012', DB::table('teacher_bank_accounts')->first()->account_number_encrypted);
        $this->postJson('/api/giao-vien/ngan-hang', ['bank_name' => 'Second bank', 'account_holder' => 'Teacher', 'account_number' => '999999999999'])->assertCreated();
        $this->assertSame(1, DB::table('teacher_bank_accounts')->where('id_giao_vien', $teacher->id)->where('is_current', 1)->count());
        $this->getJson('/api/giao-vien/ngan-hang')->assertOk()->assertJsonCount(2, 'data')->assertJsonMissingPath('data.0.account_number_encrypted');
    }

    public function test_admin_catalog_crud_rejects_referenced_delete_and_student_mutation(): void
    {
        $admin = Admin::create(['ho_ten' => 'Admin', 'email' => 'admin@example.test', 'password' => 'test-password', 'tinh_trang' => 1]);
        Auth::forgetGuards();
        $this->withToken($admin->createToken('test')->plainTextToken);
        $created = $this->postJson('/api/admin/mon-hoc', ['ten_mon_hoc' => 'Physics', 'tinh_trang' => 'hoat_dong'])->assertCreated();
        $id = $created->json('data.id');
        $this->putJson('/api/admin/mon-hoc/'.$id, ['ten_mon_hoc' => 'Physics 2', 'tinh_trang' => 'hoat_dong'])->assertOk();
        $this->coreClass(null, MonHoc::findOrFail($id));
        $this->deleteJson('/api/admin/mon-hoc/'.$id)->assertStatus(409);
        $room = $this->postJson('/api/admin/phong-hoc', ['so_phong' => 'P102', 'dia_chi' => 'Campus'])->assertCreated()->json('data.id');
        $this->deleteJson('/api/admin/phong-hoc/'.$room)->assertOk();
        $referenced = $this->corePhysicalRoom();
        $this->coreClass(null, null, ['hinh_thuc' => 'offline', 'id_phong_hoc' => $referenced->id]);
        $this->deleteJson('/api/admin/phong-hoc/'.$referenced->id)->assertStatus(409);
        $this->asCoreActor($this->coreStudent())->postJson('/api/admin/mon-hoc', ['ten_mon_hoc' => 'Forged'])->assertStatus(401);
    }

    public function test_chat_is_persistent_two_party_and_incremental_with_equal_role_ids(): void
    {
        $student = $this->coreStudent();
        $teacher = $this->coreTeacher();
        $outsider = $this->coreStudent();
        $conversation = $this->asCoreActor($student)->postJson('/api/chat/conversations', ['id_giao_vien' => $teacher->id])->assertSuccessful()->json('data.id');
        $first = $this->postJson('/api/chat/conversations/'.$conversation.'/messages', ['body' => 'First message'])->assertCreated()->json('data.id');
        $this->asCoreActor($teacher)->postJson('/api/chat/conversations/'.$conversation.'/messages', ['body' => 'Reply'])->assertCreated()->assertJsonPath('data.sender_role', 'giao_vien');
        $this->asCoreActor($student)->getJson('/api/chat/conversations/'.$conversation.'/messages?after_id='.$first)->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.body', 'Reply');
        $this->asCoreActor($outsider)->getJson('/api/chat/conversations/'.$conversation.'/messages')->assertStatus(404);
        $this->postJson('/api/chat/conversations/'.$conversation.'/messages', ['body' => 'Forbidden'])->assertStatus(404);
        $this->assertDatabaseCount('consultation_messages', 2);
    }

    public function test_ai_unconfigured_and_provider_failure_are_honest_and_context_is_owned(): void
    {
        $student = $this->coreStudent();
        config(['supporting.ai.url' => null, 'supporting.ai.key' => null]);
        $this->asCoreActor($student)->postJson('/api/ai/assist', ['message' => 'Help'])->assertStatus(503)->assertJsonPath('status', false);
        config(['supporting.ai.url' => 'https://provider.example.test/chat', 'supporting.ai.key' => 'test-secret', 'supporting.ai.model' => 'test', 'supporting.ai.daily_limit' => 2]);
        Http::fake(['*' => Http::sequence()->push(['error' => 'secret provider detail'], 500)
            ->push(['choices' => [['message' => ['content' => 'Study reply']]]])]);
        $this->postJson('/api/ai/assist', ['message' => 'Help'])->assertStatus(503)->assertJsonMissing(['message' => 'secret provider detail']);
        $this->postJson('/api/ai/assist', ['message' => 'Help'])->assertOk()->assertJsonPath('data.reply', 'Study reply');
        $this->postJson('/api/ai/assist', ['message' => 'Over quota'])->assertStatus(429);
        $other = $this->coreStudent();
        $teacher = $this->coreTeacher();
        $conversation = $this->asCoreActor($other)->postJson('/api/chat/conversations', ['id_giao_vien' => $teacher->id])->json('data.id');
        $this->asCoreActor($student)->postJson('/api/ai/assist', ['message' => 'Leak', 'conversation_id' => $conversation])->assertStatus(404);
    }

    public function test_ai_connection_timeout_is_reported_without_provider_exception_or_fake_reply(): void
    {
        config(['supporting.ai.url' => 'https://provider.example.test/chat', 'supporting.ai.key' => 'test-secret', 'supporting.ai.model' => 'test']);
        Http::fake(fn () => throw new ConnectionException('Secret provider diagnostic'));
        $response = $this->asCoreActor($this->coreStudent())->postJson('/api/ai/assist', ['message' => 'Help']);
        $response->assertStatus(503)->assertJsonPath('status', false)->assertJsonMissingPath('data.reply');
        $this->assertStringNotContainsString('Secret provider diagnostic', $response->getContent());
        $this->assertDatabaseHas('ai_daily_usage', ['actor_role' => 'hoc_vien', 'requests' => 1]);
    }
}
