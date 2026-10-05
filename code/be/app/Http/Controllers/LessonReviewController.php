<?php

namespace App\Http\Controllers;

use App\Models\BuoiHoc;
use App\Models\DangKyLop;
use App\Services\SupportTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LessonReviewController extends SupportController
{
    public function studentIndex()
    {
        return $this->ok($this->query()->where('r.id_hoc_vien', Auth::guard('sanctum')->id())->orderByDesc('r.id')->get());
    }

    public function teacherIndex()
    {
        return $this->ok($this->query()->where('r.id_giao_vien', Auth::guard('sanctum')->id())->orderByDesc('r.id')->get());
    }

    public function store(Request $request)
    {
        $data = $this->input($request, ['id_buoi_hoc' => 'required|integer|min:1', 'rating' => 'required|integer|min:1|max:5', 'comment' => 'nullable|string|max:2000']);
        $review = SupportTransaction::run(function () use ($data) {
            $studentId = Auth::guard('sanctum')->id();
            $session = BuoiHoc::find($data['id_buoi_hoc']);
            if (! $session || $session->trang_thai !== 'completed' || now()->lessThan($session->thoi_gian_ket_thuc)) {
                $this->reject('Chỉ được đánh giá buổi học đã hoàn thành.', 403);
            }
            $enrolled = DangKyLop::where('id_lop_hoc', $session->id_lop_hoc)->where('id_hoc_vien', $studentId)
                ->whereIn('trang_thai', ['da_xac_nhan', 'da_thanh_toan'])->exists();
            $attendance = DB::table('chi_tiet_phong_hops')->where('id_buoi_hoc', $session->id)->where('id_nguoi_dung', $studentId)
                ->where('loai_nguoi_dung', 'hoc_vien');
            if ($session->lopHoc->hinh_thuc === 'offline') {
                $attendance->where('attendance_source', 'teacher_attested');
            } else {
                $attendance->where('xac_thuc_khuon_mat', 1)->where(function ($query) {
                    $query->where('attendance_source', 'face_verified')->orWhereNull('attendance_source');
                });
            }
            $attended = $attendance->exists();
            if (! $enrolled || ! $attended) {
                $this->reject('Bạn chưa tham gia buổi học này.', 403);
            }
            if (DB::table('lesson_reviews')->where('id_buoi_hoc', $session->id)->where('id_hoc_vien', $studentId)->exists()) {
                $this->reject('Buổi học đã được đánh giá.', 409);
            }
            $id = DB::table('lesson_reviews')->insertGetId(['id_buoi_hoc' => $session->id, 'id_hoc_vien' => $studentId,
                'id_giao_vien' => $session->lopHoc->id_giao_vien, 'rating' => $data['rating'], 'comment' => $data['comment'] ?? null,
                'created_at' => now(), 'updated_at' => now()]);

            return $this->query()->where('r.id', $id)->first();
        });

        return $this->ok($review, 'Đã gửi đánh giá.', 201);
    }

    private function query()
    {
        return DB::table('lesson_reviews as r')->join('hoc_viens as h', 'h.id', '=', 'r.id_hoc_vien')
            ->join('giao_viens as g', 'g.id', '=', 'r.id_giao_vien')->join('buoi_hocs as b', 'b.id', '=', 'r.id_buoi_hoc')
            ->join('lop_hocs as l', 'l.id', '=', 'b.id_lop_hoc')->join('mon_hocs as m', 'm.id', '=', 'l.id_mon_hoc')
            ->select('r.*', 'h.ho_ten as ten_hoc_vien', 'g.ho_ten as ten_giao_vien', 'm.ten_mon_hoc', 'b.thoi_gian_bat_dau');
    }
}
