<?php

namespace App\Api\Users\Controllers;

use App\Api\Users\Requests\LoginRequest;
use App\Api\Users\Requests\UserRequest;
use App\Api\Users\Resources\UserResource;
use App\Controller;
use Domain\Users\Actions\SendWelcomeEmailAction;
use Domain\Users\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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

    public function register(UserRequest $request): JsonResponse
    {
//        TODO: Move to an Action and DTOs
        $user = new User();
        $user->name = $request->string('name');
        $user->email = $request->string('email');
        $user->password = Hash::make($request->string('password'));
        $user->locale = app()->getLocale();
        $user->save();

        Log::channel('daily')->info('New user was created.', ['email' => $user->email]);

        (new SendWelcomeEmailAction())->onQueue()->execute($user);

        return response()->json([
            'message' => trans('message.created', [
                'attribute' => trans('validation.attributes.user'),
            ]),
            'data' => UserResource::make($user),
        ]);
    }

//    TODO: Implement Laravel Fortify
}
