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
    getJson(route('api.public.quotes.show', ['quote' => $this->quote->id]))
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

it('can include user', function () {
    getJson(route('api.public.quotes.show', ['quote' => $this->quote->getKey(), 'include' => 'user']))
        ->assertJson(function (AssertableJson $json) {
            $json->has('data.user', function (AssertableJson $data) {
                $data->where('id', $this->user->id)
                    ->has('name')
                    ->has('email')
                    ->has('created_at');
            })->etc();
        });
});

test('sql queries optimization test', function () {
    DB::enableQueryLog();
    getJson(route('api.public.quotes.show', ['quote' => $this->quote->getKey()]))->assertOk();

    expect(formatQueries(DB::getQueryLog()))
        ->toHaveCount(1)
        ->sequence(
            fn ($query) => $query->toBe('select `id`, `title`, `content`, `state`, `average_score`, `user_id`, `created_at`, `updated_at` from `quotes` where `state` = ? and `id` = ? limit 1'),
        );

    DB::disableQueryLog();
});
