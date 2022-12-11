<?php

use Domain\Quotes\Factories\QuoteFactory;
use Domain\Users\Models\User;
use function Pest\Laravel\getJson;
use function PHPUnit\Framework\assertArrayHasKey;
use function PHPUnit\Framework\assertEquals;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->quote = (new QuoteFactory)->withUser($this->user)->create();  /* @phpstan-ignore-line */

    (new QuoteFactory)->setAmount(3)->withUser($this->user)->create();

    login($this->user);
});

it('can include user', function () {
    $responseData = getJson(route('quotes.show', [
        'quote' => $this->quote->getKey(),
        'include' => 'user',
    ]))->json('data');

    assertArrayHasKey('user', $responseData);
    assertEquals($this->user->getKey(), $responseData['user']['id']);
});
