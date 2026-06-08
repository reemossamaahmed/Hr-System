<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PasswordOtp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required'
        ]);
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }


        // 👇 device fingerprint
        $device = $request->header('User-Agent');

        // 👇 امسح توكن نفس الجهاز
        $user->tokens()
            ->where('name', $device)
            ->delete();

        // 👇 اعمل token جديد مرتبط بالجهاز
        $token = $user->createToken(
            $device,
            // [$user->role]
            $user->getRoleNames()->toArray()
        )->plainTextToken;

        // $token = $user->createToken('auth-token',[$user->role])->plainTextToken;

        // return response()->json([
        //     'message' => 'Logged in successfully',
        //     'user' => $user,
        //     'token' => $token,
        // ]);

        return response()->json([
            'message' => 'Logged in successfully',
            'user' => $user
        ])->cookie('token',$token,60 * 24 * 7,'/',null,false, // true in production HTTPS
            true,false,'Lax' //'Strict'
            );
    }

    public function logout(Request $request)
    {
        $request->user()
            ->currentAccessToken()
            ->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ])->withoutCookie('token');
    }

    public function me(Request $request)
    {
        return response()->json([
            'user' => $request->user()
        ]);
    }


    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user)
        {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        // delete old unused OTPs
        PasswordOtp::where('email', $request->email)
            ->where('used', false)
            ->delete();

        // secure OTP
        $otp = random_int(100000, 999999);

        PasswordOtp::create([
            'email' => $request->email,
            'otp' => $otp,
            'expires_at' => now()->addMinutes(10),
            'used' => false,
        ]);

        Mail::raw(
            "Your OTP code is: $otp\nThis code expires in 10 minutes.",
            function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('Reset Password OTP');
            }
        );

        return response()->json([
            'message' => 'OTP sent successfully'
        ]);
    }



    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|digits:6'
        ]);

        $otpRecord = PasswordOtp::where('email', $request->email)
            ->where('otp', $request->otp)
            ->where('used', false)
            ->latest()
            ->first();

        if (!$otpRecord) {
            return response()->json([
                'message' => 'Invalid OTP'
            ], 400);
        }

        // check expiration
        if (now()->gt($otpRecord->expires_at)) {
            return response()->json([
                'message' => 'OTP expired'
            ], 400);
        }

        $otpRecord->update([
            'verified_at' => now()
        ]);


        return response()->json([
            'message' => 'OTP verified successfully'
        ]);
    }


    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|digits:6',
            // 'password' => 'required|min:6|confirmed'
            'password' => [
                'required',
                'confirmed',
                Password::min(6)->letters()->numbers()
            ]
        ]);

        $otpRecord = PasswordOtp::where('email', $request->email)
            ->where('otp', $request->otp)
            ->where('used', false)
            ->latest()
            ->first();

        if (!$otpRecord) {
            return response()->json([
                'message' => 'Invalid OTP'
            ], 400);
        }

        if (now()->gt($otpRecord->expires_at)) {
            return response()->json([
                'message' => 'OTP expired'
            ], 400);
        }

        if (!$otpRecord->verified_at) {
            return response()->json([
                'message' => 'OTP must be verified first'
            ], 400);
        }

        // update password
        $employee = User::where('email', $request->email)->first();

        $employee->password = Hash::make($request->password);
        $employee->save();

        // revoke all tokens
        $employee->tokens()->delete();
        // mark OTP as used
        $otpRecord->used = true;
        $otpRecord->save();

        return response()->json([
            'message' => 'Password reset successfully'
        ]);
    }



}
