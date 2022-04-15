<?php

namespace Quotes\Actions;

use Domain\Quotes\Actions\UpdateQuoteAction;
use Domain\Quotes\DTO\QuoteData;
use Domain\Quotes\Models\Quote;
use PHPUnit\Framework\TestCase;

class UpdateQuoteActionTest extends TestCase
{
    public function test_quote_is_created(): void
    {
        $quoteData = new QuoteData(
            title: 'new title', content: 'new title'
        );
        $updateQuoteAction = new UpdateQuoteAction();
        /** @var Quote $quote */
        $quote = Quote::factory()->create([
            'title' => 'old title',
            'content' => 'old content',
        ]);

        $quoteUpdated = $updateQuoteAction->__invoke($quoteData, $quote);

        $this->assertEquals($quote->getKey(), $quoteUpdated->getKey());
        $this->assertEquals($quote->title, $quoteUpdated->title);
        $this->assertEquals($quote->content, $quoteUpdated->content);
    }
}
