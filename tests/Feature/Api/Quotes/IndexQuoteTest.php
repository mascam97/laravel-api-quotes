<?php

use Domain\Quotes\Factories\QuoteFactory;
use Domain\Quotes\Models\Quote;
use Domain\Quotes\States\Published;
use Domain\Users\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Testing\Fluent\AssertableJson;
use function Pest\Laravel\getJson;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertLessThan;

beforeEach(function () {
    $this->user = User::factory()->create();

    (new QuoteFactory)->setAmount(5)->withUser($this->user)->create();

    loginApi($this->user);
});

it('can index', function () {
    getJson(route('api.quotes.index'))
        ->assertOk()
        ->assertJsonStructure([
            'data' => [
                '*' => ['id', 'title', 'content', 'state', 'average_rating', 'excerpt', 'created_at', 'updated_at'], ],
        ]);
});

it('can filter by title', function () {
    /** @var Quote $quote */
    $quote = (new QuoteFactory)->withUser($this->user)->create([
        'title' => 'Hamlet',
    ]);

    getJson(route('api.quotes.index', ['filter[title]' => 'hamlet']))
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
                })->etc();
        });
});

it('can filter by content', function () {
    /** @var Quote $quote */
    $quote = (new QuoteFactory)->withUser($this->user)->create([
        'content' => 'Some text about something',
    ]);

    getJson(route('api.quotes.index', ['filter[content]' => 'Some text about something']))
        ->assertSuccessful()
        ->assertJson(function (AssertableJson $json) use ($quote) {
            $json->has('data', 1)
                ->has('data.0', function (AssertableJson $data) use ($quote) {
                    $data->where('id', $quote->getKey())
                        ->where('content', 'Some text about something')
                        ->whereAllType([
                            'id' => 'integer',
                            'content' => 'string',
                        ])->etc();
                })->etc();
        });
});

it('can filter by state', function () {
    /** @var Quote $quote */
    $quote = (new QuoteFactory)->withUser($this->user)->withState(Published::$name)->create();

    getJson(route('api.quotes.index', ['filter[state]' => 'published']))
        ->assertSuccessful()
        ->assertJson(function (AssertableJson $json) use ($quote) {
            $json->has('data', 1)
                ->has('data.0', function (AssertableJson $data) use ($quote) {
                    $data->where('id', $quote->getKey())
                        ->where('state', 'PUBLISHED')
                        ->whereAllType([
                            'id' => 'integer',
                            'state' => 'string',
                        ])
                        ->etc();
                })->etc();
        });
});

it('can sort by id', function () {
    $responseData = getJson(route('api.quotes.index', ['sort' => 'id']))
        ->json('data');

    assertLessThan($responseData[4]['id'], $responseData[0]['id']);

    $responseDataTwo = getJson(route('api.quotes.index', ['sort' => '-id']))
        ->json('data');

    assertLessThan($responseDataTwo[0]['id'], $responseDataTwo[4]['id']);
});

it('can sort by title', function () {
    (new QuoteFactory)->withUser($this->user)->create([
        'title' => '000',
    ]);

    $responseData = getJson(route('api.quotes.index', ['sort' => 'title']))
        ->json('data');

    assertEquals('000', $responseData[0]['title']);

    $responseDataTwo = getJson(route('api.quotes.index', ['sort' => '-title']))
        ->json('data');

    assertEquals('000', $responseDataTwo[5]['title']);
});

test('sql queries optimization test', function () {
    DB::enableQueryLog();
    getJson(route('api.quotes.index'))->assertOk();

    expect(formatQueries(DB::getQueryLog()))
        ->toHaveCount(2)
        ->sequence(
            fn ($query) => $query->toBe('select count(*) as aggregate from `quotes` where `user_id` = ?'),
            fn ($query) => $query->toBe('select `id`, `title`, `content`, `state`, `average_score`, `created_at`, `updated_at` from `quotes` where `user_id` = ? order by `created_at` asc limit 20 offset 0'),
        );

    DB::disableQueryLog();
});
