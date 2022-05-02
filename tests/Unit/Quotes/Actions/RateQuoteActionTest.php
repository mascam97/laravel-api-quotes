<?php

namespace Tests\Unit\Quotes\Actions;

use Domain\Quotes\Actions\RateQuoteAction;
use Domain\Quotes\DTO\QuoteData;
use Domain\Quotes\Factories\QuoteFactory;
use Domain\Quotes\Models\Quote;
use Domain\Users\Factories\UserFactory;
use Domain\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RateQuoteActionTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = (new UserFactory)->create();
    }

    /**
     * @throws \Support\Exceptions\InvalidScore
     */
    public function test_quote_is_created(): void
    {
        $quoteData = new QuoteData(score: 0);
        /** @var Quote $quote */
        $quote = (new QuoteFactory)->withUser((new UserFactory)->create())->create();

        $quoteRated = (new RateQuoteAction)->__invoke($quoteData, $quote, $this->user);

        $this->assertEquals($quote->getKey(), $quoteRated->getKey());
    }
}
