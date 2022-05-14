<?php

namespace App\Api\Users\Controllers;

use App\Api\Users\Queries\UserIndexQuery;
use App\Api\Users\Resources\UserResource;
use Domain\Users\Models\User;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\QueryBuilder\QueryBuilder;
use Support\App\Api\Controller;

class UserController extends Controller
{
    public function index(UserIndexQuery $userQuery): AnonymousResourceCollection
    {
        $users = $userQuery->get();

        return UserResource::collection($users);
    }

    public function show(int $userId): UserResource
    {
        $user = QueryBuilder::for(User::class)
            ->whereId($userId)
            ->allowedIncludes('quotes')
            ->firstOrFail();

        return UserResource::make($user);
    }

//    TODO: Add missing update and destroy functions
}
