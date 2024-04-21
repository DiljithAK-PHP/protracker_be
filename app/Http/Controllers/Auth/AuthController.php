<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        if($user) {
            return response()->json(['status' => 1, 'message' => 'User created successfully.'], 201);
        } else {
            return response()->json(['status' => 0, 'message' => 'Failed to create user.'], 400);
        }
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            $token = auth()->user()->createToken('auth_token')->accessToken;
            return response()->json(['status' => 1, 'message' => 'Logged in successully.', 'token' => $token], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'Unauthorized'], 401);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['status' => 1, 'message' => 'Logged out'], 200);
    }

}
