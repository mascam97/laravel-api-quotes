<?php

use Domain\Quotes\Factories\QuoteFactory;
use Domain\Quotes\Models\Quote;
use Domain\Users\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Testing\Fluent\AssertableJson;
use function Pest\Laravel\postJson;

beforeEach(function () {
    $this->user = User::factory()->create();

    (new QuoteFactory())->withUser($this->user)->setAmount(5)->create();

    loginApiAnalytics($this->user);
});

it('can index', function () {
    postJson(route('graphql'), [
        'query' => '{
            quotes {
                id
                title
                content
                state
                user_id
                created_at
                updated_at
            }
        }',
    ])->assertOk()
        ->assertJson(function (AssertableJson $json) {
            $json->has('data.quotes', 5, fn ($json) => $json
                ->has('id')
                ->has('title')
                ->has('content')
                ->has('state')
                ->has('user_id')
                ->has('created_at')
                ->has('updated_at')
            )->etc();
        });
});

it('can get by id', function () {
    /** @var Quote $quote */
    $quote = (new QuoteFactory())->withUser($this->user)->create();

    postJson(route('graphql'), [
        'query' => "{
            quotes(id: {$quote->getKey()} ) {
                id
                title
                state
                created_at
            }
        }",
    ])->assertOk()
        ->assertJson(function (AssertableJson $json) use ($quote) {
            $json->has('data.quotes', 1, fn ($json) => $json
                ->where('id', $quote->getKey())
                ->where('title', $quote->title)
                ->where('state', $quote->state->getValue())
                ->has('created_at')
            )->etc();
        });
});

it('can include user', function () {
    $user = User::factory()->create();
    /** @var Quote $quote */
    $quote = (new QuoteFactory())->withUser($user)->create();

    postJson(route('graphql'), [
        'query' => "{
            quotes(id: {$quote->getKey()} ) {
                id
                title
                user {
                    id
                    name
                }
                created_at
            }
        }",
    ])->assertOk()
        ->assertJson(function (AssertableJson $json) use ($quote, $user) {
            $json->has('data.quotes', 1, fn ($json) => $json
                ->where('id', $quote->getKey())
                ->where('title', $quote->title)
                ->has('user')
                ->where('user.id', $user->getKey())
                ->where('user.name', $user->name)
                ->has('created_at')
            )->etc();
        });
});

test('sql queries optimization test', function () {
    DB::enableQueryLog();

    postJson(route('graphql'), [
        'query' => '{
            quotes {
                id
                title
                user {
                    id
                    name
                }
            }
        }',
    ])->assertOk();

    expect(formatQueries(DB::getQueryLog()))
        ->toHaveCount(2)
        ->sequence(
            fn ($query) => $query->toBe('select `quotes`.`id`, `quotes`.`title`, `quotes`.`user_id` from `quotes`'),
            fn ($query) => $query->toContain('select `users`.`id`, `users`.`name` from `users` where `users`.`id` in'),
        );

    DB::disableQueryLog();
});
