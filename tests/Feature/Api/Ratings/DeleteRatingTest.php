<?php

use Domain\Quotes\Factories\QuoteFactory;
use Domain\Rating\Models\Rating;
use Domain\Users\Models\User;
use Illuminate\Support\Facades\DB;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\deleteJson;

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

it('can delete', function () {
    loginApi($this->user);

    deleteJson(route('api.ratings.destroy', ['rating' => $this->rating->id]))
        ->assertOk()
        ->assertJson(['message' => 'The rating was deleted successfully']);

    assertDatabaseMissing(Rating::class, ['id' => $this->rating->id]);
});

it('cannot destroy data by not owner', function () {
    loginApi();

    deleteJson(route('api.ratings.destroy', ['rating' => $this->rating->getKey()]))
        ->assertForbidden();

    $this->assertDatabaseHas(Rating::class, [
        'id' => $this->rating->getKey(),
        'score' => 5,
    ]);
});

test('sql queries optimization test', function () {
    DB::enableQueryLog();
    deleteJson(route('api.ratings.destroy', ['rating' => $this->rating->id]))->assertOk();

    expect(formatQueries(DB::getQueryLog()))
        ->toHaveCount(5)
        ->sequence(
            fn ($query) => $query->toBe('select * from `ratings` where `id` = ? limit 1'),
            fn ($query) => $query->toBe('select * from `permissions`'), // TODO: Remove this query
            fn ($query) => $query->toBe('select * from `quotes` where `quotes`.`id` = ? limit 1'),
            fn ($query) => $query->toBe('delete from `ratings` where `id` = ?'),
            fn ($query) => $query->toBe('select avg(`score`) as aggregate from `users` inner join `ratings` on `users`.`id` = `ratings`.`qualifier_id` where `ratings`.`rateable_id` = ? and `ratings`.`rateable_type` = ? and `ratings`.`qualifier_type` = ? and `ratings`.`rateable_type` = ? and `users`.`deleted_at` is null'),
        );

    DB::disableQueryLog();
});
