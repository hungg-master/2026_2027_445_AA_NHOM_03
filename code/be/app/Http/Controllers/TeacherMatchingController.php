<?php

namespace App\Http\Controllers;

use App\Services\TeacherMatchingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeacherMatchingController extends Controller
{
    public function index(Request $request, TeacherMatchingService $matching)
    {
        $data = $request->validate(['id_mon_hoc' => 'required|integer|exists:mon_hocs,id', 'date' => 'required|date_format:Y-m-d|after_or_equal:today', 'duration_minutes' => 'required|integer|between:15,180']);

        return response()->json(['status' => true, 'data' => $matching->suggestions(Auth::guard('sanctum')->user(), (int) $data['id_mon_hoc'], Carbon::parse($data['date']), (int) $data['duration_minutes'])]);
    }
}
