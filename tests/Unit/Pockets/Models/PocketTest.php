<?php

use Domain\Pockets\Models\Pocket;
use Domain\Users\Models\User;

it('can get user', function () {
    /** @var Pocket $pocket */
    $pocket = Pocket::factory()->create();

    $user = User::factory()->create(['pocket_id' => $pocket->getKey()]);

    $user->pocket()->associate($pocket);
    $pocket->refresh();

    expect($pocket->user()->is($user))->toBeTrue();
});
