<?php

namespace App\Http\Controllers;

use App\Services\SupportTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class TeacherBankController extends SupportController
{
    public function index()
    {
        return $this->ok(DB::table('teacher_bank_accounts')->where('id_giao_vien', Auth::guard('sanctum')->id())
            ->select('id', 'bank_name', 'account_holder', 'account_last_four', 'is_current', 'created_at')->orderByDesc('id')->get());
    }

    public function store(Request $request)
    {
        $data = $this->input($request, ['bank_name' => 'required|string|max:150', 'account_holder' => 'required|string|max:150',
            'account_number' => ['required', 'string', 'regex:/^[0-9]{6,34}$/']]);
        $id = SupportTransaction::run(function () use ($data) {
            $teacherId = Auth::guard('sanctum')->id();
            DB::table('teacher_bank_accounts')->where('id_giao_vien', $teacherId)->update(['is_current' => false, 'updated_at' => now()]);

            return DB::table('teacher_bank_accounts')->insertGetId(['id_giao_vien' => $teacherId,
                'bank_name' => $data['bank_name'], 'account_holder' => $data['account_holder'],
                'account_number_encrypted' => Crypt::encryptString($data['account_number']),
                'account_last_four' => substr($data['account_number'], -4), 'is_current' => true, 'created_at' => now(), 'updated_at' => now()]);
        });

        return $this->ok(DB::table('teacher_bank_accounts')->where('id', $id)
            ->select('id', 'bank_name', 'account_holder', 'account_last_four', 'is_current', 'created_at')->first(), 'Đã lưu thông tin ngân hàng.', 201);
    }
}
