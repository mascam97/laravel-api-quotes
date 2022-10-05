<?php

use Domain\Quotes\Actions\RateQuoteAction;
use Domain\Quotes\DTO\RateQuoteData;
use Domain\Quotes\Factories\QuoteFactory;
use Domain\Quotes\Models\Quote;
use Domain\Users\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('quote is rated', function () {
    /** @var Quote $quote */
    $quote = (new QuoteFactory)->withUser(User::factory()->create())->create();

    $rateQuoteData = new RateQuoteData(score: 1);
    $quoteRated = (new RateQuoteAction)->__invoke($rateQuoteData, $quote, $this->user);

    $this->assertEquals($quote->getKey(), $quoteRated->getKey());
});
