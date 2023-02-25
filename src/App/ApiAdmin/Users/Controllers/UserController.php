<?php

namespace App\ApiAdmin\Users\Controllers;

use App\ApiAdmin\Users\Queries\UserIndexQuery;
use App\ApiAdmin\Users\Resources\UserResource;
use App\Controller;
use Domain\Users\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\QueryBuilder\QueryBuilder;

class UserController extends Controller
{
    /**
     * @throws AuthorizationException
     */
    public function index(UserIndexQuery $userQuery): AnonymousResourceCollection
    {
        $this->authorize('viewAny', User::class);

        $users = $userQuery->paginate();

        return UserResource::collection($users);
    }

    /**
     * @throws AuthorizationException
     */
    public function show(int $userId): UserResource
    {
        $this->authorize('view', User::class);

        $query = User::query()
            ->whereId($userId);

        $user = QueryBuilder::for($query)
            ->allowedIncludes('permissions')
            ->allowedIncludes('roles')
            ->firstOrFail();

        return UserResource::make($user);
    }

    /**
     * @throws AuthorizationException
     */
    public function destroy(User $user): JsonResponse
    {
        $this->authorize('delete', $user);

        // TODO: Move deletion to an action and delete its related data as quotes
        $user->delete();

        return response()->json([
            'message' => trans('message.deleted', ['attribute' => 'user']),
        ]);
    }
}
