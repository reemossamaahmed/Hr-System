<?php

namespace App\Http\Controllers\Api\Hr;

use App\Http\Controllers\Controller;
use App\Http\Requests\GeneratePayrollRequest;
use App\Models\Attendance;
use App\Models\Payroll;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PayrollController extends Controller
{
    // public function generate(Request $request)
    // {
    //     $request->validate([
    //         'user_id' => 'required|exists:users,id',
    //         'month' => 'required|date',
    //     ]);


    //     $user = User::find($request->user_id);

    //     if (!$user->hasRole('Employee')) {
    //         return response()->json([
    //             'message' => 'Payroll can only be generated for employees'
    //         ], 400);
    //     }


    //     $exists = Payroll::where('user_id', $request->user_id)
    //     ->where('month', $request->month)
    //     ->exists();

    //     if ($exists) {
    //         return response()->json([
    //             'message' => 'Payroll already generated'
    //         ], 409);
    //     }


    //     $attendances = Attendance::where('user_id', $user->id)
    //         ->whereYear('date', date('Y', strtotime($request->month)))
    //         ->whereMonth('date', date('m', strtotime($request->month)))
    //         ->get();

    //     $totalLate = $attendances->sum('late_minutes');

    //     // 🔥 15 minute rule
    //     $lateUnits = intdiv($totalLate, 15);

    //     $hourRate = $user->base_salary / 30 / 8;

    //     $deduction = $lateUnits * $hourRate;

    //     $netSalary = $user->base_salary - $deduction;

    //     $payroll = Payroll::create([
    //         'user_id' => $user->id,
    //         'month' => $request->month,
    //         'base_salary' => $user->base_salary,
    //         'total_late_minutes' => $totalLate,
    //         'deduction' => $deduction,
    //         'net_salary' => $netSalary,
    //     ]);

    //     return response()->json([
    //         'message' => 'Payroll generated successfully',
    //         'data' => $payroll
    //     ]);
    // }


    public function generate(GeneratePayrollRequest $request)
    {
        DB::beginTransaction();

        try{

            $month = $request->month;
            $year = $request->year;
            $employees = User::role('employee')->get();
            $payrolls = [];
            foreach($employees as $employee)
            {
                $exists = Payroll::where('user_id',$employee->id)
                                ->where('month',$month)
                                ->where('year',$year)->exists();
                if($exists)
                {
                    continue;
                }

                $attendances = Attendance::where('user_id',$employee->id)
                                        ->whereMonth('date',$month)
                                        ->whereYear('date',$year)->get();
                $baseSalary = $employee->base_salary;
                $totalLateMinutes = $attendances->sum('late_minutes');
                $totalOvertimeHours = $attendances->sum('overtime_hours');
                $lateDeduction = $totalLateMinutes * 1; //كل دقيقة تأخير 1 جنيه
                $overtimeAmount = $totalOvertimeHours * 50; //كل ساعة إضافية 50 جنيه
                $absenceDeduction = 0;
                $netSalary = $baseSalary + $overtimeAmount - $lateDeduction - $absenceDeduction;

                 $payroll = Payroll::create([
                    'user_id' => $employee->id,
                    'month' => $month,
                    'year' => $year,
                    'base_salary' => $baseSalary,
                    'overtime_amount' => $overtimeAmount,
                    'late_deduction' => $lateDeduction,
                    'absence_deduction' => $absenceDeduction,
                    'net_salary' => $netSalary,
                    'status' => 'pending'
                ]);
                $payrolls[] = $payroll;
            }

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Payroll generated successfully',
                'data' =>$payrolls
            ],201);
        }

        catch(\Exception $e)
        {
            DB::rollBack();
            return response()->json([
                'status'=> false,
                'message'=> $e->getMessage()
            ],500);
        }
    }
}
