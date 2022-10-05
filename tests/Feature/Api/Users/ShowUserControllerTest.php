<?php

use Domain\Quotes\Factories\QuoteFactory;
use Domain\Users\Factories\UserFactory;
use Domain\Users\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();

    (new UserFactory)->setAmount(4)->create();
    (new QuoteFactory)->setAmount(3)->withUser($this->user)->create();

    $this->actingAs($this->user, 'sanctum');
});

test('quotes include', function () {
    $responseData = $this->json('GET', route('users.show', [
        'user' => $this->user->getKey(),
        'include' => 'quotes',
    ]))->json('data');

    $this->assertArrayNotHasKey('quotesCount', $responseData);
    $this->assertArrayHasKey('quotes', $responseData);
    $this->assertCount(3, $responseData['quotes']);
});

test('quotes count include', function () {
    $responseData = $this->json('GET', route('users.show', [
        'user' => $this->user->getKey(),
        'include' => 'quotesCount',
    ]))->json('data');

    $this->assertArrayNotHasKey('quotes', $responseData);
    $this->assertArrayHasKey('quotesCount', $responseData);
    $this->assertEquals(3, $responseData['quotesCount']);
});
