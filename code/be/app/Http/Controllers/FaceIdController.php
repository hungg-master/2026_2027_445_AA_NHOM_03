<?php

namespace App\Http\Controllers;

use App\Models\BuoiHoc;
use App\Models\HocVien;
use App\Models\LopHoc;
use App\Services\FaceVerificationService;
use App\Services\SessionAccessService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class FaceIdController extends Controller
{
    public function sample(Request $request, FaceVerificationService $faces)
    {
        $actor = Auth::guard('sanctum')->user();
        $descriptor = $faces->descriptor($request->input('descriptor', $request->input('du_lieu_khuon_mat')));
        DB::transaction(function () use ($actor, $descriptor, $faces, $request) {
            $actor = $actor->newQuery()->whereKey($actor->id)->lockForUpdate()->firstOrFail();
            $old = $faces->readSample($actor);
            if ($old && $faces->distance($old, $descriptor) >= config('smarttrial.face_distance_threshold')
                && ! Hash::check((string) $request->input('current_password'), $actor->password)) {
                throw ValidationException::withMessages(['current_password' => 'Nhập mật khẩu hiện tại để thay mẫu khuôn mặt.']);
            }
            $actor->du_lieu_khuon_mat = Crypt::encryptString(json_encode($descriptor, JSON_THROW_ON_ERROR));
            $actor->save();
            DB::table('face_verifications')->where('actor_type', $faces::role($actor))->where('actor_id', $actor->id)->whereNull('consumed_at')->delete();
        }, 5);

        return response()->json(['status' => true, 'data' => ['has_face_id' => true]]);
    }

    public function verify(Request $request, FaceVerificationService $faces, SessionAccessService $access)
    {
        $data = $request->validate([
            'purpose' => 'required|in:room,enrollment',
            'id_lop_hoc' => 'required_if:purpose,enrollment|nullable|integer|exists:lop_hocs,id',
            'id_buoi_hoc' => 'required_if:purpose,room|nullable|integer|exists:buoi_hocs,id',
        ]);
        $descriptor = $faces->descriptor($request->input('descriptor'));
        $actor = Auth::guard('sanctum')->user();
        if ($data['purpose'] === 'room') {
            $target = (int) $data['id_buoi_hoc'];
            $access->authorize($actor, BuoiHoc::findOrFail($target));
        } else {
            abort_unless($actor instanceof HocVien, 403);
            $target = (int) $data['id_lop_hoc'];
            $class = LopHoc::findOrFail($target);
            abort_unless(in_array($class->tinh_trang, ['sap_mo', 'dang_mo']), 403, 'Lớp không nhận đăng ký.');
        }
        $proof = DB::transaction(function () use ($actor, $faces, $descriptor, $data, $target) {
            $actor = $actor->newQuery()->whereKey($actor->id)->lockForUpdate()->firstOrFail();
            $old = $faces->readSample($actor);
            if (! $old || $faces->distance($old, $descriptor) >= config('smarttrial.face_distance_threshold')) {
                throw ValidationException::withMessages(['descriptor' => 'Khuôn mặt không khớp mẫu đã đăng ký.']);
            }

            return $faces->issue($actor, $data['purpose'], $target);
        }, 5);

        return response()->json(['status' => true, 'data' => $proof]);
    }
}
