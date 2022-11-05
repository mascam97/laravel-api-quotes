<?php

use Domain\Quotes\Factories\QuoteFactory;
use Domain\Rating\Factories\RatingFactory;
use Domain\Users\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();

    $this->actingAs($this->user, 'sanctum');

    // TODO: Build a stronger test with a better factory and many ratings
    $this->quote = (new QuoteFactory)->withUser($this->user)->create();
    (new RatingFactory())->withUser($this->user)->withQuote($this->quote)->create(5); /* @phpstan-ignore-line */
});

it('can use qualifier_type filter', function () {
    $responseData = $this->json('GET', route('ratings.index', ['filter[qualifier_type]' => 'user']))
        ->json('data');

    $this->assertCount(1, $responseData);
    $this->assertEquals($this->user->getMorphClass(), $responseData[0]['qualifier_type']);
    $this->assertEquals($this->user->getKey(), $responseData[0]['qualifier_id']);
});

it('can use rateable_type filter', function () {
    $responseData = $this->json('GET', route('ratings.index', ['filter[rateable_type]' => 'quote']))
        ->json('data');

    $this->assertCount(1, $responseData);
    $this->assertEquals($this->quote->getMorphClass(), $responseData[0]['rateable_type']);
    $this->assertEquals($this->quote->getKey(), $responseData[0]['rateable_id']);
});

it('can use qualifier include', function () {
    $responseData = $this->json('GET', route('ratings.index', ['include' => 'qualifier']))
        ->json('data');

    $this->assertCount(1, $responseData);
    $this->assertArrayHasKey('qualifier', $responseData[0]);

    $this->assertEquals($this->user->getKey(), $responseData[0]['qualifier']['id']);
    $this->assertEquals($this->user->name, $responseData[0]['qualifier']['name']);
    $this->assertEquals($this->user->email, $responseData[0]['qualifier']['email']);
    $this->assertEquals($this->user->created_at, $responseData[0]['qualifier']['created_at']);
});

it('can use rateable include', function () {
    $responseData = $this->json('GET', route('ratings.index', ['include' => 'rateable']))
        ->json('data');

    $this->assertCount(1, $responseData);
    $this->assertArrayHasKey('rateable', $responseData[0]);

    $this->assertEquals($this->quote->getKey(), $responseData[0]['rateable']['id']);
    $this->assertEquals($this->quote->title, $responseData[0]['rateable']['title']);
    $this->assertEquals($this->quote->excerpt, $responseData[0]['rateable']['excerpt']);
    $this->assertEquals($this->quote->content, $responseData[0]['rateable']['content']);
    $this->assertEquals($this->quote->state, $responseData[0]['rateable']['state']);
    $this->assertEquals($this->quote->getAverageUserScore(), $responseData[0]['rateable']['average_rating']);
    $this->assertEquals($this->quote->created_at, $responseData[0]['rateable']['created_at']);
    $this->assertEquals($this->quote->updated_at, $responseData[0]['rateable']['updated_at']);
});

// TODO: Test sort parameters
