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
     * @param int $user_id
     * @return UserResource
     */
    public function show(int $user_id): UserResource
    {
        $user = QueryBuilder::for(User::query()->where('id', $user_id))
            ->allowedIncludes('quotes')
            ->firstOrFail();

        return UserResource::make($user);
    }

//    TODO: Add missing update and destroy functions
}
