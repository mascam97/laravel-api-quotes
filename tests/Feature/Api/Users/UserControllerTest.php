<?php

use Domain\Quotes\Factories\QuoteFactory;
use Domain\Users\Models\User;
use function Pest\Laravel\getJson;

beforeEach(function () {
    $this->user = User::factory()->create();
    (new QuoteFactory)->withUser($this->user)->create();
});

it('cannot authorize guest', function () {
    getJson(route('users.index'))
        ->assertUnauthorized();

    getJson(route('users.show', ['user' => $this->user->id]))
        ->assertUnauthorized();
});

it('cannot show undefined data', function () {
    login($this->user);

    getJson(route('users.show', ['user' => 100000]))
        ->assertNotFound();
});
