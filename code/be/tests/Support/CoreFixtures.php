<?php

namespace Tests\Support;

use App\Models\BuoiHoc;
use App\Models\DangKyLop;
use App\Models\GiaoVien;
use App\Models\HocVien;
use App\Models\LopHoc;
use App\Models\MonHoc;
use App\Models\PhongHoc;
use App\Models\PhongHop;
use App\Models\ThoiGianRanh;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Testing\TestResponse;

trait CoreFixtures
{
    private int $fixtureSequence = 0;

    protected function prepareCoreFixtures(): void
    {
        config(['app.timezone' => 'Asia/Ho_Chi_Minh']);
        Carbon::setTestNow(Carbon::parse('2026-10-04 08:00:00', 'Asia/Ho_Chi_Minh'));
        $this->beforeApplicationDestroyed(fn () => Carbon::setTestNow());
    }

    protected function coreTeacher(array $attributes = []): GiaoVien
    {
        return GiaoVien::create(array_merge([
            'ho_ten' => 'Teacher '.++$this->fixtureSequence,
            'email' => 'teacher'.$this->fixtureSequence.'@example.test',
            'password' => 'test-password',
            'trang_thai_duyet' => 'da_duyet',
            'tinh_trang' => 1, 'is_active' => 1, 'is_block' => 0,
        ], $attributes));
    }

    protected function coreStudent(array $attributes = []): HocVien
    {
        return HocVien::create(array_merge([
            'ho_ten' => 'Student '.++$this->fixtureSequence,
            'email' => 'student'.$this->fixtureSequence.'@example.test',
            'password' => 'test-password',
            'tinh_trang' => 1, 'is_active' => 1, 'is_block' => 0,
        ], $attributes));
    }

    protected function coreSubject(): MonHoc
    {
        return MonHoc::create([
            'ten_mon_hoc' => 'Subject '.++$this->fixtureSequence,
            'tinh_trang' => 'hoat_dong',
        ]);
    }

    protected function corePhysicalRoom(): PhongHoc
    {
        return PhongHoc::create(['so_phong' => 'P'.++$this->fixtureSequence, 'dia_chi' => 'Test campus']);
    }

    protected function coreClass(?GiaoVien $teacher = null, ?MonHoc $subject = null, array $attributes = []): LopHoc
    {
        return LopHoc::create(array_merge([
            'id_giao_vien' => ($teacher ?? $this->coreTeacher())->id,
            'id_mon_hoc' => ($subject ?? $this->coreSubject())->id,
            'id_phong_hoc' => null, 'hinh_thuc' => 'online', 'loai_lop' => 'kem',
            'hoc_phi' => 100000, 'si_so_toi_da' => 5,
            'thoi_gian_bat_dau' => '2026-10-11 09:00:00',
            'thoi_gian_ket_thuc' => '2026-10-11 10:00:00',
            'tinh_trang' => 'dang_mo',
        ], $attributes));
    }

    protected function coreSession(LopHoc $class, array $attributes = []): BuoiHoc
    {
        return BuoiHoc::create(array_merge([
            'id_lop_hoc' => $class->id,
            'thoi_gian_bat_dau' => $class->thoi_gian_bat_dau,
            'thoi_gian_ket_thuc' => $class->thoi_gian_ket_thuc,
            'trang_thai' => 'scheduled',
        ], $attributes));
    }

    protected function coreEnrollment(HocVien $student, LopHoc $class, string $status = 'da_xac_nhan'): DangKyLop
    {
        return DangKyLop::create([
            'id_hoc_vien' => $student->id, 'id_lop_hoc' => $class->id,
            'ngay_dang_ky' => now(), 'trang_thai' => $status,
        ]);
    }

    protected function coreRoom(LopHoc $class): PhongHop
    {
        return PhongHop::create([
            'ma_phong' => 'TEST-'.++$this->fixtureSequence,
            'ten_phong' => 'Test online class', 'id_chu_phong' => $class->id_giao_vien,
            'id_lop_hoc' => $class->id, 'so_nguoi_toi_da' => $class->si_so_toi_da + 1,
            'thoi_gian_bat_dau' => $class->thoi_gian_bat_dau,
            'thoi_gian_ket_thuc' => $class->thoi_gian_ket_thuc, 'trang_thai' => 1,
        ]);
    }

    protected function coreAvailability(HocVien|GiaoVien $actor, int $day, string $start, string $end): ThoiGianRanh
    {
        $teacher = $actor instanceof GiaoVien;

        return ThoiGianRanh::create([
            'loai_nguoi_dung' => $teacher ? 'giao_vien' : 'hoc_vien',
            'id_giao_vien' => $teacher ? $actor->id : null,
            'id_hoc_vien' => $teacher ? null : $actor->id,
            'ngay_trong_tuan' => $day,
            'thoi_gian_bat_dau' => $start, 'thoi_gian_ket_thuc' => $end,
            'trang_thai' => 'hoat_dong',
        ]);
    }

    protected function asCoreActor(HocVien|GiaoVien $actor): static
    {
        Auth::forgetGuards();

        return $this->withToken($actor->createToken('core-regression')->plainTextToken);
    }

    protected function coreDescriptor(float $value = 0.01): array
    {
        return array_fill(0, 128, $value);
    }

    protected function coreProof(HocVien|GiaoVien $actor, string $purpose, int $target): array
    {
        $prefix = $actor instanceof GiaoVien ? 'giao-vien' : 'hoc-vien';
        if (! $actor->fresh()->du_lieu_khuon_mat) {
            $this->asCoreActor($actor)->postJson('/api/'.$prefix.'/face-id/sample', [
                'descriptor' => $this->coreDescriptor(),
            ])->assertSuccessful()->assertJsonPath('data.has_face_id', true);
        }
        $response = $this->asCoreActor($actor)->postJson('/api/'.$prefix.'/face-id/verify', [
            'descriptor' => $this->coreDescriptor(), 'purpose' => $purpose,
            $purpose === 'room' ? 'id_buoi_hoc' : 'id_lop_hoc' => $target,
        ])->assertSuccessful()->assertJsonPath('status', true);
        $this->assertNotEmpty($response->json('data.verification_id'));
        $this->assertNotEmpty($response->json('data.expires_at'));

        return $response->json('data');
    }

    protected function assertCoreRejected(TestResponse $response): void
    {
        $this->assertContains($response->status(), [400, 401, 403, 404, 409, 422], $response->getContent());
    }

    protected function coreClassPayload(MonHoc $subject, array $attributes = []): array
    {
        return array_merge([
            'id_mon_hoc' => $subject->id, 'loai_lop' => 'kem', 'hinh_thuc' => 'online',
            'hoc_phi' => 100000, 'si_so_toi_da' => 5,
            'thoi_gian_bat_dau' => '2026-10-11T09:00:00+07:00',
            'thoi_gian_ket_thuc' => '2026-10-11T10:00:00+07:00',
            'recurrence' => 'once',
        ], $attributes);
    }
}
