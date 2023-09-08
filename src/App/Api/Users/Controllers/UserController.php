<?php

namespace App\Api\Users\Controllers;

use App\Api\Users\Queries\UserIndexQuery;
use App\Api\Users\Queries\UserShowQuery;
use App\Api\Users\Resources\UserResource;
use App\Controller;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/** @authenticated */
class UserController extends Controller
{
    /**
     * @bodyParam filter[id] int Filter by id Example: 1
     * @bodyParam filter[name] string Filter by name Example: John
     * @bodyParam include string Include relationships Example: quotes
     * @bodyParam sort string Sort by fields Example: id,name
     */
    public function index(UserIndexQuery $userQuery): AnonymousResourceCollection
    {
        $users = $userQuery->paginate();

        return UserResource::collection($users);
    }

    /**
     * @bodyParam include string Include relationships Example: quotes
     */
    public function show(UserShowQuery $userQuery, int $userId): UserResource
    {
        $user = $userQuery->where('id', $userId)
            ->firstOrFail();

        return UserResource::make($user);
    }
}
