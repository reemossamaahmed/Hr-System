<?php

namespace App\Http\Controllers\Api\Hr;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::withCount('users')->get();

        return response()->json([
            'status' => true,
            'data' => $departments
        ]);
    }
    public function show($departmentId)
    {
        $department = Department::with('users')->find($departmentId);

        if (!$department) {
            return response()->json([
                'status' => false,
                'message' => 'Department not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $department
        ]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:departments,name',
            'description' => 'nullable|string',
            'status' => 'in:active,inactive',
            "code" =>"required|string|unique:departments,code"
        ]);

        $department = Department::create($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Department created successfully',
            'data' => $department
        ], 201);
    }



    public function update(Request $request, $departmentId)
    {
        $department = Department::find($departmentId);

        if (!$department) {
            return response()->json([
                'status' => false,
                'message' => 'Department not found'
            ], 404);
        }

        $request->validate([
            'name' => 'sometimes|string|unique:departments,name',
            'description' => 'nullable|string',
            'status' => 'in:active,inactive'
        ]);

        $department->update($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Department updated successfully',
            'data' => $department
        ]);
    }

    public function destroy($departmentId)
    {
        $department = Department::withCount('users')->find($departmentId);

        if (!$department) {
            return response()->json([
                'status' => false,
                'message' => 'Department not found'
            ], 404);
        }

        if ($department->employees_count > 0) {
            return response()->json([
                'status' => false,
                'message' => 'Cannot delete department with employees'
            ], 400);
        }

        $department->delete();

        return response()->json([
            'status' => true,
            'message' => 'Department deleted successfully'
        ]);
    }
}
