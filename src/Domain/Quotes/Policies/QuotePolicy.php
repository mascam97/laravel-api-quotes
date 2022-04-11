<?php

namespace Domain\Quotes\Policies;

use Domain\Quotes\Models\Quote;
use Domain\Users\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Log;

class QuotePolicy
{
    use HandlesAuthorization;

    public function pass(User $user, Quote $quote): bool
    {
        if ($user->id == $quote->user_id) {
            return true;
        }

        Log::channel('daily')->warning("User $user->id tried to delete or update the quote $quote->id");

        return false;
    }
}
