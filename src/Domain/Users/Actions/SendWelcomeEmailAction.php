<?php

namespace Domain\Users\Actions;

use Domain\Users\Mail\WelcomeEmail;
use Domain\Users\Models\User;
use Illuminate\Support\Facades\Mail;
use Spatie\QueueableAction\QueueableAction;

class SendWelcomeEmailAction
{
    use QueueableAction;

    public function execute(User $user): void
    {
        $email = new WelcomeEmail();

        Mail::to($user->email)->send($email);
    }
}
