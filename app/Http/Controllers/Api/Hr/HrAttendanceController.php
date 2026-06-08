<?php

namespace App\Http\Controllers\Api\Hr;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;

class HrAttendanceController extends Controller
{
     public function index(Request $request)
    {
        $attendances = Attendance::with('user')
            ->orderBy('date', 'desc')
            ->get();

        return response()->json([
            'message' => 'Attendance records fetched successfully',
            'data' => $attendances
        ]);
    }
}
