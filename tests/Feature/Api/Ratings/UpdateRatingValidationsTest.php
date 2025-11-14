<?php

use Domain\Quotes\Factories\QuoteFactory;
use Domain\Rating\Models\Rating;
use Domain\Users\Models\User;
use Illuminate\Support\Facades\DB;
use function Pest\Laravel\putJson;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->quote = (new QuoteFactory)->withUser($this->user)->create();  /* @phpstan-ignore-line */

    $this->rating = new Rating();
    $this->rating->qualifier()->associate($this->user);
    $this->rating->rateable()->associate($this->quote); /* @phpstan-ignore-line */
    $this->rating->score = 5;
    $this->rating->save();

    loginApi($this->user);
});

it('can update', function () {
    putJson(route('api.ratings.update', ['rating' => $this->rating->id]), ['score' => 3])
        ->assertOk()
        ->assertJson(['message' => 'The rating was updated successfully']);
});

test('sql queries optimization test', function () {
    DB::enableQueryLog();
    putJson(route('api.ratings.update', ['rating' => $this->rating->id]), ['score' => 3])->assertOk();

    expect(formatQueries(DB::getQueryLog()))
        ->toHaveCount(6)
        ->sequence(
            fn ($query) => $query->toBe('select * from `ratings` where `id` = ? limit 1'),
            fn ($query) => $query->toBe('select * from `permissions`'), // TODO: Remove this query
            fn ($query) => $query->toBe('update `ratings` set `score` = ?, `ratings`.`updated_at` = ? where `id` = ?'),
            fn ($query) => $query->toBe('select * from `quotes` where `quotes`.`id` = ? limit 1'),
            fn ($query) => $query->toBe('select avg(`score`) as aggregate from `users` inner join `ratings` on `users`.`id` = `ratings`.`qualifier_id` where `ratings`.`rateable_id` = ? and `ratings`.`rateable_type` = ? and `ratings`.`qualifier_type` = ? and `ratings`.`rateable_type` = ? and `users`.`deleted_at` is null'),
            fn ($query) => $query->toBe('update `quotes` set `average_score` = ?, `quotes`.`updated_at` = ? where `id` = ?'),
        );

    DB::disableQueryLog();
});
