<?php

use Domain\Quotes\Factories\QuoteFactory;
use Domain\Quotes\Models\Quote;
use Domain\Users\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;
use function Pest\Laravel\getJson;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertLessThan;

beforeEach(function () {
    $this->user = User::factory()->create();

    (new QuoteFactory)->setAmount(5)->withUser($this->user)->create();

    login($this->user);
});

it('can filter by title', function () {
    /** @var Quote $quote */
    $quote = (new QuoteFactory)->withUser($this->user)->create([
        'title' => 'Hamlet',
    ]);

    getJson(route('quotes.index', ['filter[title]' => 'hamlet']))
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

    getJson(route('quotes.index', ['filter[content]' => 'Some text about something']))
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
    $quote = (new QuoteFactory)->withUser($newUser)->create();

    getJson(route('quotes.index', ['filter[user_id]' => $newUser->id]))
        ->assertJson(function (AssertableJson $json) use ($quote) {
            $json->has('data', 1)
                ->has('data.0', function (AssertableJson $data) use ($quote) {
                    $data->where('id', $quote->getKey())
                        ->whereAllType([
                            'id' => 'integer',
                        ])
                        ->etc();
                })->etc();
        });
});

it('can include user', function () {
    getJson(route('quotes.index', ['include' => 'user']))
        ->assertJson(function (AssertableJson $json) {
            $json->has('data', 5)
                ->has('data.0', function (AssertableJson $data) {
                    $data->has('user')
                        ->whereAllType([
                            'user' => 'array',
                        ])->etc();
                })->etc();
        });

    $newUser = User::factory()->create();

    /** @var Quote $quote */
    $quote = (new QuoteFactory)->withUser($newUser)->create([
        'title' => 'Some text about something',
    ]);

    getJson(route('quotes.index', [
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
    $responseData = getJson(route('quotes.index', ['sort' => 'id']))
        ->json('data');

    assertLessThan($responseData[4]['id'], $responseData[0]['id']);

    $responseDataTwo = getJson(route('quotes.index', ['sort' => '-id']))
        ->json('data');

    assertLessThan($responseDataTwo[0]['id'], $responseDataTwo[4]['id']);
});

it('can sort by title', function () {
    (new QuoteFactory)->withUser($this->user)->create([
        'title' => 'AAA',
    ]);

    $responseData = getJson(route('quotes.index', ['sort' => 'title']))
        ->json('data');

    assertEquals('AAA', $responseData[0]['title']);

    $responseDataTwo = getJson(route('quotes.index', ['sort' => '-title']))
        ->json('data');

    assertEquals('AAA', $responseDataTwo[5]['title']);
});