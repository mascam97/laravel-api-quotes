<?php

namespace App\Api\Users\Controllers;

use App\Api\Users\Requests\LoginRequest;
use App\Api\Users\Requests\UserRequest;
use App\Jobs\Users\SendWelcomeEmail;
use Domain\Users\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Support\App\Api\Controller;

class AuthController extends Controller
{
    public function api_token_auth(LoginRequest $request): JsonResponse
    {
        if (Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'data' => [
                    'user_logged' => [
                        'id' => $request->user()->id,
                        'name' => $request->user()->name,
                        'email' => $request->user()->email,
                    ],
                ],
                'token' => $request->user()->createToken($request->device_name)->plainTextToken,
                'message' => trans('message.success'),
            ]);
        }

        Log::channel('daily')->error('User failed to login.', ['email' => $request->email]);

        return response()->json([
            'message' => trans('auth.unauthorized'),
        ], 401);
    }

    public function register(UserRequest $request): JsonResponse
    {
        $user = User::create(
            $request->except('password') + ['password' => Hash::make($request->getPassword())]
        );
        Log::channel('daily')->info('New user was created.', ['email' => $user->email]);

        dispatch(new SendWelcomeEmail($user->email));

        return response()->json([
            'message' => trans('message.created', ['attribute' => 'user']),
        ]);
    }
}
