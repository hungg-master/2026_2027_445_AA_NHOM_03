<?php

namespace App\Http\Requests\DangKyLop;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation cho duyệt học viên đăng ký (giáo viên)
 */
class DuyetHocVienDangKyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_dang_ky' => 'required|exists:dang_ky_lops,id',
            'hanh_dong' => 'required|in:duyet, tu_choi, huy',
            // Mapping: duyet -> da_xac_nhan, tu_choi -> da_huy
        ];
    }

    public function messages(): array
    {
        return [
            'id_dang_ky.required' => 'Thiếu ID đăng ký.',
            'id_dang_ky.exists' => 'Đăng ký không tồn tại.',
            'hanh_dong.required' => 'Vui lòng chọn hành động.',
            'hanh_dong.in' => 'Hành động không hợp lệ (duyet|tu_choi|huy).',
        ];
    }
}
