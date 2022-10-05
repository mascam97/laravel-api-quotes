<?php

use Domain\Quotes\Factories\QuoteFactory;
use Domain\Users\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->quote = (new QuoteFactory)->withUser($this->user)->create();  /* @phpstan-ignore-line */

    (new QuoteFactory)->setAmount(3)->withUser($this->user)->create();

    $this->actingAs($this->user, 'sanctum');
});

test('user include', function () {
    $responseData = $this->json('GET', route('quotes.show', [
        'quote' => $this->quote->getKey(),
        'include' => 'user',
    ]))->json('data');

    $this->assertArrayHasKey('user', $responseData);
    $this->assertEquals($this->user->getKey(), $responseData['user']['id']);
});
