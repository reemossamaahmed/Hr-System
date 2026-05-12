<?php

namespace App\Http\Controllers\Api\Hr;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEmployeeRequest;
use App\Mail\WelcomeMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class EmployeeController extends Controller
{
    public function store(StoreEmployeeRequest $request)
    {

        $password = Str::random(8);
        // $passwordUnHashed = $request->password;
        // dd($passwordUnHashed);
        $employee = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $password,
            'phone' => $request->phone,
            'role' => 'employee',
            'position' => $request->position,
            'department_id' => $request->department_id,
            'base_salary' => $request->base_salary,
            'hire_date' => $request->hire_date,
            'status' => 'active',
            'address' => $request->address,
            'national_id' => $request->national_id,
        ]);

        Mail::to($employee->email)->send(new WelcomeMail($employee, $password));

        return response()->json([
            'message' => 'Employee created successfully',
            'employee' => $employee,
        ], 201);
    }
}
