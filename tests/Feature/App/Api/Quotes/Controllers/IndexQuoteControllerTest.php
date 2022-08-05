<?php

namespace Tests\Feature\App\Api\Quotes\Controllers;

use Domain\Quotes\Factories\QuoteFactory;
use Domain\Quotes\Models\Quote;
use Domain\Users\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();

    (new QuoteFactory)->setAmount(5)->withUser($this->user)->create();

    $this->actingAs($this->user, 'sanctum');
});

test('title filter', function () {
    /** @var Quote $quote */
    $quote = (new QuoteFactory)->withUser($this->user)->create([
        'title' => 'Hamlet',
    ]);

    $responseData = $this->json('GET', route('quotes.index', ['filter[title]' => 'hamlet']))
        ->json('data');

    $this->assertCount(1, $responseData);
    $this->assertEquals($quote->getKey(), $responseData[0]['id']);
});

test('content filter', function () {
    /** @var Quote $quote */
    $quote = (new QuoteFactory)->withUser($this->user)->create([
        'content' => 'Some text about something',
    ]);

    $responseData = $this->json('GET', route('quotes.index', ['filter[content]' => 'Some text about something']))
        ->json('data');

    $this->assertCount(1, $responseData);
    $this->assertEquals($quote->getKey(), $responseData[0]['id']);
});

test('user id filter', function () {
    /** @var User $newUser */
    $newUser = User::factory()->create();

    /** @var Quote $quote */
    $quote = (new QuoteFactory)->withUser($newUser)->create();

    $responseData = $this->json('GET', route('quotes.index', ['filter[user_id]' => $newUser->id]))
        ->json('data');

    $this->assertCount(1, $responseData);
    $this->assertEquals($quote->getKey(), $responseData[0]['id']);
});

test('user include', function () {
    $responseData = $this->json('GET', route('quotes.index', ['include' => 'user']))
        ->json('data');

    $this->assertCount(5, $responseData);
    $this->assertArrayHasKey('user', $responseData[0]);

    $newUser = User::factory()->create();

    /** @var Quote $quote */
    $quote = (new QuoteFactory)->withUser($newUser)->create([
        'title' => 'Some text about something',
    ]);

    $responseDataTwo = $this->json('GET', route('quotes.index', [
        'filter[title]' => 'Some text about something',
        'include' => 'user',
    ]))
        ->json('data');

    $this->assertCount(1, $responseDataTwo);
    $this->assertEquals($quote->getKey(), $responseDataTwo[0]['id']);
    $this->assertEquals($newUser->getKey(), $responseDataTwo[0]['user']['id']);
});

test('id sort', function () {
    $responseData = $this->json('GET', route('quotes.index', ['sort' => 'id']))
        ->json('data');

    $this->assertEquals(1, $responseData[0]['id']);

    $responseDataTwo = $this->json('GET', route('quotes.index', ['sort' => '-id']))
        ->json('data');

    $this->assertEquals(5, $responseDataTwo[0]['id']);
});

test('title sort', function () {
    (new QuoteFactory)->withUser($this->user)->create([
        'title' => 'AAA',
    ]);

    $responseData = $this->json('GET', route('quotes.index', ['sort' => 'title']))
        ->json('data');

    $this->assertEquals('AAA', $responseData[0]['title']);

    $responseDataTwo = $this->json('GET', route('quotes.index', ['sort' => '-title']))
        ->json('data');

    $this->assertEquals('AAA', $responseDataTwo[5]['title']);
});
