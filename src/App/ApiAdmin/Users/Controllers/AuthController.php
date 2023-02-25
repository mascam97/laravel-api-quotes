<?php

namespace App\ApiAdmin\Users\Controllers;

use App\ApiAdmin\Users\Requests\LoginRequest;
use App\ApiAdmin\Users\Resources\UserResource;
use App\Controller;
use Domain\Users\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        if (Auth::attempt($request->only('email', 'password'))) {
            /** @var User $authUser */
            $authUser = $request->user();

            return response()->json([
                'data' => [
                    'user' => UserResource::make($authUser),
                ],
                'token' => $authUser->createToken($request->string('device_name'))->plainTextToken,
                'message' => trans('message.success'),
            ]);
        }

        Log::channel('daily')->error('User failed to login.', ['email' => $request->string('email')]);

        return response()->json([
            'message' => trans('auth.unauthorized'),
        ], 401);
    }
}
