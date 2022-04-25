<?php

namespace App\Api\Controllers\Api\V1;

use App\Api\Controllers\Controller;
use App\Api\Resources\V1\UserResource;
use Domain\Users\Models\User;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\QueryBuilder\QueryBuilder;

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
