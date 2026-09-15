<?php

namespace App\Http\Requests\DangKyLop;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation cho đăng ký lớp học
 */
class DangKyLopRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_lop_hoc' => 'required|exists:lop_hocs,id',
        ];
    }

    public function messages(): array
    {
        return [
            'id_lop_hoc.required' => 'Thiếu ID lớp học.',
            'id_lop_hoc.exists' => 'Lớp học không tồn tại.',
        ];
    }
}
