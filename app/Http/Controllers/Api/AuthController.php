<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function api_token_auth(LoginRequest $request)
    {
        if (Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'data' => [
                    'user_logged' => [
                        'id' => $request->user()->id,
                        'name' => $request->user()->name,
                        'email' => $request->user()->email
                    ]
                ],
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
        $user = User::create(
            $request->except('password') + ['password' => Hash::make($request->password)]
        );
        return response()->json([
            'message' => 'User created successfully'
        ]);
    }
}
