<?php

namespace Tests\Unit\Users\Models;

use Domain\Users\Models\User;
use Illuminate\Database\Eloquent\Collection;

// PHPUnit\Framework\TestCase is not used because the test use native function of Laravel

test('get quotes as collection', function () {
    $user = new User();

    expect($user->quotes)->toBeInstanceOf(Collection::class);
});
