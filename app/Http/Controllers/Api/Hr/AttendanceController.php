<?php

namespace App\Http\Controllers\Api\Hr;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Carbon\Carbon;


class AttendanceController extends Controller
{
    //CHECK IN
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'check_in' => 'required|date_format:H:i',
        ]);

        // $startWork = '09:00';

        // $lateMinutes = 0;

        $exists = Attendance::where('user_id', $request->user_id)
            ->where('date', now()->toDateString())
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Already checked in today'
            ], 400);
        }


        $startWork = \Carbon\Carbon::createFromTime(9, 0);

        $checkIn = \Carbon\Carbon::createFromFormat('H:i', $request->check_in);

        $lateMinutes = $checkIn->greaterThan($startWork)? $startWork->diffInMinutes($checkIn): 0;




        // if ($request->check_in > $startWork) {
        //     $lateMinutes = \Carbon\Carbon::parse($startWork)
        //         ->diffInMinutes(\Carbon\Carbon::parse($request->check_in));
        // }

        $attendance = Attendance::create([
            'user_id' => $request->user_id,
            'date' => now()->toDateString(),
            'check_in' => $checkIn,
            'late_minutes' => $lateMinutes,
        ]);

        return response()->json([
            'message' => 'Attendance checked in',
            'data' => $attendance
        ]);
    }

    //CHECK OUT
    public function checkout(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'check_out' => 'required|date_format:H:i',
        ]);

        // نجيب Attendance بتاع النهارده
        $attendance = Attendance::where('user_id', $request->user_id)
            ->where('date', now()->toDateString())
            ->first();

        if (!$attendance) {
            return response()->json([
                'message' => 'No check-in record found for today'
            ], 404);
        }

        if ($attendance->check_out) {
            return response()->json([
                'message' => 'Already checked out today'
            ], 400);
        }
        // update check-out
        $attendance->check_out = $request->check_out;

        // حساب ساعات العمل
        if ($attendance->check_in) {
            $checkIn = Carbon::parse($attendance->check_in);
            $checkOut = Carbon::parse($request->check_out);

            $hours = $checkIn->diffInMinutes($checkOut) / 60;

            $attendance->working_hours = round($hours, 2);
        }

        $attendance->save();

        return response()->json([
            'message' => 'Check-out saved successfully',
            'data' => $attendance
        ]);
    }
}
