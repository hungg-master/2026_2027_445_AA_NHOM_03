<?php

namespace App\Http\Requests\HocVien;

use Illuminate\Foundation\Http\FormRequest;

class DangKyHocVienRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ho_ten'        => 'required|string|max:100|min:2',
            'email'         => 'required|email|unique:hoc_viens,email',
            'password'      => 'required|min:6|max:50',
            're_password'   => 'required|same:password',
            'so_dien_thoai' => 'required|digits:10',
            'dia_chi'       => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'ho_ten.required'        => 'Họ tên học viên không được để trống',
            'ho_ten.min'             => 'Họ tên phải có ít nhất 2 ký tự',
            'email.required'         => 'Email không được để trống',
            'email.email'            => 'Email không đúng định dạng',
            'email.unique'           => 'Email này đã được đăng ký tài khoản học viên',
            'password.required'      => 'Mật khẩu không được để trống',
            'password.min'           => 'Mật khẩu phải có ít nhất 6 ký tự',
            're_password.required'   => 'Mật khẩu xác nhận không được để trống',
            're_password.same'       => 'Mật khẩu xác nhận không khớp',
            'so_dien_thoai.required' => 'Số điện thoại không được để trống',
            'so_dien_thoai.digits'   => 'Số điện thoại phải bao gồm đúng 10 chữ số',
        ];
    }
}
