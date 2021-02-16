<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\UserRequest;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        if (Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'token' => $request->user()->createToken($request->device_name)->plainTextToken,
                'message' => 'Success'
            ]);
        }

        return response()->json([
            'message' => 'Unauthorized'
        ], 401);
    }

    public function register(UserRequest $request)
    {
        $user = User::create($request->except('device_name'));
        return response()->json([
            'data' => [
                'user_id' => $user->id
            ],
            'token' => $user->createToken($request->device_name)->plainTextToken,
            'message' => 'User created successfully'
        ]);
    }
}
