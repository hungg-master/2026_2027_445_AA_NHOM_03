<?php

namespace App\Http\Controllers;

use App\Models\BuoiHoc;
use App\Models\ChiTietPhongHop;
use App\Models\GiaoVien;
use App\Models\PhongHop;
use App\Services\FaceVerificationService;
use App\Services\SessionAccessService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PhongHopController extends Controller
{
    private function session(Request $request): BuoiHoc
    {
        $request->validate(['id_buoi_hoc' => 'required|integer|exists:buoi_hocs,id']);
        $session = BuoiHoc::with(['lopHoc.giaoVien', 'lopHoc.phongHop'])->findOrFail($request->integer('id_buoi_hoc'));
        app(SessionAccessService::class)->authorize(Auth::guard('sanctum')->user(), $session);
        abort_unless($session->lopHoc->hinh_thuc === 'online' && $session->lopHoc->phongHop?->trang_thai == 1, 409, 'Buổi học không có phòng trực tuyến mở.');

        return $session;
    }

    private function timeWindow(BuoiHoc $session): void
    {
        abort_if(now()->lt($session->thoi_gian_bat_dau->copy()->subMinutes(config('smarttrial.room_early_minutes')))
            || now()->gte($session->thoi_gian_ket_thuc), 409, 'Chưa đến giờ vào phòng hoặc buổi học đã kết thúc.');
    }

    public function taoToken(Request $request, FaceVerificationService $proofs)
    {
        $request->validate(['verification_id' => 'required|string|size:64']);
        $actor = Auth::guard('sanctum')->user();
        $data = DB::transaction(function () use ($request, $proofs, $actor) {
            $session = $this->session($request);
            $this->timeWindow($session);
            $url = config('services.livekit.url');
            $key = config('services.livekit.api_key');
            $secret = config('services.livekit.api_secret');
            if (! $url || ! $key || ! $secret || ! preg_match('#^wss?://#', $url)) {
                abort(503, 'Dịch vụ phòng học chưa được cấu hình.');
            }
            $proofs->consume($actor, $request->input('verification_id'), 'room', $session->id);
            $room = $session->lopHoc->phongHop;
            $claims = [
                'iss' => $key, 'sub' => $proofs::role($actor).':'.$actor->id, 'name' => $actor->ho_ten,
                'nbf' => now()->timestamp - 5, 'iat' => now()->timestamp,
                'exp' => min(now()->timestamp + 300, $session->thoi_gian_ket_thuc->timestamp),
                'video' => ['roomJoin' => true, 'room' => $room->ma_phong, 'canPublish' => true, 'canSubscribe' => true, 'canPublishData' => true],
            ];
            $encode = fn ($value) => rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
            $body = $encode(json_encode(['alg' => 'HS256', 'typ' => 'JWT'])).'.'.$encode(json_encode($claims, JSON_THROW_ON_ERROR));
            $token = $body.'.'.$encode(hash_hmac('sha256', $body, $secret, true));
            $grant = $proofs->issue($actor, 'attendance', $session->id);

            return ['token' => $token, 'server_url' => $url, 'id_phong_hop' => $room->id, 'ma_phong' => $room->ma_phong,
                'attendance_token' => $grant['verification_id']];
        }, 5);

        return response()->json(['status' => true, 'data' => $data]);
    }

    public function chiTietPhongHopCreate(Request $request, FaceVerificationService $proofs)
    {
        $request->validate(['attendance_token' => 'required|string|size:64']);
        $actor = Auth::guard('sanctum')->user();
        $row = DB::transaction(function () use ($request, $proofs, $actor) {
            $session = $this->session($request);
            $this->timeWindow($session);
            $proofs->consume($actor, $request->input('attendance_token'), 'attendance', $session->id);

            return ChiTietPhongHop::updateOrCreate([
                'id_buoi_hoc' => $session->id, 'loai_nguoi_dung' => $proofs::role($actor), 'id_nguoi_dung' => $actor->id,
            ], [
                'id_phong_hop' => $session->lopHoc->phongHop->id, 'xac_thuc_khuon_mat' => 1,
                'attendance_source' => 'face_verified',
                'is_active' => 1, 'trang_thai' => 1, 'thoi_gian_tham_gia' => now(), 'thoi_gian_roi' => null,
            ]);
        }, 5);

        return response()->json(['status' => true, 'data' => $row]);
    }

    public function roiPhong(Request $request)
    {
        $request->validate(['id_buoi_hoc' => 'required|integer']);
        $actor = Auth::guard('sanctum')->user();
        ChiTietPhongHop::where('id_buoi_hoc', $request->integer('id_buoi_hoc'))
            ->where('loai_nguoi_dung', FaceVerificationService::role($actor))->where('id_nguoi_dung', $actor->id)
            ->where('is_active', 1)->update(['is_active' => 0, 'thoi_gian_roi' => now()]);

        return response()->json(['status' => true]);
    }

    public function lichSuThamGia()
    {
        $actor = Auth::guard('sanctum')->user();

        return response()->json(['status' => true, 'data' => ChiTietPhongHop::with('phongHop')
            ->where('loai_nguoi_dung', FaceVerificationService::role($actor))->where('id_nguoi_dung', $actor->id)->orderByDesc('id')->get()]);
    }

    public function chiTietPhongHopData()
    {
        return $this->lichSuThamGia();
    }

    public function phongHopLienQuan()
    {
        $actor = Auth::guard('sanctum')->user();
        $rooms = PhongHop::whereHas('lopHoc', function ($q) use ($actor) {
            if ($actor instanceof GiaoVien) {
                $q->where('id_giao_vien', $actor->id);
            } else {
                $q->whereHas('dangKyLops', fn ($q) => $q->where('id_hoc_vien', $actor->id)->whereIn('trang_thai', ['da_xac_nhan', 'da_thanh_toan']));
            }
        })->get();

        return response()->json(['status' => true, 'data' => $rooms]);
    }

    public function maPhong()
    {
        return $this->phongHopLienQuan();
    }

    public function kiemTraPhongHop(Request $request)
    {
        $request->validate(['ma_phong' => 'required|string|max:50']);
        $room = PhongHop::where('ma_phong', $request->input('ma_phong'))->firstOrFail();
        $request->validate(['id_buoi_hoc' => 'required|integer']);
        $session = $this->session($request);
        abort_unless($session->id_lop_hoc === $room->id_lop_hoc, 403);

        return response()->json(['status' => true, 'data' => $room]);
    }

    public function create(Request $request)
    {
        // Rooms are created atomically with an owned class, never from client room/owner IDs.
        $session = $this->session($request);
        abort_unless(Auth::guard('sanctum')->user() instanceof GiaoVien, 403);

        return response()->json(['status' => true, 'data' => $session->lopHoc->phongHop]);
    }
}
