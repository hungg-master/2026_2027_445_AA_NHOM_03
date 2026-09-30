<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Admin;

Artisan::command('edulink:create-admin {email}', function () {
    $data = [
        'email' => $this->argument('email'),
        'ho_ten' => $this->ask('Họ tên'),
        'password' => $this->secret('Mật khẩu (ít nhất 12 ký tự)'),
    ];
    $validator = Validator::make($data, [
        'email' => 'required|email|unique:admins,email',
        'ho_ten' => 'required|string|max:100',
        'password' => 'required|string|min:12',
    ]);
    if ($validator->fails()) {
        foreach ($validator->errors()->all() as $error) $this->error($error);
        return 1;
    }
    Admin::create([
        ...$data,
        'password' => Hash::make($data['password']),
        'tinh_trang' => 1,
        'is_master' => 1,
    ]);
    $this->info('Đã tạo tài khoản quản trị viên.');
    return 0;
})->purpose('Tạo admin bằng mật khẩu nhập riêng trong terminal');

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
