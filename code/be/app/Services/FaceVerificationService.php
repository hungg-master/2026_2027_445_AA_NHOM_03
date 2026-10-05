<?php

namespace App\Services;

use App\Models\GiaoVien;
use App\Models\HocVien;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class FaceVerificationService
{
    public static function role(HocVien|GiaoVien $actor): string
    {
        return $actor instanceof GiaoVien ? 'giao_vien' : 'hoc_vien';
    }

    public function descriptor(mixed $value): array
    {
        if (! is_array($value) || ! array_is_list($value) || count($value) !== 128) {
            throw ValidationException::withMessages(['descriptor' => 'Cần đúng 128 giá trị khuôn mặt.']);
        }
        foreach ($value as $number) {
            if ((! is_int($number) && ! is_float($number)) || ! is_finite((float) $number)) {
                throw ValidationException::withMessages(['descriptor' => 'Giá trị khuôn mặt phải là số hữu hạn.']);
            }
        }

        return $value;
    }

    public function readSample(HocVien|GiaoVien $actor): ?array
    {
        $raw = $actor->du_lieu_khuon_mat;
        if (! $raw) {
            return null;
        }
        if (is_array($raw)) {
            return $raw;
        }
        try {
            $raw = Crypt::decryptString($raw);
        } catch (DecryptException $e) { /* Legacy JSON is read once and replaced on next sample. */
        }
        try {
            return $this->descriptor(json_decode($raw, true, 512, JSON_THROW_ON_ERROR));
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function distance(array $a, array $b): float
    {
        return sqrt(array_sum(array_map(fn ($x, $y) => ($x - $y) ** 2, $a, $b)));
    }

    public function issue(HocVien|GiaoVien $actor, string $purpose, int $target): array
    {
        $token = Str::random(64);
        $expires = now()->addSeconds((int) config('smarttrial.face_proof_ttl', 120));
        DB::table('face_verifications')->insert([
            'token_hash' => hash('sha256', $token), 'actor_type' => self::role($actor), 'actor_id' => $actor->id,
            'purpose' => $purpose, 'target_id' => $target, 'expires_at' => $expires,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        return ['verification_id' => $token, 'expires_at' => $expires->toIso8601String()];
    }

    // Call inside the business transaction: failed business checks roll back consumption.
    public function consume(HocVien|GiaoVien $actor, string $token, string $purpose, int $target): void
    {
        $proof = DB::table('face_verifications')->where('token_hash', hash('sha256', $token))->lockForUpdate()->first();
        if (! $proof || $proof->actor_type !== self::role($actor) || (int) $proof->actor_id !== $actor->id
            || $proof->purpose !== $purpose || (int) $proof->target_id !== $target || $proof->consumed_at
            || now()->greaterThanOrEqualTo($proof->expires_at)) {
            throw ValidationException::withMessages(['verification_id' => 'Xác nhận không hợp lệ, đã hết hạn hoặc đã sử dụng.']);
        }
        DB::table('face_verifications')->where('id', $proof->id)->update(['consumed_at' => now(), 'updated_at' => now()]);
    }
}
