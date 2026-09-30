<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FaceIdPhotoController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'face_id_photo' => 'required|image|mimes:jpg,jpeg,png,webp|dimensions:min_width=1,min_height=1|max:5120',
        ]);

        $user = Auth::guard('sanctum')->user();
        $oldPath = $user->face_id_photo_path;
        $path = $request->file('face_id_photo')->store('face-id/'.$user->getTable().'/'.$user->id, 'local');
        if (!$path) {
            return response()->json(['status' => false, 'message' => 'Không thể lưu ảnh. Vui lòng thử lại.'], 500);
        }

        try {
            $user->face_id_photo_path = $path;
            $user->save();
        } catch (\Throwable $exception) {
            Storage::disk('local')->delete($path);
            throw $exception;
        }

        if ($oldPath) {
            Storage::disk('local')->delete($oldPath);
        }

        return response()->json([
            'status' => true,
            'message' => 'Đã lưu ảnh. Ảnh chưa được xác thực danh tính.',
        ]);
    }
}
