<?php

namespace App\Http\Controllers\Api\Hr;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Payroll;
use App\Models\User;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    public function generate(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'month' => 'required|date',
        ]);


        $user = User::find($request->user_id);

        if (!$user->hasRole('Employee')) {
            return response()->json([
                'message' => 'Payroll can only be generated for employees'
            ], 400);
        }


        $exists = Payroll::where('user_id', $request->user_id)
        ->where('month', $request->month)
        ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Payroll already generated'
            ], 409);
        }


        $attendances = Attendance::where('user_id', $user->id)
            ->whereYear('date', date('Y', strtotime($request->month)))
            ->whereMonth('date', date('m', strtotime($request->month)))
            ->get();

        $totalLate = $attendances->sum('late_minutes');

        // 🔥 15 minute rule
        $lateUnits = intdiv($totalLate, 15);

        $hourRate = $user->base_salary / 30 / 8;

        $deduction = $lateUnits * $hourRate;

        $netSalary = $user->base_salary - $deduction;

        $payroll = Payroll::create([
            'user_id' => $user->id,
            'month' => $request->month,
            'base_salary' => $user->base_salary,
            'total_late_minutes' => $totalLate,
            'deduction' => $deduction,
            'net_salary' => $netSalary,
        ]);

        return response()->json([
            'message' => 'Payroll generated successfully',
            'data' => $payroll
        ]);
    }
}
