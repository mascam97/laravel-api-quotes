<?php

namespace Tests\Unit\Quotes\Actions;

use Domain\Quotes\Actions\CreateQuoteAction;
use Domain\Quotes\DTO\QuoteData;
use Domain\Quotes\States\Drafted;
use Domain\Quotes\States\Published;
use Domain\Users\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('quote is created', function () {
    $quoteData = new QuoteData(
        title: 'Title', content: 'Content'
    );
    $createQuoteAction = new CreateQuoteAction();

    $quote = $createQuoteAction->__invoke($quoteData, $this->user);

    $this->assertTrue($quote->user()->is($this->user));
    $this->assertEquals($quote->title, $quoteData->title);
    $this->assertEquals($quote->content, $quoteData->content);
    $this->assertEquals(Drafted::class, $quote->state);
});

test('quote can be published', function () {
    $quoteData = new QuoteData(
        title: 'Title', content: 'Content', published: true
    );

    $createQuoteAction = new CreateQuoteAction();

    $quote = $createQuoteAction->__invoke($quoteData, $this->user);

    $this->assertTrue($quote->user()->is($this->user));
    $this->assertEquals($quote->title, $quoteData->title);
    $this->assertEquals($quote->content, $quoteData->content);
    $this->assertEquals(Published::class, $quote->state);
});
