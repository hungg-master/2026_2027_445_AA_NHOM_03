<?php

namespace App\Http\Requests\GiaoVien;

use Illuminate\Foundation\Http\FormRequest;

class DangKyGiaoVienRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ho_ten'             => 'required|string|max:100|min:2',
            'email'              => 'required|email|unique:giao_viens,email',
            'password'           => 'required|min:6|max:50',
            're_password'        => 'required|same:password',
            'so_dien_thoai'      => 'required|digits:10',
            'chuc_danh'          => 'nullable|string|max:100',
            'so_nam_kinh_nghiem' => 'nullable|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'ho_ten.required'             => 'Họ tên giáo viên không được để trống',
            'ho_ten.min'                  => 'Họ tên phải có ít nhất 2 ký tự',
            'email.required'              => 'Email không được để trống',
            'email.email'                 => 'Email không đúng định dạng',
            'email.unique'                => 'Email này đã được đăng ký tài khoản giáo viên',
            'password.required'           => 'Mật khẩu không được để trống',
            'password.min'                => 'Mật khẩu phải có ít nhất 6 ký tự',
            're_password.required'        => 'Mật khẩu nhập lại không được để trống',
            're_password.same'            => 'Mật khẩu nhập lại không khớp',
            'so_dien_thoai.required'      => 'Số điện thoại không được để trống',
            'so_dien_thoai.digits'        => 'Số điện thoại phải bao gồm đúng 10 chữ số',
            'so_nam_kinh_nghiem.integer'  => 'Số năm kinh nghiệm phải là số nguyên',
        ];
    }
}
