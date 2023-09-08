<?php

namespace App\Api\Profile\Controllers;

use App\Api\Profile\Resources\ProfileResource;
use App\Controller;
use Domain\Users\Actions\DeleteUserAction;
use Domain\Users\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/** @authenticated */
class ProfileController extends Controller
{
    public function show(Request $request): ProfileResource
    {
        /** @var User $authUser */
        $authUser = $request->user();

        return ProfileResource::make($authUser);
    }

//    TODO: Add update functions with password confirmation, email verification, etc.

    public function destroy(Request $request): JsonResponse
    {
        $request->validate(['password' => ['required']]);

        // TODO: Use OAuth2 standard instead of password
        $password = $request->input('password');
        /** @var User $authUser */
        $authUser = $request->user();

        // TODO: Use OTP email verification instead of password
        if (! Hash::check($password, $authUser->password)) {
            return response()->json([
                'message' => trans('auth.failed'),
            ], 400);
        }

        (new DeleteUserAction())->__invoke($authUser);

        return response()->json([
            'message' => trans('message.deleted', ['attribute' => 'user']),
        ]);
    }
}
