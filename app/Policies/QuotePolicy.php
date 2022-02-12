<?php

namespace App\Policies;

use App\Models\Quote;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Log;

class QuotePolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function pass(User $user, Quote $quote)
    {
        if ($user->id == $quote->user_id) {
            return true;
        }

        Log::channel('daily')->warning("User $user->id tried to delete or update the quote $quote->id");

        return false;
    }
}
