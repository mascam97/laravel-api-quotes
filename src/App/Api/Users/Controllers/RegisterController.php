<?php

namespace App\Api\Users\Controllers;

use App\Api\Users\Requests\UserRequest;
use App\Api\Users\Resources\UserResource;
use App\Controller;
use Domain\Pockets\PocketAggregateRoot;
use Domain\Users\Actions\SendWelcomeEmailAction;
use Domain\Users\Enums\SexEnum;
use Domain\Users\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    public function __invoke(UserRequest $request): JsonResponse
    {
//        TODO: Move to an Action and DTOs
        $user = new User();
        $user->name = $request->string('name');
        $user->email = $request->string('email');
        $user->sex = $request->enum('sex', SexEnum::class);
        $user->birthday = $request->date('birthday');
        $user->password = Hash::make($request->string('password'));
        $user->locale = app()->getLocale();
        $user->email_subscribed_at = now();
        $user->save();

        PocketAggregateRoot::retrieve(Str::orderedUuid())
            ->createPocket($user, 'USD') // TODO: Define currency by user information
            ->persist();

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
