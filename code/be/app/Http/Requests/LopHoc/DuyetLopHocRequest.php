<?php

namespace App\Http\Requests\LopHoc;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation cho duyệt lớp học (Admin)
 */
class DuyetLopHocRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => 'required|exists:lop_hocs,id',
            'tinh_trang' => 'required|in:sap_mo,dang_mo,da_huy',
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'Thiếu ID lớp học.',
            'id.exists' => 'Lớp học không tồn tại.',
            'tinh_trang.required' => 'Vui lòng chọn trạng thái mới.',
            'tinh_trang.in' => 'Trạng thái không hợp lệ.',
        ];
    }
}
