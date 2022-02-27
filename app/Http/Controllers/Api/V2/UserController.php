<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Http\Resources\V2\UserQuotesResource;
use App\Http\Resources\V2\UserResource;
use App\Models\Quote;
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
            ->allowedFilters('name')
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

        return new UserResource($user);
    }

    /**
     * @param User $user
     * @return AnonymousResourceCollection
     */
    public function index_quotes(User $user): AnonymousResourceCollection
    {
        return UserQuotesResource::collection(Quote::where('user_id', $user->id)
            ->paginate(6));
    }
}
