<?php

use Domain\Quotes\Factories\QuoteFactory;
use Domain\Rating\Models\Rating;
use Domain\Users\Models\User;
use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->quote = (new QuoteFactory)->withUser($this->user)->create(); /* @phpstan-ignore-line */

    $this->rating = new Rating();
    $this->rating->qualifier()->associate($this->user);
    $this->rating->rateable()->associate($this->quote); /* @phpstan-ignore-line */
    $this->rating->score = 5;
    $this->rating->save();
});

it('cannot authorize guest', function () {
    getJson(route('api.ratings.index'))->assertUnauthorized();
    getJson(route('api.ratings.show', ['rating' => $this->rating->id]))->assertUnauthorized();
    postJson(route('api.ratings.store'))->assertUnauthorized();
    putJson(route('api.ratings.update', ['rating' => $this->rating->id]))->assertUnauthorized();
    deleteJson(route('api.ratings.destroy', ['rating' => $this->rating->id]))->assertUnauthorized();
});

it('cannot show undefined data', function () {
    loginApi($this->user);

    getJson(route('api.ratings.show', ['rating' => 100000]))->assertNotFound();
    putJson(route('api.ratings.update', ['rating' => 100000]))->assertNotFound();
    deleteJson(route('api.ratings.destroy', ['rating' => 100000]))->assertNotFound();
});
