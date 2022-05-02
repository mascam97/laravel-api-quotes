<?php

namespace Tests\Unit\Quotes\Actions;

use Domain\Quotes\Actions\UpdateQuoteAction;
use Domain\Quotes\DTO\QuoteData;
use Domain\Quotes\Factories\QuoteFactory;
use Domain\Quotes\Models\Quote;
use Domain\Users\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UpdateQuoteActionTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_quote_is_created(): void
    {
        $quoteData = new QuoteData(
            title: 'new title', content: 'new title'
        );
        $updateQuoteAction = new UpdateQuoteAction();
        /** @var Quote $quote */
        $quote = (new QuoteFactory)->withUser((new UserFactory)->create())->create([
            'title' => 'old title',
            'content' => 'old content',
        ]);

        $quoteUpdated = $updateQuoteAction->__invoke($quoteData, $quote);

        $this->assertEquals($quote->getKey(), $quoteUpdated->getKey());
        $this->assertEquals($quote->title, $quoteUpdated->title);
        $this->assertEquals($quote->content, $quoteUpdated->content);
    }
}
