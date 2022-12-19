<?php

use Domain\Quotes\Factories\QuoteFactory;
use Domain\Quotes\Models\Quote;
use Domain\Rating\Actions\UpdateOrCreateRatingAction;
use Domain\Rating\Data\RatingData;
use Domain\Rating\Models\Rating;
use Domain\Users\Models\User;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertTrue;

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('can create a rating to a quote', function () {
    /** @var Quote $quote */
    $quote = (new QuoteFactory)->withUser($this->user)->create();
    $rateData = new RatingData(
        score: 5
    );

    $rating = (new UpdateOrCreateRatingAction())->__invoke($this->user, $quote, $rateData);

    assertTrue($rating->qualifier()->is($this->user));
    assertTrue($rating->rateable()->is($quote));
    assertEquals($rating->score, 5);
});

it('can update an existed quote', function () {
    $quote = (new QuoteFactory)->withUser($this->user)->create();

    $rating = new Rating();
    $rating->qualifier()->associate($this->user);
    $rating->rateable()->associate($quote);
    $rating->score = 5;
    $rating->save();

    /** @var Quote $quote */
    $rateData = new RatingData(
        score: 1
    );

    $updatedRating = (new UpdateOrCreateRatingAction())->__invoke($this->user, $quote, $rateData);

    assertTrue($updatedRating->is($rating));
    assertEquals($updatedRating->created_at, $rating->created_at);
    assertTrue($updatedRating->qualifier()->is($this->user));
    assertTrue($updatedRating->rateable()->is($quote));
    assertEquals($updatedRating->score, 1);
});
