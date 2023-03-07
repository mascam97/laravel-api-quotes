<?php

use Domain\Quotes\Actions\UpdateQuoteAction;
use Domain\Quotes\Data\UpdateQuoteData;
use Domain\Quotes\Factories\QuoteFactory;
use Domain\Quotes\Models\Quote;
use Domain\Users\Models\User;
use Illuminate\Support\Facades\DB;

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

test('sql queries optimization test', function () {
    /** @var Quote $quote */
    $quote = (new QuoteFactory)->withUser(User::factory()->create())->create();
    DB::enableQueryLog();

    (new UpdateQuoteAction())->__invoke(new UpdateQuoteData(title: 'New Title', content: 'New Content'), $quote);

    expect(formatQueries(DB::getQueryLog()))
        ->toHaveCount(1)
        ->sequence(
            fn ($query) => $query->toContain('update `quotes` set `title` = ?, `content` = ?, '), // TODO: Validate with CI
        );

    DB::disableQueryLog();
});
