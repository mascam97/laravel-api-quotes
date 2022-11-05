<?php

namespace Domain\Rating\Policies;

use Domain\Rating\Models\Rating;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class RatingPolicy
{
    use HandlesAuthorization;

    public function pass(Model $qualifier, Rating $rating): bool
    {
        if ($rating->qualifier()->is($qualifier)) {
            return true;
        }

        Log::channel('daily')->warning(
            "User {$qualifier->getKey()} tried to delete the rating $rating->id" /* @phpstan-ignore-line */
        );

        return false;
    }
}
