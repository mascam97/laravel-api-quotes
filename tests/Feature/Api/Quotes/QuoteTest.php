<?php

use Domain\Quotes\Factories\QuoteFactory;
use Domain\Users\Models\User;
use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->quote = (new QuoteFactory)->withUser($this->user)->create(); /* @phpstan-ignore-line */
});

it('cannot authorize guest', function () {
    getJson(route('api.quotes.index'))->assertUnauthorized();
    getJson(route('api.quotes.show', ['quote' => $this->quote->id]))->assertUnauthorized();
    postJson(route('api.quotes.store'))->assertUnauthorized();
    putJson(route('api.quotes.update', ['quote' => $this->quote->id]))->assertUnauthorized();
    deleteJson(route('api.quotes.destroy', ['quote' => $this->quote->id]))->assertUnauthorized();
});

it('cannot process undefined data', function () {
    loginApi();

    getJson(route('api.quotes.show', ['quote' => 100000]))->assertNotFound();
    putJson(route('api.quotes.update', ['quote' => 100000]))->assertNotFound();
    deleteJson(route('api.quotes.destroy', ['quote' => 100000]))->assertNotFound();
});
