<?php

namespace Tests\Feature;

use App\Models\GiaoVien;
use App\Models\HocVien;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ApiRegressionTest extends TestCase
{
    use RefreshDatabase;

    private function teacher(): GiaoVien
    {
        return GiaoVien::create([
            'ho_ten' => 'Test Teacher', 'email' => 'teacher@example.test',
            'password' => 'test-password', 'tinh_trang' => 1, 'is_block' => 0,
            'trang_thai_duyet' => 'da_duyet',
        ]);
    }

    private function student(): HocVien
    {
        return HocVien::create([
            'ho_ten' => 'Test Student', 'email' => 'student@example.test',
            'password' => 'test-password', 'tinh_trang' => 1, 'is_block' => 0,
        ]);
    }

    public function test_default_admin_cannot_be_created_from_public_api(): void
    {
        $this->getJson('/api/admin/tao-admin-mac-dinh')->assertNotFound();
        $this->assertDatabaseCount('admins', 0);
    }

    public function test_teacher_bearer_token_can_read_classes_and_save_schedule(): void
    {
        $teacher = $this->teacher();
        $token = $teacher->createToken('test')->plainTextToken;
        $this->withToken($token)->getJson('/api/giao-vien/lop-hoc')->assertOk()->assertJsonPath('data', []);
        Auth::forgetGuards();
        $this->withToken($token)->postJson('/api/giao-vien/thoi-gian-ranh', ['schedules' => [[
            'ngay_trong_tuan' => 1, 'thoi_gian_bat_dau' => '09:00', 'thoi_gian_ket_thuc' => '10:00',
        ]]])->assertOk();
        Auth::forgetGuards();
        $this->withToken($token)->getJson('/api/giao-vien/thoi-gian-ranh')
            ->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.id_giao_vien', $teacher->id);
    }

    public function test_student_cannot_access_teacher_routes(): void
    {
        $token = $this->student()->createToken('test')->plainTextToken;
        $this->withToken($token)->getJson('/api/giao-vien/lop-hoc')->assertUnauthorized();
    }

    public function test_logout_revokes_the_current_bearer_token(): void
    {
        $student = $this->student();
        $token = $student->createToken('test')->plainTextToken;
        $this->withToken($token)->postJson('/api/hoc-vien/logout')->assertOk();
        $this->assertDatabaseCount('personal_access_tokens', 0);
        Auth::forgetGuards();
        $this->withToken($token)->getJson('/api/hoc-vien/profile/data')->assertUnauthorized();
    }

    public function test_face_photo_is_private_and_replaced_without_profile_fields(): void
    {
        Storage::fake('local');
        foreach ([$this->student(), $this->teacher()] as $user) {
            $prefix = $user instanceof HocVien ? 'hoc-vien' : 'giao-vien';
            $token = $user->createToken('test')->plainTextToken;
            $oldPath = null;
            for ($i = 0; $i < 2; $i++) {
                Auth::forgetGuards();
                $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+jhS0AAAAASUVORK5CYII=');
                $file = UploadedFile::fake()->createWithContent('face.png', $png);
                $this->withToken($token)->postJson('/api/'.$prefix.'/profile/face-id', ['face_id_photo' => $file])
                    ->assertOk()->assertJsonPath('status', true);
                $path = $user->fresh()->face_id_photo_path;
                Storage::disk('local')->assertExists($path);
                if ($oldPath) {
                    Storage::disk('local')->assertMissing($oldPath);
                }
                $oldPath = $path;
            }
            Auth::forgetGuards();
            $this->withToken($token)->getJson('/api/'.$prefix.'/profile/data')
                ->assertOk()->assertJsonMissingPath('data.face_id_photo_path');
        }
    }

    public function test_face_photo_rejects_non_images_and_anonymous_requests(): void
    {
        Storage::fake('local');
        $this->postJson('/api/hoc-vien/profile/face-id')->assertUnauthorized();
        Auth::forgetGuards();
        $token = $this->student()->createToken('test')->plainTextToken;
        $this->withToken($token)->postJson('/api/hoc-vien/profile/face-id', [
            'face_id_photo' => UploadedFile::fake()->createWithContent('face.png', 'not an image'),
        ])->assertUnprocessable()->assertJsonValidationErrors('face_id_photo');
    }

    public function test_trial_booking_is_persisted_and_invalid_slots_rejected(): void
    {
        $payload = ['name' => 'Test Student', 'phone' => '0901234567', 'subject' => 'Math', 'schedules' => ['Mon_9:00 AM']];
        $this->postJson('/api/hoc-thu', $payload)->assertCreated()->assertJsonPath('status', true);
        $this->assertDatabaseHas('trial_bookings', ['phone' => '0901234567', 'status' => 'pending']);
        $payload['schedules'] = ['invalid'];
        $this->postJson('/api/hoc-thu', $payload)->assertUnprocessable();
        $this->assertDatabaseCount('trial_bookings', 1);
    }
}
