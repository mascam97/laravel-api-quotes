<?php

use Domain\Quotes\Factories\QuoteFactory;
use Domain\Users\Models\User;
use function Pest\Laravel\getJson;

beforeEach(function () {
    $this->user = User::factory()->create();
    (new QuoteFactory)->withUser($this->user)->create();
});

it('cannot authorize guest', function () {
    getJson(route('api.users.index'))
        ->assertUnauthorized();

    getJson(route('api.me'))
        ->assertUnauthorized();

    getJson(route('api.users.show', ['user' => $this->user->id]))
        ->assertUnauthorized();
});

it('cannot show undefined data', function () {
    login($this->user);

    getJson(route('api.users.show', ['user' => 100000]))
        ->assertNotFound();
});
