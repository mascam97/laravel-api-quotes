<?php

use Domain\Quotes\Factories\QuoteFactory;
use Domain\Quotes\Models\Quote;
use Domain\Rating\Models\Rating;
use Domain\Users\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Queue;

beforeEach(function () {
    Queue::fake();

    $this->user = User::factory()->create();
    $this->quote = (new QuoteFactory)->withUser($this->user)->create();  /* @phpstan-ignore-line */
});

it('can rate quotes by users', function () {
    $this->user->rate($this->quote, 5);

    expect($this->user->ratings(Quote::class)->get())
        ->toBeInstanceOf(Collection::class);
    expect($this->quote->qualifiers(User::class)->get())
        ->toBeInstanceOf(Collection::class);
});

it('can calculate average rating', function (array $scores, ?float $averageScore) {
    foreach ($scores as $score) {
        /** @var User $user */
        $user = User::factory()->create();

        $user->rate($this->quote, $score);
    }

    expect($this->quote->averageRating(User::class))
        ->toBe($averageScore);
})->with([
    [[], null],
    [[0], 0.0],
    [[0, 0], 0.0],
    [[4], 4.0],
    [[4, 0], 2.0],
    [[1, 4, 3], 2.7],
]);

it('can rate a model', function () {
    $this->user->rate($this->quote, 5);

    /** @var Rating $rating */
    $rating = Rating::query()->first();

    expect($rating->rateable)->toBeInstanceOf(Quote::class);
    expect($rating->qualifier)->toBeInstanceOf(User::class);
});
