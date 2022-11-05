<?php

use Domain\Quotes\Factories\QuoteFactory;
use Domain\Rating\Models\Rating;
use Domain\Users\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->quote = (new QuoteFactory)->withUser($this->user)->create();  /* @phpstan-ignore-line */

    $this->rating = new Rating();
    $this->rating->qualifier()->associate($this->user);
    $this->rating->rateable()->associate($this->quote);
    $this->rating->score = 5;
    $this->rating->save();

    $this->actingAs($this->user, 'sanctum');
});

it('can use qualifier include', function () {
    $responseData = $this->json('GET', route('ratings.show', [
        'rating' => $this->rating->getKey(),
        'include' => 'qualifier',
    ]))->json('data');

    $this->assertArrayHasKey('qualifier', $responseData);
    $this->assertEquals($this->user->getKey(), $responseData['qualifier']['id']);
    $this->assertEquals($this->user->name, $responseData['qualifier']['name']);
    $this->assertEquals($this->user->email, $responseData['qualifier']['email']);
    $this->assertEquals($this->user->created_at, $responseData['qualifier']['created_at']);
});

it('can use rateable include', function () {
    $responseData = $this->json('GET', route('ratings.show', [
        'rating' => $this->rating->getKey(),
        'include' => 'rateable',
    ]))->json('data');

    $this->assertArrayHasKey('rateable', $responseData);
    $this->assertEquals($this->quote->getKey(), $responseData['rateable']['id']);
    $this->assertEquals($this->quote->title, $responseData['rateable']['title']);
    $this->assertEquals($this->quote->excerpt, $responseData['rateable']['excerpt']);
    $this->assertEquals($this->quote->content, $responseData['rateable']['content']);
    $this->assertEquals($this->quote->state, $responseData['rateable']['state']);
    $this->assertEquals($this->quote->getAverageUserScore(), $responseData['rateable']['average_rating']);
    $this->assertEquals($this->quote->created_at, $responseData['rateable']['created_at']);
    $this->assertEquals($this->quote->updated_at, $responseData['rateable']['updated_at']);
});
