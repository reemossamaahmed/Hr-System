<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Hr\AttendanceController;
use App\Http\Controllers\Api\Hr\PayrollController;
use App\Http\Controllers\Api\Hr\DepartmentController;
use App\Http\Controllers\Api\Hr\EmployeeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;




// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');




Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
});

Route::middleware(['auth:sanctum','role:hr'])->prefix('hr')->group(function () {
    Route::post('/employees',[EmployeeController::class, 'store']);
    // Attendance
    Route::post('/attendance/check-in', [AttendanceController::class, 'store']);
    Route::post('/attendance/check-out', [AttendanceController::class, 'checkout']);

    // Payroll
    Route::post('/payroll/generate', [PayrollController::class, 'generate']);

    Route::apiResource('departments', DepartmentController::class);
});


Route::middleware(['auth:sanctum','role:employee'])->prefix('employee')->group(function () {

});





Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);

    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);

    Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);

    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
});
