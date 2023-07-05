<?php

namespace App\Web\Users\Controllers;

use App\Controller;
use Domain\Users\Models\User;

class EmailUnsubscribeUsersController extends Controller
{
    public function __invoke(string $userId): string
    {
        $user = User::query()->findOrFail($userId);

        if ($user->email_subscribed_at === null) {
            return 'Email already unsubscribed.';
        }

        $user->email_subscribed_at = null;
        $user->save();

        return 'Email unsubscribed.';
    }
}
