<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\UserResource;
use App\Models\User;
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
     * @param User $user
     * @return UserResource
     */
    public function show(User $user): UserResource
    {
        $user->quotes_count = count($user->quotes);

        return UserResource::make($user);
    }

//    TODO: Add missing update and destroy functions
}
