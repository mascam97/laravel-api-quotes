<?php

use Domain\Users\Actions\SendWelcomeEmailAction;
use Domain\Users\Mail\WelcomeEmail;
use Domain\Users\Models\User;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    Mail::fake();
});

it('will send a mail to user', function () {
    /** @var User $user */
    $user = User::factory()->create();

    (new SendWelcomeEmailAction())->execute($user);

    Mail::assertSent(function (WelcomeEmail $mail) use ($user) {
        return $mail->to[0]['address'] === $user->email;
    });
});
