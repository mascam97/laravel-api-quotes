<?php

namespace Domain\Quotes\Policies;

use Domain\Quotes\Models\Quote;
use Domain\Users\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Log;

class QuotePolicy
{
    use HandlesAuthorization;

    public function update(User $user, Quote $quote): bool
    {
        if ($quote->user()->is($user)) {
            return true;
        }

        Log::channel('daily')->warning("User {$user->id} tried to update the quote {$quote->id}");

        return false;
    }

    public function delete(User $user, Quote $quote): bool
    {
        if ($quote->user()->is($user)) {
            return true;
        }

        Log::channel('daily')->warning("User {$user->id} tried to delete the quote {$quote->id}");

        return false;
    }
}
