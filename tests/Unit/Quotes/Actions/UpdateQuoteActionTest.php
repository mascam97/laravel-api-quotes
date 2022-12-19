<?php

use Domain\Quotes\Actions\UpdateQuoteAction;
use Domain\Quotes\Data\UpdateQuoteData;
use Domain\Quotes\Factories\QuoteFactory;
use Domain\Quotes\Models\Quote;
use Domain\Users\Models\User;

it('can update a quote', function () {
    $quoteData = new UpdateQuoteData(
        title: 'new title', content: 'new title'
    );
    /** @var Quote $quote */
    $quote = (new QuoteFactory)->withUser(User::factory()->create())->create([
        'title' => 'old title',
        'content' => 'old content',
    ]);

    $quoteUpdated = (new UpdateQuoteAction())->__invoke($quoteData, $quote);

    expect($quoteUpdated)
        ->getKey()->toEqual($quoteUpdated->getKey())
        ->title->toEqual($quoteUpdated->title)
        ->content->toEqual($quoteUpdated->content);
});
