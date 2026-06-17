<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    //HR
    public function report(Request $request)
    {
        $month = $request->month ?? date('m');
        $year  = $request->year ?? date('Y');

        $report = Attendance::selectRaw('user_id,COUNT(*) as total_days,SUM(late_minutes) as total_late_minutes,SUM(overtime_hours) as total_overtime_hours')
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->groupBy('user_id')
            ->with('user')
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Attendance report generated successfully',
            'data' => $report
        ]);
    }

    //EMPLOYEE
    public function checkIn()
    {
        $user = Auth::user();

        $today = Carbon::today()->toDateString();

        // 1. Check if already checked in today
        $existing = Attendance::where('user_id', $user->id)
                    ->where('date', $today)
                    ->first();

        if ($existing) {
            return response()->json([
                'status' => false,
                'message' => 'You already checked in today'
            ], 400);
        }

        // 2. Current time
        $now = Carbon::now();

        // 3. Official start time (مثلاً 9 AM)
        $startTime = Carbon::createFromTime(9, 0, 0);

        // 4. Calculate late minutes
        $lateMinutes = 0;

        if ($now->greaterThan($startTime)) {
            $lateMinutes = $now->diffInMinutes($startTime);
        }

        // 5. Status
        $status = $lateMinutes > 0 ? 'late' : 'present';

        // 6. Create attendance
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => $today,
            'check_in' => $now->format('H:i:s'),
            'late_minutes' => $lateMinutes,
            'status' => $status
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Check-in successful',
            'data' => $attendance
        ], 201);
    }

    public function checkOut()
    {
        $user = Auth::user();

        $today = Carbon::today()->toDateString();

        // 1. Get today's attendance record
        $attendance = Attendance::where('user_id', $user->id)
                        ->where('date', $today)
                        ->first();

        // 2. If no check-in found
        if (!$attendance) {
            return response()->json([
                'status' => false,
                'message' => 'You must check in first'
            ], 400);
        }

        // 3. Prevent double check-out
        if ($attendance->check_out) {
            return response()->json([
                'status' => false,
                'message' => 'You already checked out today'
            ], 400);
        }

        // 4. Current time
        $checkOutTime = Carbon::now();

        // 5. Update check-out time
        $attendance->check_out = $checkOutTime->format('H:i:s');

        // 6. Calculate total working hours
        $checkInTime = Carbon::parse($attendance->date . ' ' . $attendance->check_in);

        $totalMinutes = $checkInTime->diffInMinutes($checkOutTime);

        $totalHours = $totalMinutes / 60;

        // 7. Overtime calculation (after 8 hours)
        $overtime = 0;

        if ($totalHours > 8) {
            $overtime = $totalHours - 8;
        }

        $attendance->overtime_hours = round($overtime, 2);

        // 8. Save
        $attendance->save();

        return response()->json([
            'status' => true,
            'message' => 'Check-out successful',
            'data' => $attendance
        ]);
    }
}
