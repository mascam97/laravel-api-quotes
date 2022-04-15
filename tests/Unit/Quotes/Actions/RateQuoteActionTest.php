<?php

namespace Quotes\Actions;

use Domain\Quotes\Actions\RateQuoteAction;
use Domain\Quotes\DTO\QuoteData;
use Domain\Quotes\Models\Quote;
use Domain\Users\Models\User;
use PHPUnit\Framework\TestCase;

class RateQuoteActionTest extends TestCase
{
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    /**
     * @throws \Support\Exceptions\InvalidScore
     */
    public function test_quote_is_created(): void
    {
        $quoteData = new QuoteData(score: 0);
        $rateQuoteAction = new RateQuoteAction();
        /** @var Quote $quote */
        $quote = Quote::factory()->create();

        $quoteRated = $rateQuoteAction->__invoke($quoteData, $quote, $this->user);

        $this->assertEquals($quote->getKey(), $quoteRated->getKey());
    }
}
