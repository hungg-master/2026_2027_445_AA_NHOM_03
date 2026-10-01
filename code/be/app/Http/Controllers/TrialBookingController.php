<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TrialBookingController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'required|digits:10',
            'subject' => 'required|string|max:150',
            'schedules' => 'required|array|min:1|max:98',
            'schedules.*' => ['required', 'string', 'distinct', 'regex:/^(Mon|Tue|Wed|Thu|Fri|Sat|Sun)_([1-9]|1[0-2]):00 (AM|PM)$/'],
        ]);
        DB::table('trial_bookings')->insert([
            ...$data,
            'schedules' => json_encode($data['schedules']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Đã tiếp nhận yêu cầu học thử. Lịch học đang chờ xác nhận.',
        ], 201);
    }
}
