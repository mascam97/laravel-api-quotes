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
