<?php

use Domain\Quotes\Factories\QuoteFactory;
use Domain\Users\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Testing\Fluent\AssertableJson;
use function Pest\Laravel\postJson;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->quote = (new QuoteFactory)->withUser($this->user)->create();  /* @phpstan-ignore-line */

    loginApi($this->user);
});

it('can store', function () {
    postJson(route('api.ratings.store'), [
        'score' => 4,
        'rateableId' => $this->quote->getKey(),
        'rateableType' => $this->quote->getMorphClass(),
    ])->assertCreated()
        ->assertJson(function (AssertableJson $json) {
            $json->has('data', function (AssertableJson $data) {
                $data->has('id')
                    ->where('qualifier_id', $this->user->getKey())
                    ->where('qualifier_type', $this->user->getMorphClass())
                    ->where('rateable_id', $this->quote->getKey())
                    ->where('rateable_type', $this->quote->getMorphClass())
                    ->where('score', 4)
                    ->has('qualifier')
                    ->has('rateable')
                    ->has('created_at')
                    ->has('updated_at');
            })->where('message', 'The rating was created successfully')
                ->etc();
        });
});

test('sql queries optimization test', function () {
    DB::enableQueryLog();

    postJson(route('api.ratings.store'), [
        'score' => 6,
        'rateableId' => $this->quote->getKey(),
        'rateableType' => $this->quote->getMorphClass(),
    ])->assertCreated();

    expect(formatQueries(DB::getQueryLog()))
        ->toHaveCount(5)
        ->sequence(
            fn ($query) => $query->toBe('select * from `quotes` where `id` = ? limit 1'),
            fn ($query) => $query->toBe('insert into `ratings` (`qualifier_id`, `qualifier_type`, `rateable_id`, `rateable_type`, `score`, `updated_at`, `created_at`) values (?, ?, ?, ?, ?, ?, ?)'),
            fn ($query) => $query->toBe('select avg(`score`) as aggregate from `users` inner join `ratings` on `users`.`id` = `ratings`.`qualifier_id` where `ratings`.`rateable_id` = ? and `ratings`.`rateable_type` = ? and `ratings`.`qualifier_type` = ? and `ratings`.`rateable_type` = ? and `users`.`deleted_at` is null'),
            fn ($query) => $query->toBe('update `quotes` set `average_score` = ?, `quotes`.`updated_at` = ? where `id` = ?'),
            fn ($query) => $query->toContain('select * from `quotes` where `quotes`.`id` in '),
        );

    DB::disableQueryLog();
});
