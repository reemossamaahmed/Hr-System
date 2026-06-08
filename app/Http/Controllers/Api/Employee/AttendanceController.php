<?php

namespace App\Http\Controllers\Api\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function checkIn(Request $request)
    {
        $user = auth()->user();

        // منع HR
        if (!$user->hasRole('Employee')) {
            return response()->json([
                'message' => 'Only employees can check in'
            ], 403);
        }

        $today = now()->toDateString();

        // منع تكرار check-in
        $exists = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Already checked in today'
            ], 400);
        }

        // $startWork = Carbon::createFromTime(9, 0);
        // $checkInTime = now();

        // $startWork = now()->copy()->setTime(9, 0);
        // $checkInTime = now();

        $startWork = Carbon::parse(today()->toDateString() . ' 09:00:00');
        $checkInTime = now();

        $lateMinutes = $checkInTime->greaterThan($startWork)
            ? $startWork->diffInMinutes($checkInTime)
            : 0;

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => $today,
            'check_in' => $checkInTime->format('H:i'),
            'late_minutes' => $lateMinutes,
        ]);

        return response()->json([
            'message' => 'Checked in successfully',
            'data' => $attendance
        ]);
    }

    public function checkOut(Request $request)
    {
        $user = auth()->user();

        if (!$user->hasRole('Employee')) {
            return response()->json([
                'message' => 'Only employees can check out'
            ], 403);
        }

        $today = now()->toDateString();

        $attendance = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        if (!$attendance) {
            return response()->json([
                'message' => 'You must check in first'
            ], 400);
        }

        if ($attendance->check_out) {
            return response()->json([
                'message' => 'Already checked out'
            ], 400);
        }

        $checkOutTime = now();

        $attendance->check_out = $checkOutTime->format('H:i');

        // حساب ساعات العمل
        $checkIn = Carbon::parse($attendance->check_in);
        $hours = $checkIn->diffInMinutes($checkOutTime) / 60;

        $attendance->working_hours = round($hours, 2);

        $attendance->save();

        return response()->json([
            'message' => 'Checked out successfully',
            'data' => $attendance
        ]);
    }
}
