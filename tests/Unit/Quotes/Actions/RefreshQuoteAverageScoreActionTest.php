<?php

use Domain\Quotes\Actions\RefreshQuoteAverageScoreAction;
use Domain\Quotes\Factories\QuoteFactory;
use Domain\Quotes\Models\Quote;
use Domain\Rating\Models\Rating;
use Domain\Users\Models\User;
use Illuminate\Support\Facades\DB;
use function PHPUnit\Framework\assertEquals;

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('can refresh quote average score', function () {
    /** @var Quote $quote */
    $quote = (new QuoteFactory)->withUser($this->user)->create();

    (new RefreshQuoteAverageScoreAction())->__invoke($quote);
    $quote->refresh();

    assertEquals(0, $quote->average_score);

    $rating = new Rating();
    $rating->qualifier()->associate($this->user);
    $rating->rateable()->associate($quote);
    $rating->score = 4;
    $rating->save();

    $quote->refresh();
    assertEquals(0, $quote->average_score);

    (new RefreshQuoteAverageScoreAction())->__invoke($quote);
    $quote->refresh();

    assertEquals(4, $quote->average_score);
});

test('sql queries optimization test', function () {
    /** @var Quote $quote */
    $quote = (new QuoteFactory)->withUser($this->user)->create();
    $rating = new Rating();
    $rating->qualifier()->associate($this->user);
    $rating->rateable()->associate($quote);
    $rating->score = 4;
    $rating->save();

    DB::enableQueryLog();
    (new RefreshQuoteAverageScoreAction())->__invoke($quote);

    expect(formatQueries(DB::getQueryLog()))
        ->toHaveCount(2)
        ->sequence(
            fn ($query) => $query->toBe('select avg(`score`) as aggregate from `users` inner join `ratings` on `users`.`id` = `ratings`.`qualifier_id` where `ratings`.`rateable_id` = ? and `ratings`.`rateable_type` = ? and `ratings`.`qualifier_type` = ? and `ratings`.`rateable_type` = ?'),
            fn ($query) => $query->toContain('update `quotes` set `average_score` = ?, '),
        );

    DB::disableQueryLog();
});
