<?php

namespace Domain\Users\Policies;

use Domain\Users\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $authUser): bool
    {
        return $authUser->can('view any users');
    }

    public function view(User $authUser): bool
    {
        return $authUser->can('view users');
    }

    public function delete(User $authUser): bool
    {
        return $authUser->can('delete users');
    }
}
