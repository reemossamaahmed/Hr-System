<?php

namespace App\Http\Controllers\Api\Hr;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index()
    {
        return Department::with('users')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:departments',
            'description' => 'nullable'
        ]);

        $department = Department::create($request->all());

        return response()->json($department, 201);
    }

    public function update(Request $request, $id)
    {

        $department = Department::find($id);
        if (!$department) {
            return response()->json(['message' => 'Department not found'], 404);
        }


        $department->update($request->only(['name','description']));

        return response()->json([
            'department' => $department->fresh()
        ]);
    }

    public function destroy($id)
    {
        Department::destroy($id);

        return response()->json(['message' => 'deleted']);
    }
}
