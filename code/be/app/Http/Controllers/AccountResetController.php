<?php

namespace App\Http\Controllers;

use App\Mail\AccountResetMail;
use App\Models\Admin;
use App\Models\GiaoVien;
use App\Models\HocVien;
use App\Services\SupportTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AccountResetController extends SupportController
{
    private const ROLES = ['hoc_vien' => HocVien::class, 'giao_vien' => GiaoVien::class, 'admin' => Admin::class];

    public function forgot(Request $request)
    {
        $data = $this->input($request, ['role' => 'required|in:hoc_vien,giao_vien,admin', 'email' => 'required|email|max:255']);
        $email = mb_strtolower(trim($data['email']));
        $actor = $this->uniqueAccount($data['role'], $email);
        if ($actor) {
            $token = Str::random(64);
            SupportTransaction::run(function () use ($data, $email, $token) {
                DB::table('account_password_resets')->updateOrInsert(['role' => $data['role'], 'email' => $email], [
                    'token_hash' => hash('sha256', $token), 'expires_at' => now()->addMinutes(config('supporting.reset_minutes', 60)),
                    'consumed_at' => null, 'created_at' => now(), 'updated_at' => now(),
                ]);
            });
            try {
                Mail::to($actor->email)->send(new AccountResetMail($data['role'], $email, $token));
            } catch (\Throwable $error) {
                report($error);
                // Keep the public reply identical for account enumeration protection.
            }
        }

        return $this->ok(null, 'Nếu tài khoản tồn tại, hướng dẫn đặt lại mật khẩu sẽ được gửi qua email.');
    }

    public function reset(Request $request)
    {
        $data = $this->input($request, ['role' => 'required|in:hoc_vien,giao_vien,admin', 'email' => 'required|email|max:255',
            'token' => 'required|string|size:64', 'password' => 'required|string|min:10|max:100|confirmed']);
        SupportTransaction::run(function () use ($data) {
            $email = mb_strtolower(trim($data['email']));
            $reset = DB::table('account_password_resets')->where(['role' => $data['role'], 'email' => $email])->first();
            if (! $reset || $reset->consumed_at || now()->greaterThanOrEqualTo($reset->expires_at) || ! hash_equals($reset->token_hash, hash('sha256', $data['token']))) {
                $this->reject('Liên kết đặt lại mật khẩu không hợp lệ hoặc đã hết hạn.', 422);
            }
            $actor = $this->uniqueAccount($data['role'], $email);
            if (! $actor) {
                $this->reject('Liên kết đặt lại mật khẩu không hợp lệ hoặc đã hết hạn.', 422);
            }
            $actor->forceFill(['password' => $data['password']])->save();
            $actor->tokens()->delete();
            DB::table('account_password_resets')->where('id', $reset->id)->update(['consumed_at' => now(), 'updated_at' => now()]);
        });

        return $this->ok(null, 'Đã đặt lại mật khẩu. Vui lòng đăng nhập lại.');
    }

    private function uniqueAccount(string $role, string $email): Admin|GiaoVien|HocVien|null
    {
        $matches = self::ROLES[$role]::whereRaw('LOWER(email) = ?', [$email])->limit(2)->get();

        // Preserve legacy ambiguous records: never select an arbitrary account.
        return $matches->count() === 1 ? $matches->first() : null;
    }
}
