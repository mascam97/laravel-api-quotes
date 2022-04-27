<?php

namespace App\Api\Users\Controllers;

use App\Api\Users\Resources\UserResource;
use Domain\Users\Models\User;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\QueryBuilder\QueryBuilder;
use Support\App\Api\Controller;

class UserController extends Controller
{
    /**
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        $users = QueryBuilder::for(User::class)
            ->allowedFilters(['id', 'name'])
            ->allowedIncludes('quotes')
            ->allowedSorts('id', 'name')
            ->get();

        return UserResource::collection($users);
    }

    /**
     * @param int $userId
     * @return UserResource
     */
    public function show(int $userId): UserResource
    {
        $user = QueryBuilder::for(User::query()->where('id', $userId))
            ->whereId($userId)
            ->allowedIncludes('quotes')
            ->firstOrFail();

        return UserResource::make($user);
    }

//    TODO: Add missing update and destroy functions
}
