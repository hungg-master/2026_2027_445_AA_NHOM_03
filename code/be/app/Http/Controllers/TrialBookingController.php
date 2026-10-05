<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\DangKyLop;
use App\Models\GiaoVien;
use App\Models\HocVien;
use App\Models\LopHoc;
use App\Models\PhongHop;
use App\Services\LessonScheduleService;
use App\Services\SupportEvents;
use App\Services\TeacherMatchingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TrialBookingController extends Controller
{
    public function store(Request $request, TeacherMatchingService $matching)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100', 'phone' => 'required|digits:10', 'subject' => 'required|string|max:150',
            'schedules' => $request->filled('id_giao_vien') ? 'present|array|max:98' : 'required|array|min:1|max:98',
            'schedules.*' => ['required', 'string', 'distinct', 'regex:/^(Mon|Tue|Wed|Thu|Fri|Sat|Sun)_([1-9]|1[0-2]):00 (AM|PM)$/'],
            'id_giao_vien' => 'nullable|required_with:id_mon_hoc|integer|exists:giao_viens,id',
            'id_mon_hoc' => 'nullable|required_with:id_giao_vien|integer|exists:mon_hocs,id',
            'thoi_gian_bat_dau' => 'nullable|required_with:id_giao_vien|date|after:now',
            'thoi_gian_ket_thuc' => 'nullable|required_with:id_giao_vien|date|after:thoi_gian_bat_dau',
        ]);
        $actor = Auth::guard('sanctum')->user();
        if ($request->bearerToken()) {
            abort_unless($actor instanceof HocVien && ! $actor->is_block && $actor->tinh_trang == 1 && $actor->is_active, 403);
        }
        if (isset($data['id_giao_vien'])) {
            abort_unless($actor instanceof HocVien, 401, 'Đăng nhập để đặt lịch cụ thể.');
            abort_unless($matching->eligible((int) $data['id_giao_vien'], (int) $data['id_mon_hoc']), 422, 'Giáo viên không phù hợp môn học.');
            $start = Carbon::parse($data['thoi_gian_bat_dau'])->setTimezone(config('app.timezone'));
            $end = Carbon::parse($data['thoi_gian_ket_thuc'])->setTimezone(config('app.timezone'));
            abort_unless($start->isSameDay($end) && $end->timestamp - $start->timestamp >= 900 && $end->timestamp - $start->timestamp <= 10800, 422, 'Buổi thử phải từ 15 đến 180 phút trong cùng ngày.');
            $data['thoi_gian_bat_dau'] = $start;
            $data['thoi_gian_ket_thuc'] = $end;
        }
        $id = DB::transaction(fn () => DB::table('trial_bookings')->insertGetId([
            ...$data, 'id_hoc_vien' => $actor instanceof HocVien ? $actor->id : null,
            'schedules' => json_encode($data['schedules']), 'created_at' => now(), 'updated_at' => now(),
        ]), 5);
        app(SupportEvents::class)->trialChanged($id);

        return response()->json(['status' => true, 'message' => 'Yêu cầu học thử đang chờ xác nhận.', 'data' => ['id' => $id]], 201);
    }

    public function index()
    {
        $actor = Auth::guard('sanctum')->user();
        $query = DB::table('trial_bookings');
        if (! $actor instanceof Admin) {
            $query->where($actor instanceof GiaoVien ? 'id_giao_vien' : 'id_hoc_vien', $actor->id);
        }
        $rows = $query->orderByDesc('id')->get()->map(function ($row) {
            $row->schedules = json_decode($row->schedules, true);

            return $row;
        });

        return response()->json(['status' => true, 'data' => $rows]);
    }

    private function owned(int $id, bool $confirm = false)
    {
        $actor = Auth::guard('sanctum')->user();
        $booking = DB::table('trial_bookings')->where('id', $id)->lockForUpdate()->first();
        abort_if(! $booking, 404);
        if ($actor instanceof Admin) {
            return $booking;
        }
        if ($actor instanceof GiaoVien) {
            abort_unless((int) $booking->id_giao_vien === $actor->id, 403);
        } else {
            abort_unless(! $confirm && (int) $booking->id_hoc_vien === $actor->id, 403);
        }

        return $booking;
    }

    public function confirm(int $id, TeacherMatchingService $matching, LessonScheduleService $schedule)
    {
        DB::transaction(function () use ($id, $matching, $schedule) {
            $preview = DB::table('trial_bookings')->find($id);
            abort_if(! $preview || ! $preview->id_hoc_vien || ! $preview->id_giao_vien || ! $preview->id_mon_hoc, 422, 'Cần chọn tài khoản, môn và lịch cụ thể.');
            $student = HocVien::whereKey($preview->id_hoc_vien)->lockForUpdate()->firstOrFail();
            $teacher = GiaoVien::whereKey($preview->id_giao_vien)->lockForUpdate()->firstOrFail();
            $booking = $this->owned($id, true);
            abort_unless(! $student->is_block && $student->is_active && $student->tinh_trang == 1 && $matching->eligible($teacher->id, (int) $booking->id_mon_hoc), 403);
            if ($booking->status === 'confirmed') {
                return;
            }
            abort_unless($booking->status === 'pending', 409, 'Yêu cầu đã hủy.');
            $start = Carbon::parse($booking->thoi_gian_bat_dau);
            $end = Carbon::parse($booking->thoi_gian_ket_thuc);
            abort_unless($matching->available($teacher, $student, $start, $end, false), 409, 'Khung giờ không còn phù hợp hoặc đã có lịch.');
            $schedule->checkResources($teacher->id, null, [['thoi_gian_bat_dau' => $start, 'thoi_gian_ket_thuc' => $end]]);
            $class = LopHoc::create([
                'id_giao_vien' => $teacher->id, 'id_mon_hoc' => $booking->id_mon_hoc, 'loai_lop' => 'kem',
                'hinh_thuc' => 'online', 'hoc_phi' => 0, 'si_so_toi_da' => 1, 'tinh_trang' => 'dang_mo',
                'thoi_gian_bat_dau' => $start, 'thoi_gian_ket_thuc' => $end,
            ]);
            $session = $class->buoiHocs()->create(['thoi_gian_bat_dau' => $start, 'thoi_gian_ket_thuc' => $end]);
            $room = PhongHop::create([
                'id_lop_hoc' => $class->id, 'id_chu_phong' => $teacher->id, 'ma_phong' => 'ST-'.Str::uuid(),
                'ten_phong' => 'Học thử '.$class->monHoc->ten_mon_hoc, 'so_nguoi_toi_da' => 2,
                'thoi_gian_bat_dau' => $start, 'thoi_gian_ket_thuc' => $end, 'trang_thai' => 1,
            ]);
            $class->update(['link_online' => '/phong-hoc/'.$room->ma_phong]);
            DangKyLop::create(['id_lop_hoc' => $class->id, 'id_hoc_vien' => $student->id, 'trang_thai' => 'da_xac_nhan', 'ngay_dang_ky' => now()]);
            DB::table('trial_bookings')->where('id', $id)->update(['status' => 'confirmed', 'id_lop_hoc' => $class->id, 'id_buoi_hoc' => $session->id, 'updated_at' => now()]);
        }, 5);
        app(SupportEvents::class)->trialChanged($id);

        return response()->json(['status' => true, 'data' => DB::table('trial_bookings')->find($id)]);
    }

    public function cancel(int $id)
    {
        DB::transaction(function () use ($id) {
            $preview = DB::table('trial_bookings')->find($id);
            abort_if(! $preview, 404);
            if ($preview->id_hoc_vien) {
                HocVien::whereKey($preview->id_hoc_vien)->lockForUpdate()->firstOrFail();
            }
            if ($preview->id_giao_vien) {
                GiaoVien::whereKey($preview->id_giao_vien)->lockForUpdate()->firstOrFail();
            }
            $booking = $this->owned($id);
            if ($booking->status === 'cancelled') {
                return;
            }
            if ($booking->id_lop_hoc) {
                $class = LopHoc::whereKey($booking->id_lop_hoc)->lockForUpdate()->firstOrFail();
                abort_if($class->buoiHocs()->where('thoi_gian_bat_dau', '<=', now())->exists(), 409, 'Buổi thử đã bắt đầu.');
                $class->update(['tinh_trang' => 'da_huy']);
                foreach ($class->buoiHocs as $session) {
                    $session->update(['trang_thai' => 'cancelled']);
                }
                foreach ($class->dangKyLops as $row) {
                    $row->update(['trang_thai' => 'da_huy']);
                }
                $class->phongHop?->update(['trang_thai' => 0]);
            }
            DB::table('trial_bookings')->where('id', $id)->update(['status' => 'cancelled', 'updated_at' => now()]);
        }, 5);
        app(SupportEvents::class)->trialChanged($id);

        return response()->json(['status' => true]);
    }
}
