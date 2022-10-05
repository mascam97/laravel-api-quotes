<?php

use Domain\Quotes\Factories\QuoteFactory;
use Domain\Users\Models\User;

test('view', function () {
    $user = User::factory()->create();
    $quotes = (new QuoteFactory)->setAmount(3)->withUser($user)->create();

    $this->get(route('welcome'))
        ->assertOk()
        ->assertSee($quotes->pluck('title')[0])
        ->assertSee($quotes->pluck('title')[1])
        ->assertSee($quotes->pluck('title')[2]);
});
