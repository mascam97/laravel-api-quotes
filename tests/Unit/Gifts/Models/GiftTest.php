<?php

use Domain\Gifts\Models\Gift;
use Domain\Users\Models\User;

it('gets user as single model', function () {
    $gift = new Gift();
    $user = new User();

    $gift->user()->associate($user);

    expect($gift->user)->toBeInstanceOf(User::class);
});

it('gets sender user as single model', function () {
    $gift = new Gift();
    $senderUser = new User();

    $gift->senderUser()->associate($senderUser);

    expect($gift->senderUser)->toBeInstanceOf(User::class);
});
