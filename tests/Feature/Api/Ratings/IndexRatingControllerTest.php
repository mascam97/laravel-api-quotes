<?php

use Domain\Quotes\Factories\QuoteFactory;
use Domain\Rating\Factories\RatingFactory;
use Domain\Users\Models\User;
use function Pest\Laravel\getJson;
use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertEquals;

beforeEach(function () {
    $this->user = User::factory()->create();

    login($this->user);

    // TODO: Build a stronger test with a better factory and many ratings
    $this->quote = (new QuoteFactory)->withUser($this->user)->create();
    (new RatingFactory())->withUser($this->user)->withQuote($this->quote)->create(5); /* @phpstan-ignore-line */
});

it('can filter by qualifier_type', function () {
    $responseData = getJson(route('ratings.index', ['filter[qualifier_type]' => 'user']))
        ->json('data');

    assertCount(1, $responseData);
    assertEquals($this->user->getMorphClass(), $responseData[0]['qualifier_type']);
    assertEquals($this->user->getKey(), $responseData[0]['qualifier_id']);
});

it('can filter by rateable_type', function () {
    $responseData = getJson(route('ratings.index', ['filter[rateable_type]' => 'quote']))
        ->json('data');

    assertCount(1, $responseData);
    assertEquals($this->quote->getMorphClass(), $responseData[0]['rateable_type']);
    assertEquals($this->quote->getKey(), $responseData[0]['rateable_id']);
});

it('can include qualifier', function () {
    $responseData = getJson(route('ratings.index', ['include' => 'qualifier']))
        ->json('data');

    assertCount(1, $responseData);

    expect($responseData[0]['qualifier'])
        ->id->toEqual($this->user->getKey())
        ->name->toEqual($this->user->name)
        ->email->toEqual($this->user->email)
        ->created_at->toEqual($this->user->created_at);
});

it('can include rateable', function () {
    $responseData = getJson(route('ratings.index', ['include' => 'rateable']))
        ->json('data');

    assertCount(1, $responseData);

    expect($responseData[0]['rateable'])
        ->id->toEqual($this->quote->getKey())
        ->title->toEqual($this->quote->title)
        ->excerpt->toEqual($this->quote->excerpt)
        ->content->toEqual($this->quote->content)
        ->state->toEqual($this->quote->state)
        ->average_rating->toEqual($this->quote->getAverageUserScore())
        ->created_at->toEqual($this->quote->created_at)
        ->updated_at->toEqual($this->quote->updated_at);
});

// TODO: Test sort parameters