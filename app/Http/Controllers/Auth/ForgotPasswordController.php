<?php

namespace App\Http\Controllers\Auth;
use Illuminate\Support\Facades\Password;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;

use App\Models\PasswordReset;

class ForgotPasswordController extends Controller
{
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $otp = mt_rand(100000, 999999);

        PasswordReset::create([
            'email' => $request->email,
            'token' => Hash::make($otp),
            'created_at' => now(),
            'expires_at' => now()->addMinutes(5),
        ]);

        Mail::raw("Your OTP is: $otp", function ($message) use ($request) {
            $message->to($request->email)->subject('Password Reset OTP');
        });

        return response()->json(['status' => 1, 'message' => 'OTP sent to your email'], 200);
    }
}
