<?php

namespace Domain\Activities\Policies;

use Domain\Users\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ActivityPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $authUser): bool
    {
        return $authUser->can('view any activities');
    }

    public function view(User $authUser): bool
    {
        return $authUser->can('view activities');
    }

    public function delete(User $authUser): bool
    {
        return $authUser->can('delete activities');
    }

    public function export(User $authUser): bool
    {
        return $authUser->can('export activities');
    }
}
