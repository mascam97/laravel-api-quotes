<?php

namespace App\ApiAdmin\Users\Controllers;

use App\ApiAdmin\Users\Queries\UserIndexQuery;
use App\ApiAdmin\Users\Queries\UserShowQuery;
use App\ApiAdmin\Users\Resources\UserResource;
use App\Controller;
use Domain\Users\Actions\DeleteUserAction;
use Domain\Users\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

/** @authenticated */
class UserController extends Controller
{
    /**
     * @bodyParam filter[id] int The ID of the user.
     * @bodyParam filter[name] string The name of the user.
     * @bodyParam filter[trashed] for deleted users, `only` or `with` trashed.
     * @bodyParam include string Include relationship, Example permissions, roles.
     * @bodyParam sort string Sort by column, Example id, name.
     *
     * @throws AuthorizationException
     */
    public function index(UserIndexQuery $userQuery): AnonymousResourceCollection
    {
        $this->authorize('viewAny', User::class);

        $users = $userQuery->paginate();

        return UserResource::collection($users);
    }

    /**
     * @bodyParam include string Include relationship, Example permissions, roles.
     *
     * @throws AuthorizationException
     */
    public function show(UserShowQuery $userQuery, int $userId): UserResource
    {
        $this->authorize('view', User::class);

        $user = $userQuery->where('id', $userId)
            ->firstOrFail();

        return UserResource::make($user);
    }

    /**
     * @throws AuthorizationException
     */
    public function destroy(User $user): JsonResponse
    {
        $this->authorize('delete', $user);

        /** @var User $authUser */
        $authUser = Auth::user();

        (new DeleteUserAction())->__invoke($user);

        activity()
            ->causedBy($authUser)
            ->performedOn($user) // TODO: Validate if soft delete is required for this activity
            ->log('deleted');

        return response()->json([
            'message' => trans('message.deleted', ['attribute' => 'user']),
        ]);
    }
}
