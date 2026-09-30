<?php

namespace App\Http\Requests\HocVien;

use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordHocVienRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'current_password' => 'required',
            'password'         => 'required|min:6|max:50',
            're_password'      => 'required|same:password',
        ];
    }

    public function messages(): array
    {
        return [
            'current_password.required' => 'Mật khẩu hiện tại không được để trống',
            'password.required'         => 'Mật khẩu mới không được để trống',
            'password.min'              => 'Mật khẩu mới phải có ít nhất 6 ký tự',
            're_password.required'      => 'Mật khẩu xác nhận không được để trống',
            're_password.same'          => 'Mật khẩu xác nhận không khớp',
        ];
    }
}
