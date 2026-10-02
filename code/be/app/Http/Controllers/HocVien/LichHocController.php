<?php

namespace App\Http\Controllers\HocVien;

use App\Http\Controllers\Controller;
use App\Http\Requests\DangKyLop\DangKyLopRequest;
use App\Models\DangKyLop;
use App\Models\LopHoc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Controller: Lịch học + Lớp học của tôi (phía Học viên)
 */
class LichHocController extends Controller
{
    /**
     * GET /api/hoc-vien/lich-hoc
     * Query: from_date, to_date, id_mon_hoc
     */
    public function lichHoc(Request $request)
    {
        try {
            $hv = Auth::guard('sanctum')->user();

            $query = DangKyLop::with([
                'lopHoc' => function ($q) {
                    $q->with(['monHoc', 'phongHoc', 'giaoVien', 'phongHop']);
                }
            ])
                ->where('id_hoc_vien', $hv->id)
                ->where('trang_thai', '!=', 'da_huy');

            $allDks = $query->orderBy('ngay_dang_ky', 'desc')->get();

            $fromCarbon = $request->filled('from_date')
                ? \Carbon\Carbon::parse($request->from_date)->startOfDay()
                : null;
            $toCarbon = $request->filled('to_date')
                ? \Carbon\Carbon::parse($request->to_date)->endOfDay()
                : null;

            $now = now();
            $sessions = collect();

            foreach ($allDks as $dk) {
                if (!$dk->lopHoc) continue;
                $lop = $dk->lopHoc;

                if ($request->filled('id_mon_hoc') && $lop->id_mon_hoc != $request->id_mon_hoc) {
                    continue;
                }

                if (!$lop->thoi_gian_bat_dau || !$lop->thoi_gian_ket_thuc) {
                    continue;
                }

                $origStart = \Carbon\Carbon::parse($lop->thoi_gian_bat_dau);
                $origEnd = \Carbon\Carbon::parse($lop->thoi_gian_ket_thuc);

                if ($fromCarbon && $toCarbon) {
                    // Chiếu lịch học vào tuần được yêu cầu (Weekly recurrence projection)
                    $dayOfWeekOrig = $origStart->dayOfWeek; // 0 = CN, 1 = T2...
                    $diffDays = ($dayOfWeekOrig - $fromCarbon->dayOfWeek + 7) % 7;
                    $targetDate = $fromCarbon->copy()->addDays($diffDays);

                    if ($targetDate->between($fromCarbon, $toCarbon)) {
                        $sessionStart = $targetDate->copy()->setTime($origStart->hour, $origStart->minute, $origStart->second);
                        $sessionEnd = $targetDate->copy()->setTime($origEnd->hour, $origEnd->minute, $origEnd->second);

                        $clonedDk = clone $dk;
                        $clonedLop = clone $lop;
                        $clonedLop->thoi_gian_bat_dau = $sessionStart;
                        $clonedLop->thoi_gian_ket_thuc = $sessionEnd;
                        $clonedDk->setRelation('lopHoc', $clonedLop);

                        // Trạng thái buổi học
                        if ($sessionEnd->lt($now)) {
                            $clonedDk->trang_thai_buoi = 'da_hoc';
                        } elseif ($sessionStart->lte($now) && $sessionEnd->gte($now)) {
                            $clonedDk->trang_thai_buoi = 'dang_dien_ra';
                        } else {
                            $clonedDk->trang_thai_buoi = 'sap_toi';
                        }

                        $sessions->push($clonedDk);
                    }
                } else {
                    $clonedDk = clone $dk;
                    if ($origEnd->lt($now)) {
                        $clonedDk->trang_thai_buoi = 'da_hoc';
                    } elseif ($origStart->lte($now) && $origEnd->gte($now)) {
                        $clonedDk->trang_thai_buoi = 'dang_dien_ra';
                    } else {
                        $clonedDk->trang_thai_buoi = 'sap_toi';
                    }
                    $sessions->push($clonedDk);
                }
            }

            return response()->json([
                'status' => true,
                'message' => 'Lấy lịch học thành công.',
                'data' => $sessions->values(),
            ]);
        } catch (\Exception $e) {
            Log::error('LichHoc HV error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Đã có lỗi xảy ra: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET /api/hoc-vien/lop-hoc-cua-toi
     * Danh sách các lớp HV đã đăng ký
     */
    public function lopHocCuaToi()
    {
        try {
            $hv = Auth::guard('sanctum')->user();

            $data = DangKyLop::with([
                'lopHoc.monHoc',
                'lopHoc.phongHoc',
                'lopHoc.giaoVien',
            ])
                ->where('id_hoc_vien', $hv->id)
                ->orderBy('ngay_dang_ky', 'desc')
                ->get();

            return response()->json([
                'status' => true,
                'message' => 'Lấy danh sách lớp đã đăng ký thành công.',
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            Log::error('LopHocCuaToi HV error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Đã có lỗi xảy ra: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * POST /api/hoc-vien/dang-ky-lop-hoc
     * Body: id_lop_hoc
     */
    public function dangKy(DangKyLopRequest $request)
    {
        try {
            $hv = Auth::guard('sanctum')->user();
            $lopHoc = LopHoc::with(['monHoc', 'giaoVien', 'phongHoc'])->find($request->id_lop_hoc);

            if (!$lopHoc) {
                return response()->json([
                    'status' => false,
                    'message' => 'Lớp học không tồn tại.',
                ], 404);
            }

            if (in_array($lopHoc->tinh_trang, ['da_huy', 'da_ket_thuc'])) {
                return response()->json([
                    'status' => false,
                    'message' => 'Lớp học này đã hủy hoặc kết thúc, không thể đăng ký!',
                ], 400);
            }

            // Kiểm tra sĩ số tối đa
            if ($lopHoc->si_so_toi_da && $lopHoc->so_hoc_vien_hien_tai >= $lopHoc->si_so_toi_da) {
                return response()->json([
                    'status' => false,
                    'message' => "Lớp học đã đủ sĩ số tối đa ({$lopHoc->si_so_toi_da} học viên), không thể nhận thêm đăng ký!",
                ], 400);
            }

            // Kiểm tra đã đăng ký chính lớp này chưa
            $existing = DangKyLop::where('id_hoc_vien', $hv->id)
                ->where('id_lop_hoc', $request->id_lop_hoc)
                ->where('trang_thai', '!=', 'da_huy')
                ->first();
            if ($existing) {
                return response()->json([
                    'status' => false,
                    'message' => 'Bạn đã đăng ký lớp học này rồi!',
                ], 400);
            }

            // 1 MÔN CHỈ ĐĂNG KÝ ĐƯỢC 1 LỚP DUY NHẤT
            $existingSameSubject = DangKyLop::where('id_hoc_vien', $hv->id)
                ->where('trang_thai', '!=', 'da_huy')
                ->whereHas('lopHoc', function ($q) use ($lopHoc) {
                    $q->where('id_mon_hoc', $lopHoc->id_mon_hoc);
                })
                ->with(['lopHoc.giaoVien', 'lopHoc.monHoc'])
                ->first();

            if ($existingSameSubject) {
                $lopCu = $existingSameSubject->lopHoc;
                $tenMon = $lopHoc->monHoc?->ten_mon_hoc ?? 'môn học này';
                $tenGv = $lopCu?->giaoVien?->ho_ten ?? 'Giáo viên';

                return response()->json([
                    'status' => false,
                    'da_dang_ky_mon' => true,
                    'message' => "Không thể đăng ký! Mỗi môn học bạn chỉ được đăng ký 1 lớp duy nhất. Bạn đã đăng ký lớp môn '{$tenMon}' do {$tenGv} phụ trách rồi. Vui lòng hủy lớp đã đăng ký nếu muốn đổi sang lớp này.",
                    'lop_da_dang_ky' => [
                        'id' => $lopCu?->id,
                        'ten_mon' => $tenMon,
                        'giao_vien' => $tenGv,
                    ]
                ], 400);
            }

            // =========================================================
            // KIỂM TRA TRÙNG GIỜ HỌC VỚI CÁC LỚP SINH VIÊN ĐÃ ĐĂNG KÝ
            // =========================================================
            $activeDks = DangKyLop::with(['lopHoc.monHoc', 'lopHoc.giaoVien', 'lopHoc.phongHoc'])
                ->where('id_hoc_vien', $hv->id)
                ->where('trang_thai', '!=', 'da_huy')
                ->get();

            if ($lopHoc->thoi_gian_bat_dau && $lopHoc->thoi_gian_ket_thuc) {
                $startA = \Carbon\Carbon::parse($lopHoc->thoi_gian_bat_dau);
                $endA = \Carbon\Carbon::parse($lopHoc->thoi_gian_ket_thuc);
                $timeA_start = $startA->format('H:i:s');
                $timeA_end = $endA->format('H:i:s');

                $thuMap = [
                    0 => 'Chủ nhật',
                    1 => 'Thứ 2',
                    2 => 'Thứ 3',
                    3 => 'Thứ 4',
                    4 => 'Thứ 5',
                    5 => 'Thứ 6',
                    6 => 'Thứ 7'
                ];

                foreach ($activeDks as $dk) {
                    $existingLop = $dk->lopHoc;
                    if (!$existingLop || !$existingLop->thoi_gian_bat_dau || !$existingLop->thoi_gian_ket_thuc) {
                        continue;
                    }

                    $startB = \Carbon\Carbon::parse($existingLop->thoi_gian_bat_dau);
                    $endB = \Carbon\Carbon::parse($existingLop->thoi_gian_ket_thuc);
                    $timeB_start = $startB->format('H:i:s');
                    $timeB_end = $endB->format('H:i:s');

                    $isConflict = false;
                    $conflictReason = '';

                    // 1. Trùng chính xác cùng ngày và thời gian giao nhau
                    if ($startA->format('Y-m-d') === $startB->format('Y-m-d')) {
                        if ($startA < $endB && $endA > $startB) {
                            $isConflict = true;
                            $conflictReason = 'cùng ngày ' . $startB->format('d/m/Y');
                        }
                    }

                    // 2. Trùng theo lịch tuần: cùng Thứ và khung giờ trong ngày giao nhau
                    if ($startA->dayOfWeek === $startB->dayOfWeek) {
                        if ($timeA_start < $timeB_end && $timeA_end > $timeB_start) {
                            $isConflict = true;
                            $conflictReason = 'vào ' . ($thuMap[$startB->dayOfWeek] ?? 'Thứ') . ' hàng tuần';
                        }
                    }

                    if ($isConflict) {
                        $tenThu = $thuMap[$startB->dayOfWeek] ?? 'Thứ ' . $startB->dayOfWeek;
                        $khungGio = $startB->format('H:i') . ' - ' . $endB->format('H:i');
                        $tenMon = $existingLop->monHoc?->ten_mon_hoc ?? ('Lớp #' . $existingLop->id);

                        return response()->json([
                            'status' => false,
                            'trung_gio' => true,
                            'message' => "Không thể đăng ký! Lớp học này bị TRÙNG GIỜ với lớp '{$tenMon}' ({$tenThu}, {$khungGio}) mà bạn đã đăng ký.",
                            'lop_trung' => [
                                'id' => $existingLop->id,
                                'ten_mon' => $tenMon,
                                'thu' => $tenThu,
                                'khung_gio' => $khungGio,
                                'reason' => $conflictReason,
                            ],
                        ], 400);
                    }
                }
            }

            DB::beginTransaction();
            $dangKy = DangKyLop::create([
                'id_hoc_vien' => $hv->id,
                'id_lop_hoc' => $request->id_lop_hoc,
                'ngay_dang_ky' => now(),
                'trang_thai' => 'da_xac_nhan',
            ]);
            $dangKy->load(['lopHoc.monHoc', 'lopHoc.giaoVien', 'lopHoc.phongHoc']);
            DB::commit();

            return response()->json([
                'status' => true,
                'message' => "Đăng ký thành công lớp '" . ($lopHoc->monHoc?->ten_mon_hoc ?? 'Lớp học') . "'!",
                'data' => $dangKy,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Dang ky HV error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Đăng ký thất bại: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * DELETE /api/hoc-vien/huy-dang-ky/{id}
     */
    public function huyDangKy($id)
    {
        try {
            $hv = Auth::guard('sanctum')->user();
            $dangKy = DangKyLop::where('id', $id)
                ->where('id_hoc_vien', $hv->id)
                ->first();

            if (!$dangKy) {
                return response()->json([
                    'status' => false,
                    'message' => 'Không tìm thấy đăng ký.',
                ], 404);
            }

            if ($dangKy->trang_thai === 'da_huy') {
                return response()->json([
                    'status' => false,
                    'message' => 'Đăng ký này đã được hủy trước đó.',
                ], 400);
            }

            $dangKy->trang_thai = 'da_huy';
            $dangKy->save();

            return response()->json([
                'status' => true,
                'message' => 'Đã hủy đăng ký lớp học thành công!',
            ]);
        } catch (\Exception $e) {
            Log::error('Huy dang ky error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Hủy đăng ký thất bại: ' . $e->getMessage(),
            ], 500);
        }
    }
}
