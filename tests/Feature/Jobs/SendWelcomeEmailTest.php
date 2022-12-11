<?php

use App\Jobs\Users\SendWelcomeEmail;
use Domain\Users\Mail\WelcomeEmail;
use Domain\Users\Models\User;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    Mail::fake();
});

it('will send a mail to user', function () {
    /** @var User $user */
    $user = User::factory()->create();

    (new SendWelcomeEmail($user->email))->handle();

    Mail::assertSent(function (WelcomeEmail $mail) use ($user) {
        return $mail->to[0]['address'] === $user->email;
    });
});
