<?php

use Domain\Pockets\Models\Pocket;
use Domain\Users\Models\User;
use Illuminate\Database\Eloquent\Collection;

// PHPUnit\Framework\TestCase is not used because the test use native function of Laravel

it('get quotes as collection', function () {
    $user = new User();

    expect($user->quotes)->toBeInstanceOf(Collection::class);
});

it('get pocket as single model', function () {
    $user = new User();
    $pocket = new Pocket();

    $user->pocket()->associate($pocket);

    expect($user->pocket)->toBeInstanceOf(Pocket::class);
});
