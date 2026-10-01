<?php

namespace App\Http\Controllers;

use App\Models\LopHoc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LopHocController extends Controller
{
    public function getGiaoVienClasses()
    {
        $giaoVien = Auth::guard('sanctum')->user();
        if (!$giaoVien) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 401);
        }

        $classes = LopHoc::where('id_giao_vien', $giaoVien->id)
            ->with(['monHoc', 'phongHoc'])
            ->get();

        return response()->json([
            'status' => true,
            'data' => $classes
        ]);
    }
}
