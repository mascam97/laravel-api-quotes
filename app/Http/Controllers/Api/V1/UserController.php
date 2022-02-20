<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\UserQuotesResource;
use App\Http\Resources\V1\UserResource;
use App\Models\Quote;
use App\Models\User;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserController extends Controller
{
    /**
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        return UserResource::collection(User::withCount('quotes')
            ->paginate(5));
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
