<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PasswordOtp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        $token = $user->createToken('auth-token',[$user->role])->plainTextToken;

        return response()->json([
            'message' => 'Logged in successfully',
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()
            ->currentAccessToken()
            ->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }

    public function me(Request $request)
    {
        return response()->json([
            'user' => $request->user()
        ]);
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        // OTP 6 digits
        $otp = rand(100000, 999999);

        PasswordOtp::create([
            'email' => $request->email,
            'otp' => $otp,
            'expires_at' => Carbon::now()->addMinutes(10),
        ]);

        Mail::raw(
            "Your OTP code is: $otp\nIt expires in 10 minutes.",
            function ($message) use ($request) {
                $message->to($request->email)
                    ->subject('Password Reset OTP');
            }
        );

        return response()->json([
            'message' => 'OTP sent to email'
        ]);
    }


    public function verifyOtpAndReset(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        $otpRecord = PasswordOtp::where('email', $request->email)
            ->where('otp', $request->otp)
            ->where('used', false)
            ->first();

        if (!$otpRecord) {
            return response()->json([
                'message' => 'Invalid OTP'
            ], 400);
        }

        if (Carbon::now()->gt($otpRecord->expires_at)) {
            return response()->json([
                'message' => 'OTP expired'
            ], 400);
        }

        // update password
        $employee = User::where('email', $request->email)->first();

        $employee->password = Hash::make($request->password);
        $employee->save();

        // mark OTP as used
        $otpRecord->used = true;
        $otpRecord->save();

        return response()->json([
            'message' => 'Password reset successfully'
        ]);
    }










}
