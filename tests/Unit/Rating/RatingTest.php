<?php

use Domain\Quotes\Factories\QuoteFactory;
use Domain\Quotes\Models\Quote;
use Domain\Rating\Models\Rating;
use Domain\Users\Models\User;
use Illuminate\Database\Eloquent\Collection;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->quote = (new QuoteFactory)->withUser($this->user)->create();  /* @phpstan-ignore-line */
});

test('users rate quotes', function () {
    $this->user->rate($this->quote, 5);

    expect($this->user->ratings(Quote::class)->get())->toBeInstanceOf(Collection::class);
    expect($this->quote->qualifiers(User::class)->get())->toBeInstanceOf(Collection::class);
});

test('calculate average rating', function () {
    /** @var User $anotherUser */
    $anotherUser = User::factory()->create();

    $this->user->rate($this->quote, 5);
    $anotherUser->rate($this->quote, 3);

    expect($this->quote->averageRating(User::class))->toBe((float) 4.0);
});

test('rating model', function () {
    $this->user->rate($this->quote, 5);

    /** @var Rating $rating */
    $rating = Rating::query()->first();

    expect($rating->rateable)->toBeInstanceOf(Quote::class);
    expect($rating->qualifier)->toBeInstanceOf(User::class);
});
