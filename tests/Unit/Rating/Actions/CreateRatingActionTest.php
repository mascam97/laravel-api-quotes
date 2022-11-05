<?php

use Domain\Quotes\Factories\QuoteFactory;
use Domain\Quotes\Models\Quote;
use Domain\Rating\Actions\UpdateOrCreateRatingAction;
use Domain\Rating\DTO\RatingData;
use Domain\Rating\Models\Rating;
use Domain\Users\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('quote is created', function () {
    /** @var Quote $quote */
    $quote = (new QuoteFactory)->withUser($this->user)->create();
    $rateData = new RatingData(
        score: 5
    );

    $rating = (new UpdateOrCreateRatingAction())->__invoke($this->user, $quote, $rateData);

    $this->assertTrue($rating->qualifier()->is($this->user));
    $this->assertTrue($rating->rateable()->is($quote));
    $this->assertEquals($rating->score, 5);
});

test('quote is updated if it exists', function () {
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

    $this->assertTrue($updatedRating->is($rating));
    $this->assertEquals($updatedRating->created_at, $rating->created_at);
    $this->assertTrue($updatedRating->qualifier()->is($this->user));
    $this->assertTrue($updatedRating->rateable()->is($quote));
    $this->assertEquals($updatedRating->score, 1);
});
