<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Employee\AttendanceController;
use App\Http\Controllers\Api\Hr\HrAttendanceController;
use App\Http\Controllers\Api\Hr\PayrollController;
use App\Http\Controllers\Api\Hr\DepartmentController;
use App\Http\Controllers\Api\Hr\EmployeeController;
use Illuminate\Support\Facades\Route;


Route::prefix('auth')->controller(AuthController::class)->group(function () {
    Route::post('/login', 'login');

    Route::post('/forgot-password','forgotPassword');

    Route::post('/verify-otp', 'verifyOtp');

    Route::post('/reset-password','resetPassword');
});


Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
});


Route::middleware(['auth:sanctum','role:HR'])->prefix('hr')->group(function () {

    Route::prefix('/employees')->group(function(){
        // Create employee
        Route::post('/', [EmployeeController::class, 'store']);

        // Get all employees
        Route::get('/', [EmployeeController::class, 'index']);

        // Get single employee
        Route::get('/{employeeId}', [EmployeeController::class, 'show']);

        // Update employee
        Route::put('/{employeeId}', [EmployeeController::class, 'update']);

        // Delete employee
        Route::delete('/{employeeId}', [EmployeeController::class, 'destroy']);
    });

    // Departments
    Route::prefix('/departments')->group(function () {

        Route::post('/', [DepartmentController::class, 'store']);

        Route::get('/', [DepartmentController::class, 'index']);

        Route::get('/{departmentId}', [DepartmentController::class, 'show']);

        Route::put('/{departmentId}', [DepartmentController::class, 'update']);

        Route::delete('/{departmentId}', [DepartmentController::class, 'destroy']);

    });

    // Payroll
    Route::post('/payroll/generate', [PayrollController::class, 'generate']);

    // Get all attendances
    Route::get('/attendances', [HrAttendanceController::class, 'index']);

});























Route::middleware(['auth:sanctum','role:Employee'])->prefix('employee')->controller(AttendanceController::class)->group(function () {

        Route::post('/attendance/check-in', 'checkIn');
        Route::post('/attendance/check-out','checkOut');

});





