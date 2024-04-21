<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Facades\Password;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Str;

use App\Models\User;
use App\Models\PasswordReset;

class ResetPasswordController extends Controller
{
    public function verifyOTP(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|numeric',
        ]);

        $passwordReset = PasswordReset::where('email', $request->email)
            ->where('expires_at', '>=', now())
            ->latest()
            ->first();

        if (!$passwordReset) {
            return response()->json(['status' => 0, 'message' => 'Invalid OTP or OTP expired'], 400);
        }

        if (!Hash::check($request->otp, $passwordReset->token)) {
            return response()->json(['status' => 0, 'message' => 'Invalid OTP'], 400);
        }

        $newToken = Str::random(60);
        $passwordReset->token = Hash::make($newToken);

        $passwordReset->expires_at = Carbon::now()->addMinutes(5);

        $passwordReset->save();

        return response()->json(['status' => 1, 'message' => 'OTP verified', 'token' => $newToken], 200);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required|min:8|confirmed',
            'token' => 'required',
        ]);

        $passwordReset = PasswordReset::where('email', $request->email)
            ->where('expires_at', '>=', now())
            ->first();

        if (!$passwordReset || ($request->token == $passwordReset->token)) {
            return response()->json(['status' => 0, 'message' => 'Invalid token or token expired'], 400);
        }

        $user = User::where('email', $passwordReset->email)->firstOrFail();
        $user->password = Hash::make($request->password);
        $user->save();

        $passwordReset->delete();

        return response()->json(['status' => 1, 'message' => 'Password reset successfully'], 200);
    }
}
