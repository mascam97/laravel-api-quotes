<?php

use Domain\Quotes\Factories\QuoteFactory;
use Domain\Quotes\States\Published;
use Domain\Users\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Testing\Fluent\AssertableJson;
use function Pest\Laravel\getJson;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->quote = (new QuoteFactory)->withUser($this->user)->withState(Published::$name)->create();

    (new QuoteFactory)->setAmount(3)->withUser($this->user)->withState(Published::$name)->create();

    loginApi($this->user);
});

it('can show', function () {
    getJson(route('api.quotes.show', ['quote' => $this->quote->id]))
        ->assertOk()
        ->assertJson(function (AssertableJson $json) {
            $json->has('data', function (AssertableJson $data) {
                $data->where('id', $this->quote->id)
                    ->has('title')
                    ->has('content')
                    ->has('average_rating')
                    ->has('state')
                    ->has('excerpt')
                    ->has('created_at')
                    ->has('updated_at');
            })->etc();
        });
});

test('sql queries optimization test', function () {
    DB::enableQueryLog();
    getJson(route('api.quotes.show', ['quote' => $this->quote->getKey()]))->assertOk();

    expect(formatQueries(DB::getQueryLog()))
        ->toHaveCount(2)
        ->sequence(
            fn ($query) => $query->toBe('select `id`, `title`, `content`, `state`, `average_score`, `user_id`, `created_at`, `updated_at` from `quotes` where `id` = ? limit 1'),
            fn ($query) => $query->toBe('select * from `permissions`'), // TODO: Remove this query
        );

    DB::disableQueryLog();
});
