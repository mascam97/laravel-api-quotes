<?php

use Domain\Quotes\Factories\QuoteFactory;
use Domain\Rating\Models\Rating;
use Domain\Users\Models\User;
use function Pest\Laravel\getJson;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->quote = (new QuoteFactory)->withUser($this->user)->create();  /* @phpstan-ignore-line */

    $this->rating = new Rating();
    $this->rating->qualifier()->associate($this->user);
    $this->rating->rateable()->associate($this->quote);
    $this->rating->score = 5;
    $this->rating->save();

    login($this->user);
});

it('can include qualifier', function () {
    $responseData = getJson(route('ratings.show', [
        'rating' => $this->rating->getKey(),
        'include' => 'qualifier',
    ]))->json('data');

    expect($responseData['qualifier'])
        ->id->toEqual($this->user->getKey())
        ->name->toEqual($this->user->name)
        ->email->toEqual($this->user->email)
        ->created_at->toEqual($this->user->created_at);
});

it('can include rateable', function () {
    $responseData = getJson(route('ratings.show', [
        'rating' => $this->rating->getKey(),
        'include' => 'rateable',
    ]))->json('data');

    expect($responseData['rateable'])
        ->id->toEqual($this->quote->getKey())
        ->title->toEqual($this->quote->title)
        ->excerpt->toEqual($this->quote->excerpt)
        ->content->toEqual($this->quote->content)
        ->state->toEqual($this->quote->state)
        ->average_rating->toEqual($this->quote->getAverageUserScore())
        ->created_at->toEqual($this->quote->created_at)
        ->updated_at->toEqual($this->quote->updated_at);
});
