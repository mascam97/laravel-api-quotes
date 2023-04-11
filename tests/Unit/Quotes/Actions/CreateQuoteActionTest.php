<?php

use Domain\Quotes\Actions\StoreQuoteAction;
use Domain\Quotes\Data\StoreQuoteData;
use Domain\Quotes\States\Drafted;
use Domain\Quotes\States\Published;
use Domain\Users\Models\User;
use Illuminate\Support\Facades\DB;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertTrue;

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('can create a quote', function () {
    $quoteData = new StoreQuoteData(
        title: 'Title', content: 'Content'
    );
    $quote = (new StoreQuoteAction())->__invoke($quoteData, $this->user);

    assertTrue($quote->user()->is($this->user));
    assertEquals($quote->title, $quoteData->title);
    assertEquals($quote->content, $quoteData->content);
    assertEquals(Drafted::$name, $quote->state);
});

it('can create a quote as published', function () {
    $quoteData = new StoreQuoteData(
        title: 'Title', content: 'Content', published: true
    );

    $quote = (new StoreQuoteAction())->__invoke($quoteData, $this->user);

    expect($quote) /* @phpstan-ignore-line */
        ->user->toEqual($this->user)
        ->title->toEqual($quoteData->title)
        ->content->toEqual($quoteData->content)
        ->state->toEqual(Published::$name);
});

test('sql queries optimization test', function () {
    DB::enableQueryLog();

    (new StoreQuoteAction())->__invoke(new StoreQuoteData(title: 'Title', content: 'Content'), $this->user);

    expect(formatQueries(DB::getQueryLog()))
        ->toHaveCount(1)
        ->sequence(
            fn ($query) => $query->toBe('insert into `quotes` (`state`, `title`, `content`, `average_score`, `user_id`, `updated_at`, `created_at`) values (?, ?, ?, ?, ?, ?, ?)'),
        );

    DB::disableQueryLog();
});
