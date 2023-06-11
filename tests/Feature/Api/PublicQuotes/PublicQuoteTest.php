<?php

use Domain\Quotes\Factories\QuoteFactory;
use Domain\Users\Models\User;
use function Pest\Laravel\getJson;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->quote = (new QuoteFactory)->withUser($this->user)->create(); /* @phpstan-ignore-line */
});

it('cannot authorize guest', function () {
    getJson(route('api.public.quotes.index'))->assertUnauthorized();
    getJson(route('api.public.quotes.show', ['quote' => $this->quote->id]))->assertUnauthorized();
});

it('cannot process undefined data', function () {
    loginApi();

    getJson(route('api.public.quotes.show', ['quote' => 100000]))->assertNotFound();
});
