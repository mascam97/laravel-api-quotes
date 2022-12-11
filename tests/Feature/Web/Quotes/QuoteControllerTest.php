<?php

use Domain\Quotes\Factories\QuoteFactory;
use Domain\Users\Models\User;
use function Pest\Laravel\get;

it('can see the main view', function () {
    $user = User::factory()->create();
    $quotes = (new QuoteFactory)->setAmount(3)->withUser($user)->create();

    get(route('welcome'))
        ->assertOk()
        ->assertSee($quotes->pluck('title')[0])
        ->assertSee($quotes->pluck('title')[1])
        ->assertSee($quotes->pluck('title')[2]);
});
