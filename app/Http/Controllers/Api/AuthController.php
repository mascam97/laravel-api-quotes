<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Jobs\SendWelcomeEmail;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

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
                'message' => trans("message.success")
            ]);
        }

        Log::channel('daily')->error('User failed to login.', ['email' => $request->email]);
        return response()->json([
            'message' => trans("auth.unauthorized")
        ], 401);
    }

    public function register(UserRequest $request)
    {
        $user = User::create(
            $request->except('password') + ['password' => Hash::make($request->password)]
        );
        Log::channel('daily')->info('New user was created.', ['email' => $user->email]);

        dispatch(new SendWelcomeEmail($user->email));

        return response()->json([
            'message' => trans("message.created", ["attribute" => "user"])
        ]);
    }
}
