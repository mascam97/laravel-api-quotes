<?php

namespace Domain\Users\Actions;

use Domain\Quotes\Models\Quote;
use Domain\Users\Models\User;
use Illuminate\Support\Facades\DB;

class DeleteUserAction
{
    public function __invoke(User $user): void
    {
        // TODO: Delete pocket before user is deleted
        // TODO: And validate pocket is empty, without balance and without pending transactions

        // TODO: Add unit testing to test database transaction
        DB::transaction(function () use ($user) {
            // TODO: move to a DeleteQuotesAction and delete its ratings
            $user->quotes()->delete();
            $user->ratings(Quote::class)->delete();

            $user->delete();
        });
    }
}
