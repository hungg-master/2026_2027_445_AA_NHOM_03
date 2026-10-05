<?php

namespace App\Http\Controllers\GiaoVien;

use App\Http\Controllers\Controller;
use App\Http\Requests\LopHoc\TaoLopHocRequest;
use App\Models\GiaoVien;
use App\Models\LopHoc;
use App\Services\ClassLifecycleService;
use App\Services\ClassRoomService;
use App\Services\LessonScheduleService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LopHocController extends Controller
{
    public function lichDay(Request $request, LessonScheduleService $schedule)
    {
        return response()->json(['status' => true, 'data' => $schedule->calendar($request, Auth::guard('sanctum')->user())]);
    }

    public function index()
    {
        $rows = LopHoc::with(['monHoc', 'phongHoc', 'phongHop', 'buoiHocs'])
            ->where('id_giao_vien', Auth::guard('sanctum')->id())->orderByDesc('thoi_gian_bat_dau')->get();
        $rows->each(fn ($row) => $row->so_hoc_vien_hien_tai = $row->so_hoc_vien_hien_tai);

        return response()->json(['status' => true, 'data' => $rows]);
    }

    public function show(int $id)
    {
        $class = LopHoc::with(['monHoc', 'phongHoc', 'phongHop', 'buoiHocs', 'dangKyLops.hocVien'])
            ->where('id_giao_vien', Auth::guard('sanctum')->id())->findOrFail($id);
        $class->so_hoc_vien_hien_tai = $class->so_hoc_vien_hien_tai;

        return response()->json(['status' => true, 'data' => $class]);
    }

    public function store(TaoLopHocRequest $request, LessonScheduleService $schedule)
    {
        $class = DB::transaction(function () use ($request, $schedule) {
            $data = $this->classData($request);
            $intervals = $schedule->intervals($data);
            $schedule->checkResources(Auth::guard('sanctum')->id(), $data['id_phong_hoc'], $intervals);
            $class = LopHoc::create([...$data, 'id_giao_vien' => Auth::guard('sanctum')->id()]);
            foreach ($intervals as $interval) {
                $class->buoiHocs()->create($interval);
            }
            $this->syncRoom($class);

            return $class->load(['monHoc', 'phongHoc', 'phongHop', 'buoiHocs']);
        }, 5);

        return response()->json(['status' => true, 'message' => 'Tạo lớp học thành công.', 'data' => $class], 201);
    }

    public function update(TaoLopHocRequest $request, int $id, LessonScheduleService $schedule)
    {
        $class = DB::transaction(function () use ($request, $id, $schedule) {
            $actor = Auth::guard('sanctum')->user();
            GiaoVien::whereKey($actor->id)->lockForUpdate()->firstOrFail();
            $class = LopHoc::where('id_giao_vien', $actor->id)->lockForUpdate()->findOrFail($id);
            $data = $this->classData($request);
            abort_if(in_array($class->tinh_trang, ['da_huy', 'da_ket_thuc'])
                && $data['tinh_trang'] !== $class->tinh_trang, 409, 'Không thể mở lại lớp đã hủy hoặc kết thúc.');
            abort_if($data['tinh_trang'] === 'da_ket_thuc' && $class->tinh_trang !== 'da_ket_thuc',
                409, 'Hãy hoàn tất từng buổi học sau giờ kết thúc để kết thúc lớp.');
            $fields = ['id_mon_hoc', 'hinh_thuc', 'id_phong_hoc', 'thoi_gian_bat_dau', 'thoi_gian_ket_thuc', 'recurrence', 'recurrence_until'];
            $changed = false;
            foreach ($fields as $field) {
                $old = $class->$field;
                if ($old instanceof \DateTimeInterface) {
                    $old = $old->format($field === 'recurrence_until' ? 'Y-m-d' : 'Y-m-d H:i:s');
                }
                $new = $data[$field] ?? null;
                if (in_array($field, ['thoi_gian_bat_dau', 'thoi_gian_ket_thuc'])) {
                    $new = Carbon::parse($new)->setTimezone(config('app.timezone'))->format('Y-m-d H:i:s');
                }
                if ((string) $old !== (string) $new) {
                    $changed = true;
                }
            }
            // A published enrolled timetable cannot be silently replaced or lose attendance/reviews.
            if ($changed && ($class->dangKyLops()->where('trang_thai', '!=', 'da_huy')->exists()
                || $class->buoiHocs()->where('thoi_gian_bat_dau', '<=', now())->exists())) {
                throw ValidationException::withMessages(['thoi_gian_bat_dau' => 'Lớp đã có học viên hoặc đã diễn ra; hãy tạo lớp mới để đổi lịch.']);
            }
            abort_if($data['si_so_toi_da'] < $class->so_hoc_vien_hien_tai, 409, 'Sĩ số mới thấp hơn số học viên.');
            if ($changed) {
                $intervals = $schedule->intervals($data);
                $schedule->checkResources($actor->id, $data['id_phong_hoc'], $intervals, $class->id);
                $class->buoiHocs()->delete();
                foreach ($intervals as $interval) {
                    $class->buoiHocs()->create($interval);
                }
            }
            $class->fill($data)->save();
            $this->syncRoom($class);
            if ($class->tinh_trang === 'da_huy') {
                $this->cancel($class);
            }

            return $class->load(['monHoc', 'phongHoc', 'phongHop', 'buoiHocs']);
        }, 5);

        return response()->json(['status' => true, 'data' => $class]);
    }

    public function destroy(int $id)
    {
        DB::transaction(function () use ($id) {
            $class = LopHoc::where('id_giao_vien', Auth::guard('sanctum')->id())->lockForUpdate()->findOrFail($id);
            $this->cancel($class);
        }, 5);

        return response()->json(['status' => true, 'message' => 'Đã hủy lớp học.']);
    }

    private function cancel(LopHoc $class): void
    {
        app(ClassLifecycleService::class)->cancel($class);
    }

    private function classData(TaoLopHocRequest $request): array
    {
        $data = $request->validated();
        $data['recurrence'] = $data['recurrence'] ?? 'once';
        $data['recurrence_until'] = $data['recurrence'] === 'weekly' ? $data['recurrence_until'] : null;
        $data['id_phong_hoc'] = $data['hinh_thuc'] === 'offline' ? (int) $data['id_phong_hoc'] : null;
        $data['link_online'] = $data['hinh_thuc'] === 'online' ? ($data['link_online'] ?? null) : null;
        $data['tinh_trang'] = $data['tinh_trang'] ?? 'sap_mo';
        foreach (['thoi_gian_bat_dau', 'thoi_gian_ket_thuc'] as $field) {
            $data[$field] = Carbon::parse($data[$field])->setTimezone(config('app.timezone'));
        }

        return $data;
    }

    private function syncRoom(LopHoc $class): void
    {
        app(ClassRoomService::class)->sync($class);
    }
}
