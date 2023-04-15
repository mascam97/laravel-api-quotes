<?php

namespace App\ExternalApi\Users\Controllers;

use App\Controller;
use App\ExternalApi\Users\Requests\LoginRequest;
use Domain\Users\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        // TODO: Implement Laravel Passport and use different credentials for each application
        if (Auth::attempt($request->only('email', 'password'))) {
            /** @var User $authUser */
            $authUser = $request->user();

            return response()->json([
                'message' => trans('message.success'),
                'token' => $authUser->createToken($request->string('device_name'))->plainTextToken,
            ]);
        }

        Log::channel('daily')->error('User failed to login.', ['email' => $request->string('email')]);

        return response()->json([
            'message' => trans('auth.unauthorized'),
        ], 401);
    }
}
