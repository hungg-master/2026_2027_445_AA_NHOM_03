<?php

namespace App\Http\Controllers;

use App\Models\DongGopYKien;
use Illuminate\Http\Request;

class DongGopYKienController extends Controller
{
    /**
     * Gửi đóng góp ý kiến (public, không cần đăng nhập)
     */
    public function store(Request $request)
    {
        $request->validate([
            'ho_ten'        => 'required|string|max:100',
            'email'         => 'nullable|email|max:150',
            'loai_phan_hoi' => 'required|in:gop_y,bao_loi,khen_ngoi,khieu_nai,khac',
            'diem_danh_gia' => 'nullable|integer|min:1|max:5',
            'noi_dung'      => 'required|string|min:10|max:2000',
        ], [
            'ho_ten.required'        => 'Vui lòng nhập họ tên.',
            'email.email'            => 'Email không đúng định dạng.',
            'loai_phan_hoi.required' => 'Vui lòng chọn loại phản hồi.',
            'loai_phan_hoi.in'       => 'Loại phản hồi không hợp lệ.',
            'diem_danh_gia.min'      => 'Điểm đánh giá tối thiểu là 1.',
            'diem_danh_gia.max'      => 'Điểm đánh giá tối đa là 5.',
            'noi_dung.required'      => 'Vui lòng nhập nội dung góp ý.',
            'noi_dung.min'           => 'Nội dung góp ý phải có ít nhất 10 ký tự.',
        ]);

        try {
            $feedback = DongGopYKien::create([
                'ho_ten'        => $request->ho_ten,
                'email'         => $request->email,
                'loai_phan_hoi' => $request->loai_phan_hoi,
                'diem_danh_gia' => $request->diem_danh_gia,
                'noi_dung'      => $request->noi_dung,
                'trang_thai'    => 'chua_xu_ly',
            ]);

            return response()->json([
                'status'  => true,
                'message' => 'Cảm ơn bạn đã gửi đóng góp ý kiến! Chúng tôi sẽ xem xét và phản hồi sớm nhất.',
                'data'    => $feedback,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Có lỗi xảy ra, vui lòng thử lại.',
            ], 500);
        }
    }

    /**
     * Lấy danh sách đóng góp ý kiến (chỉ Admin)
     */
    public function index()
    {
        $feedbacks = DongGopYKien::orderBy('created_at', 'desc')->get();

        return response()->json([
            'status' => true,
            'data'   => $feedbacks,
        ]);
    }
}
