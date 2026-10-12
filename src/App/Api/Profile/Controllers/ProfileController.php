<?php

namespace App\Api\Profile\Controllers;

use App\Api\Profile\Requests\UpdatePasswordRequest;
use App\Api\Profile\Requests\UpdateProfileRequest;
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
        // TODO: use include=pocket as http query parameter to load pocket
        $authUser->load('pocket');

        return ProfileResource::make($authUser);
    }

    /**
     * @bodyParam name string The display name. Example: Updated name
     * @bodyParam sex string The user's sex enum. Example: FEMININE
     * @bodyParam birthday date The user's birthday. Example: 1990-05-20
     */
    public function update(UpdateProfileRequest $request): ProfileResource
    {
        /** @var User $authUser */
        $authUser = $request->user();
        $authUser->fill($request->validated());
        $authUser->save();

        return ProfileResource::make($authUser);
    }

    /**
     * @bodyParam current_password string required The current password.
     * @bodyParam password string required The new password.
     * @bodyParam password_confirmation string required Confirmation of the new password.
     */
    public function updatePassword(UpdatePasswordRequest $request): JsonResponse
    {
        /** @var User $authUser */
        $authUser = $request->user();
        $authUser->password = Hash::make($request->string('password')->toString());
        $authUser->save();
        $authUser->tokens()->delete();

        return response()->json([
            'message' => trans('message.password_updated'),
        ]);
    }

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
