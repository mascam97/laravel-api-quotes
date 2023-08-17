<?php

namespace Domain\Users\Actions;

use Domain\Quotes\Models\Quote;
use Domain\Users\Models\User;
use Illuminate\Support\Facades\DB;

class DeleteUserAction
{
    public function __invoke(User $user): void
    {
        DB::transaction(function () use ($user) {
            $user->quotes()->delete();
            $user->ratings(Quote::class)->delete();

            $user->delete();
        });
    }
}
