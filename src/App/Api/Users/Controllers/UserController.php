<?php

namespace App\Api\Users\Controllers;

use App\Api\Users\Queries\UserIndexQuery;
use App\Api\Users\Queries\UserShowQuery;
use App\Api\Users\Resources\UserResource;
use App\Controller;
use Domain\Users\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserController extends Controller
{
    public function me(Request $request): UserResource
    {
        /** @var User $authUser */
        $authUser = $request->user();

        return UserResource::make($authUser);
    }

    public function index(UserIndexQuery $userQuery): AnonymousResourceCollection
    {
        $users = $userQuery->paginate();

        return UserResource::collection($users);
    }

    public function show(UserShowQuery $userQuery, int $userId): UserResource
    {
        $user = $userQuery->where('id', $userId)
            ->firstOrFail();

        return UserResource::make($user);
    }

//    TODO: Add missing update and destroy functions
}
