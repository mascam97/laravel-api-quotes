<?php

use Domain\Quotes\Factories\QuoteFactory;
use Domain\Quotes\States\Published;
use Domain\Users\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Testing\Fluent\AssertableJson;
use function Pest\Laravel\postJson;

beforeEach(function () {
    $this->user = User::factory()->create();

    (new QuoteFactory)->setAmount(3)->withUser($this->user)->withState(Published::$name)->create();

    loginApi($this->user);
});

it('can store', function () {
    postJson(route('api.quotes.store'), ['title' => 'Quote title', 'content' => 'Quote content'])
        ->assertCreated()
        ->assertJson(function (AssertableJson $json) {
            $json->has('data', function (AssertableJson $data) {
                $data->has('id')
                    ->where('title', 'Quote title')
                    ->where('content', 'Quote content')
                    ->has('average_rating')
                    ->has('state')
                    ->has('excerpt')
                    ->has('created_at')
                    ->has('updated_at');
            })->where('message', 'The quote was created successfully')
                ->etc();
        });
});

test('sql queries optimization test', function () {
    DB::enableQueryLog();
    postJson(route('api.quotes.store'), ['title' => 'Quote title', 'content' => 'Quote content'])->assertCreated();

    expect(formatQueries(DB::getQueryLog()))
        ->toHaveCount(1)
        ->sequence(
            fn ($query) => $query->toBe('insert into `quotes` (`state`, `title`, `content`, `average_score`, `user_id`, `updated_at`, `created_at`) values (?, ?, ?, ?, ?, ?, ?)'),
        );

    DB::disableQueryLog();
});
