<?php

use Domain\Quotes\Factories\QuoteFactory;
use Domain\Users\Factories\UserFactory;
use Domain\Users\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();

    (new UserFactory)->setAmount(4)->create();

    $this->actingAs($this->user, 'sanctum');
});

test('id filter', function () {
    /** @var User $newUser */
    $newUser = User::factory()->create();

    $responseData = $this->json('GET', route('users.index', ['filter[id]' => $newUser->id]))
        ->json('data');

    $this->assertCount(1, $responseData);
    $this->assertEquals($newUser->getKey(), $responseData[0]['id']);
});

test('name filter', function () {
    $newUser = User::factory()->create([
        'name' => 'Shakespeare',
    ]);

    $responseData = $this->json('GET', route('users.index', ['filter[name]' => 'shakespeare']))
        ->json('data');

    $this->assertCount(1, $responseData);
    $this->assertEquals($newUser->getKey(), $responseData[0]['id']);
});

test('quotes include', function () {
    $responseData = $this->json('GET', route('users.index', ['include' => 'quotes']))
        ->json('data');

    $this->assertCount(5, $responseData);
    $this->assertArrayHasKey('quotes', $responseData[0]);

    $newUser = User::factory()->create([
        'name' => 'User with quote',
    ]);

    /** @var User $quote */
    $quote = (new QuoteFactory)->withUser($newUser)->create();

    $responseDataTwo = $this->json('GET', route('users.index', [
        'filter[name]' => 'User with quote',
        'include' => 'quotes',
    ]))
        ->json('data');

    $this->assertCount(1, $responseDataTwo);
    $this->assertCount(1, $responseDataTwo[0]['quotes']);
    $this->assertEquals($quote->getKey(), $responseDataTwo[0]['quotes'][0]['id']);
});

test('id sort', function () {
    $responseData = $this->json('GET', route('users.index', ['sort' => 'id']))
        ->json('data');

    $this->assertEquals(1, $responseData[0]['id']);

    $responseDataTwo = $this->json('GET', route('users.index', ['sort' => '-id']))
        ->json('data');

    $this->assertEquals(5, $responseDataTwo[0]['id']);
});

test('name sort', function () {
    $this->user->name = 'AAA';
    $this->user->update();

    $responseData = $this->json('GET', route('users.index', ['sort' => 'name']))
        ->json('data');

    $this->assertEquals('AAA', $responseData[0]['name']);

    $responseDataTwo = $this->json('GET', route('users.index', ['sort' => '-name']))
        ->json('data');

    $this->assertEquals('AAA', $responseDataTwo[4]['name']);
});
