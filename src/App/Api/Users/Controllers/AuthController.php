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

            /** @var User $authUser */
            $authUser = $request->user();

            return response()->json([
                'data' => [
                    'user' => [
                        //                        TODO: Use a resource and move to /me
                        'id' => $authUser->getKey(),
                        'name' => $authUser->name,
                        'email' => $authUser->email,
                    ],
                ],
                'token' => $request->user()->createToken($request->input('device_name'))->plainTextToken,
                'message' => trans('message.success'),
            ]);
        }

        Log::channel('daily')->error('User failed to login.', ['email' => $request->input('email')]);

        return response()->json([
            'message' => trans('auth.unauthorized'),
        ], 401);
    }

    public function register(UserRequest $request): JsonResponse
    {
//        TODO: Move to an Action and DTOs
        $user = new User();
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->password = Hash::make($request->input('password'));
        $user->save();

        Log::channel('daily')->info('New user was created.', ['email' => $user->email]);

        dispatch(new SendWelcomeEmail($user->email));

        return response()->json([
            'message' => trans('message.created', ['attribute' => 'user']),
        ]);
    }

//    TODO: Implement Laravel Fortify
}
