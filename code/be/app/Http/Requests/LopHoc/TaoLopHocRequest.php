<?php

namespace App\Http\Requests\LopHoc;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation cho việc tạo/cập nhật lớp học
 */
class TaoLopHocRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Đã check qua middleware
    }

    public function rules(): array
    {
        $isOnline = $this->input('hinh_thuc') === 'online';
        $loaiLop = $this->input('loai_lop');
        $maxSiSo = $loaiLop === 'kem' ? 5 : 30;

        return [
            'id_mon_hoc' => 'required|exists:mon_hocs,id',
            'id_phong_hoc' => [
                Rule::requiredIf(!$isOnline),
                'nullable',
                'exists:phong_hocs,id',
            ],
            'loai_lop' => 'required|in:dai_tra,kem',
            'hinh_thuc' => 'required|in:online,offline',
            'link_online' => [
                Rule::requiredIf($isOnline),
                'nullable',
                'url',
                'max:500',
            ],
            'hoc_phi' => 'required|numeric|min:0|max:999999999.99',
            'si_so_toi_da' => "required|integer|min:1|max:$maxSiSo",
            'thoi_gian_bat_dau' => 'required|date|after:now',
            'thoi_gian_ket_thuc' => 'required|date|after:thoi_gian_bat_dau',
            'tinh_trang' => 'nullable|in:sap_mo,dang_mo,dang_hoc,da_ket_thuc,da_huy',
        ];
    }

    public function messages(): array
    {
        return [
            'id_mon_hoc.required' => 'Vui lòng chọn môn học.',
            'id_mon_hoc.exists' => 'Môn học không tồn tại.',
            'id_phong_hoc.required' => 'Lớp offline phải chọn phòng học.',
            'id_phong_hoc.exists' => 'Phòng học không tồn tại.',
            'loai_lop.required' => 'Vui lòng chọn loại lớp.',
            'loai_lop.in' => 'Loại lớp không hợp lệ.',
            'hinh_thuc.required' => 'Vui lòng chọn hình thức dạy.',
            'hinh_thuc.in' => 'Hình thức không hợp lệ.',
            'link_online.required' => 'Lớp online phải có link meeting.',
            'link_online.url' => 'Link online không đúng định dạng URL.',
            'hoc_phi.required' => 'Vui lòng nhập học phí.',
            'hoc_phi.numeric' => 'Học phí phải là số.',
            'hoc_phi.min' => 'Học phí không được âm.',
            'si_so_toi_da.required' => 'Vui lòng nhập sĩ số tối đa.',
            'si_so_toi_da.integer' => 'Sĩ số phải là số nguyên.',
            'si_so_toi_da.max' => 'Lớp kèm tối đa 5 người, đại trà tối đa 30 người.',
            'thoi_gian_bat_dau.required' => 'Vui lòng chọn thời gian bắt đầu.',
            'thoi_gian_bat_dau.after' => 'Thời gian bắt đầu phải sau thời điểm hiện tại.',
            'thoi_gian_ket_thuc.required' => 'Vui lòng chọn thời gian kết thúc.',
            'thoi_gian_ket_thuc.after' => 'Thời gian kết thúc phải sau thời gian bắt đầu.',
        ];
    }
}
