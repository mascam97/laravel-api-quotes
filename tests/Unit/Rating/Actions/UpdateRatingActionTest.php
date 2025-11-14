<?php

use Domain\Quotes\Factories\QuoteFactory;
use Domain\Rating\Actions\UpdateRatingAction;
use Domain\Rating\Data\UpdateRatingData;
use Domain\Rating\Models\Rating;
use Domain\Users\Models\User;
use Illuminate\Support\Facades\DB;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertTrue;

beforeEach(function () {
    $this->user = User::factory()->create();

    $this->quote = (new QuoteFactory)->withUser($this->user)->create();

    $this->rating = new Rating();
    $this->rating->qualifier()->associate($this->user);
    $this->rating->rateable()->associate($this->quote); /* @phpstan-ignore-line */
    $this->rating->score = 5;
    $this->rating->save();
});

it('can update an existed rating', function () {
    $rateData = new UpdateRatingData(score: 1);

    $updatedRating = (new UpdateRatingAction())->__invoke($this->rating, $rateData);

    assertTrue($updatedRating->is($this->rating));
    assertEquals($updatedRating->created_at, $this->rating->created_at);
    assertTrue($updatedRating->qualifier()->is($this->user));
    assertTrue($updatedRating->rateable()->is($this->quote));
    assertEquals($updatedRating->score, 1);
});

test('sql queries optimization test', function () {
    $rateData = new UpdateRatingData(score: 1);

    DB::enableQueryLog();

    (new UpdateRatingAction())->__invoke($this->rating, $rateData);

    expect(formatQueries(DB::getQueryLog()))
        ->toHaveCount(3)
        ->sequence(
            fn ($query) => $query->toBe('update `ratings` set `score` = ?, `ratings`.`updated_at` = ? where `id` = ?'),
            fn ($query) => $query->toBe('select avg(`score`) as aggregate from `users` inner join `ratings` on `users`.`id` = `ratings`.`qualifier_id` where `ratings`.`rateable_id` = ? and `ratings`.`rateable_type` = ? and `ratings`.`qualifier_type` = ? and `ratings`.`rateable_type` = ? and `users`.`deleted_at` is null'),
            fn ($query) => $query->toBe('update `quotes` set `average_score` = ?, `quotes`.`updated_at` = ? where `id` = ?'),
        );

    DB::disableQueryLog();
});
