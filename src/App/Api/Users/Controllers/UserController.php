<?php

namespace App\Api\Users\Controllers;

use App\Api\Users\Queries\UserIndexQuery;
use App\Api\Users\Resources\UserResource;
use App\Controller;
use Domain\Users\Models\User;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\QueryBuilder\QueryBuilder;

class UserController extends Controller
{
    public function index(UserIndexQuery $userQuery): AnonymousResourceCollection
    {
        $users = $userQuery->paginate();

        return UserResource::collection($users);
    }

    public function show(int $userId): UserResource
    {
        $query = User::query()
            ->select([
                'id',
                'name',
                'email',
                'created_at',
            ])
            ->whereId($userId);

        $user = QueryBuilder::for($query)
            ->allowedIncludes('quotes')
            ->firstOrFail();

        return UserResource::make($user);
    }

//    TODO: Add missing update and destroy functions
}
