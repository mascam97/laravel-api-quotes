<?php

use Domain\Quotes\Factories\QuoteFactory;
use Domain\Quotes\Models\Quote;
use Domain\Quotes\States\Published;
use Domain\Users\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Testing\Fluent\AssertableJson;
use function Pest\Laravel\getJson;
use function PHPUnit\Framework\assertLessThan;

/**
 * Test chosen to test all query params as page[size], page[number] and metadata
 */
beforeEach(function () {
    config()->set('app.url', 'http://localhost');

    $this->user = User::factory()->create();

    (new QuoteFactory)->setAmount(5)->withUser($this->user)->withState(Published::$name)->create();

    loginApi($this->user);
});

it('can index', function () {
    getJson(route('api.public.quotes.index'))
        ->assertOk()
        ->assertJson(function (AssertableJson $json) {
            $json->has('data', 5)
                ->has('data.0', function (AssertableJson $data) {
                    $data->whereAllType([
                        'id' => 'integer',
                        'title' => 'string',
                        'content' => 'string',
                        'state' => 'string',
                        'average_rating' => 'null',
                        'excerpt' => 'string',
                        'created_at' => 'string',
                        'updated_at' => 'string',
                    ])->etc();
                })
                ->has('links', function (AssertableJson $links) {
                    $links->where('first', 'http://localhost/api/public/quotes?page%5Bnumber%5D=1')
                        ->where('last', 'http://localhost/api/public/quotes?page%5Bnumber%5D=1')
                        ->where('prev', null)
                        ->where('next', null);
                })
                ->has('meta', function (AssertableJson $meta) {
                    $meta->where('current_page', 1)
                        ->where('from', 1)
                        ->where('last_page', 1)
                        ->where('current_sort.0.column', 'created_at')
                        ->where('current_sort.0.direction', 'asc')
                        ->has('links')
                        ->where('path', 'http://localhost/api/public/quotes')
                        ->where('per_page', 20)
                        ->where('to', 5)
                        ->where('total', 5);
                });
        });
});

it('can filter by title', function () {
    /** @var Quote $quote */
    $quote = (new QuoteFactory)
        ->withUser($this->user)
        ->withState(Published::$name)
        ->create(['title' => 'Hamlet']);

    getJson(route('api.public.quotes.index', ['filter[title]' => 'hamlet']))
        ->assertJson(function (AssertableJson $json) use ($quote) {
            $json->has('data', 1)
                ->has('data.0', function (AssertableJson $data) use ($quote) {
                    $data->where('id', $quote->getKey())
                        ->where('title', 'Hamlet')
                        ->whereAllType([
                            'id' => 'integer',
                            'title' => 'string',
                        ])
                        ->etc();
                })->where('links.first', 'http://localhost/api/public/quotes?filter%5Btitle%5D=hamlet&page%5Bnumber%5D=1')->etc();
        });
});

it('can filter by content', function () {
    /** @var Quote $quote */
    $quote = (new QuoteFactory)
        ->withUser($this->user)
        ->withState(Published::$name)
        ->create(['content' => 'Some text about something']);

    getJson(route('api.public.quotes.index', ['filter[content]' => 'Some text about something']))
        ->assertSuccessful()
        ->assertJson(function (AssertableJson $json) use ($quote) {
            $json->has('data', 1)
                ->has('data.0', function (AssertableJson $data) use ($quote) {
                    $data->where('id', $quote->getKey())
                        ->where('content', 'Some text about something')
                        ->whereAllType([
                            'id' => 'integer',
                            'content' => 'string',
                        ])
                        ->etc();
                })->etc();
        });
});

it('can filter by user id', function () {
    /** @var User $newUser */
    $newUser = User::factory()->create();

    /** @var Quote $quote */
    $quote = (new QuoteFactory)->withUser($newUser)->withState(Published::$name)->create();

    getJson(route('api.public.quotes.index', ['filter[user_id]' => $newUser->id]))
        ->assertJson(function (AssertableJson $json) use ($quote) {
            $json->has('data', 1)
                ->has('data.0', function (AssertableJson $data) use ($quote) {
                    $data->where('id', $quote->getKey())
                        ->whereAllType(['id' => 'integer'])
                        ->etc();
                })->etc();
        });
});

it('can include user', function () {
    getJson(route('api.public.quotes.index', ['include' => 'user']))
        ->assertOk()
        ->assertJson(function (AssertableJson $json) {
            $json->has('data', 5)
                ->has('data.0', function (AssertableJson $data) {
                    $data->has('user')
                        ->whereAllType([
                            'user' => 'array',
                        ])->etc();
                })->where('links.first', 'http://localhost/api/public/quotes?include=user&page%5Bnumber%5D=1')->etc();
        });

    $newUser = User::factory()->create();

    /** @var Quote $quote */
    $quote = (new QuoteFactory)->withUser($newUser)->withState(Published::$name)->create([
        'title' => 'Some text about something',
    ]);

    getJson(route('api.public.quotes.index', [
        'filter[title]' => 'Some text about something',
        'include' => 'user',
    ]))->assertJson(function (AssertableJson $json) use ($quote, $newUser) {
        $json->has('data', 1)
            ->has('data.0', function (AssertableJson $data) use ($quote, $newUser) {
                $data->has('user')
                    ->where('id', $quote->getKey())
                    ->where('user.id', $newUser->getKey())
                    ->whereAllType([
                        'user' => 'array',
                    ])->etc();
            })->etc();
    });
});

it('can sort by id', function () {
    $responseData = getJson(route('api.public.quotes.index', ['sort' => 'id']))
        ->json('data');

    assertLessThan($responseData[4]['id'], $responseData[0]['id']);

    $responseDataTwo = getJson(route('api.public.quotes.index', ['sort' => '-id']))
        ->json('data');

    assertLessThan($responseDataTwo[0]['id'], $responseDataTwo[4]['id']);

    getJson(route('api.public.quotes.index', ['sort' => 'id,title']))
        ->assertJson(function (AssertableJson $json) {
            $json->where('links.first', 'http://localhost/api/public/quotes?sort=id%2Ctitle&page%5Bnumber%5D=1')
                ->where('meta.current_sort.0.column', 'id')
                ->where('meta.current_sort.1.direction', 'asc')
                ->etc();
        });
});

it('can sort by two columns', function () {
    DB::enableQueryLog();

    getJson(route('api.public.quotes.index', ['sort' => 'title,created_at']))
        ->assertSuccessful()
        ->assertJson(function (AssertableJson $json) {
            $json->where('links.first', 'http://localhost/api/public/quotes?sort=title%2Ccreated_at&page%5Bnumber%5D=1')
                ->where('meta.current_sort.0.column', 'title')
                ->where('meta.current_sort.0.direction', 'asc')
                ->where('meta.current_sort.1.column', 'created_at')
                ->where('meta.current_sort.1.direction', 'asc')
                ->etc();
        });

    expect(formatQueries(DB::getQueryLog()))
        ->toHaveCount(2)
        ->sequence(
            fn ($query) => $query->toBe('select count(*) as aggregate from `quotes` where `state` = ?'),
            fn ($query) => $query->toBe('select `id`, `title`, `content`, `state`, `average_score`, `user_id`, `created_at`, `updated_at` from `quotes` where `state` = ? order by `title` asc, `created_at` asc limit 20 offset 0'),
        );

    DB::disableQueryLog();
});

it('can get by page size', function () {
    (new QuoteFactory)->setAmount(5)->withUser($this->user)->withState(Published::$name)->create();

    getJson(route('api.public.quotes.index', ['page[size]' => 5]))
        ->assertOk()
        ->assertJson(function (AssertableJson $json) {
            $json->has('data', 5)->etc();
        });

    getJson(route('api.public.quotes.index', ['page[size]' => 10]))
        ->assertOk()
        ->assertJson(function (AssertableJson $json) {
            $json->has('data', 10)->etc();
        });
});

it('can get by page number', function () {
    (new QuoteFactory)->setAmount(18)->withUser($this->user)->withState(Published::$name)->create();

    getJson(route('api.public.quotes.index', ['page[number]' => 1]))
        ->assertOk()
        ->assertJson(function (AssertableJson $json) {
            $json->has('data', 20)->etc();
        });

    getJson(route('api.public.quotes.index', ['page[number]' => 2]))
        ->assertOk()
        ->assertJson(function (AssertableJson $json) {
            $json->has('data', 3)->etc();
        });

    getJson(route('api.public.quotes.index', ['page' => 2])) // page is not valid param anymore
        ->assertOk()
        ->assertJson(function (AssertableJson $json) {
            $json->has('data', 20)->etc();
        });
});

test('sql queries optimization test', function () {
    DB::enableQueryLog();
    getJson(route('api.public.quotes.index'))->assertOk();

    expect(formatQueries(DB::getQueryLog()))
        ->toHaveCount(2)
        ->sequence(
            fn ($query) => $query->toBe('select count(*) as aggregate from `quotes` where `state` = ?'),
            fn ($query) => $query->toBe('select `id`, `title`, `content`, `state`, `average_score`, `user_id`, `created_at`, `updated_at` from `quotes` where `state` = ? order by `created_at` asc limit 20 offset 0'),
        );

    DB::disableQueryLog();
});
