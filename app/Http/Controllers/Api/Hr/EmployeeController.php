<?php

namespace App\Http\Controllers\Api\Hr;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Mail\WelcomeMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class EmployeeController extends Controller
{

    // To Show All Employee Or Fillter
    public function index(Request $request)
    {

        $query = User::role('Employee')->with('department');


        if($request->search)
        {

            $query->where(function($q) use ($request){

                $q->where('name','like',"%{$request->search}%")
                  ->orWhere('email','like',"%{$request->search}%");
            });

        }
        if($request->department_id)
        {
            $query->where('department_id',$request->department_id);
        }


        if($request->status)
        {
            $query->where('status',$request->status);
        }


        $employees = $query->paginate(10);


        return response()->json([

            'status' => true,

            'message' => 'Employees retrieved successfully',

            'data' => $employees

        ]);

    }

    // To Show Specific Employee
    public function show(int $employeeId)
    {

        $employee = User::role('Employee')->with('department')->find($employeeId);

        if(!$employee)
        {
            return response()->json([
                'status' => false,
                'message' => 'Employee not found',
            ],404);
        }

        return response()->json([

            'status' => true,

            'message' => 'Employee retrieved successfully',

            'data' => $employee

        ]);

    }

    // To Store Employee
    public function store(StoreEmployeeRequest $request)
    {
        $data = $request->validated();

        $password = Str::random(8);

        $data['password'] = Hash::make($password);

        if ($request->hasFile('profile_image'))
        {
            $path = $request->file('profile_image')->store('employees','public');
            $data['profile_image'] = $path;
        }

        $employee = User::create($data);

        $employee->assignRole('Employee');

        Mail::to($employee->email)->send(new WelcomeMail($employee, $password));

        return response()->json([
            'status' => true,
            'message' => 'Employee created successfully',
            'data' => $employee,
        ], 201);
    }

    // To Update Employee
    public function update(UpdateEmployeeRequest $request,int $employeeId)
    {
        $employee = User::role('Employee')->find($employeeId);
        if(!$employee)
        {
            return response()->json([
                'status' => false,
                'message' => 'Employee not found',
            ],404);
        }

            $data = $request->validated();

            if($request->hasFile('profile_image'))
            {

                if($employee->profile_image && Storage::disk('public')->exists($employee->profile_image))
                {
                    Storage::disk('public')->delete($employee->profile_image);
                }

                $path = $request->file('profile_image')->store('employees','public');
                $data['profile_image'] =$path;

            }

            $employee->update($data);
            return response()->json([
                    'status' => true,
                    'message' => 'Employee updated successfully',
                    'data' => $employee->load('department')
            ]);
    }

    public function destroy(int $employeeId)
    {
        $employee = User::role('Employee')->find($employeeId);

        if (!$employee) {
            return response()->json([
                'status' => false,
                'message' => 'Employee not found'
            ], 404);
        }

        // (Optional) delete profile image
        if ($employee->profile_image && Storage::disk('public')->exists($employee->profile_image))
        {
            Storage::disk('public')->delete($employee->profile_image);
        }

        // Soft delete
        $employee->delete();

        return response()->json([
            'status' => true,
            'message' => 'Employee deleted successfully'
        ]);
    }

}
