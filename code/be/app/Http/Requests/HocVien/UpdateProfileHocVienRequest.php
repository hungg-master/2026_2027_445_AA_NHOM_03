<?php

namespace App\Http\Requests\HocVien;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileHocVienRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ho_ten'        => 'required|string|max:100|min:2',
            'so_dien_thoai' => 'required|digits:10',
            'ngay_sinh'     => 'nullable|date',
            'gioi_tinh'     => 'nullable|integer',
            'dia_chi'       => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'ho_ten.required'        => 'Họ tên không được để trống',
            'so_dien_thoai.required' => 'Số điện thoại không được để trống',
            'so_dien_thoai.digits'   => 'Số điện thoại phải có 10 chữ số',
            'ngay_sinh.date'         => 'Ngày sinh không đúng định dạng',
        ];
    }
}
