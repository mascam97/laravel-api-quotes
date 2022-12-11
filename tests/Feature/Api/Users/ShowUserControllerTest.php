<?php

use Domain\Quotes\Factories\QuoteFactory;
use Domain\Users\Factories\UserFactory;
use Domain\Users\Models\User;
use function Pest\Laravel\getJson;
use function PHPUnit\Framework\assertArrayHasKey;
use function PHPUnit\Framework\assertArrayNotHasKey;
use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertEquals;

beforeEach(function () {
    $this->user = User::factory()->create();

    (new UserFactory)->setAmount(4)->create();
    (new QuoteFactory)->setAmount(3)->withUser($this->user)->create();

    login($this->user);
});

it('can include quotes', function () {
    $responseData = getJson(route('users.show', [
        'user' => $this->user->getKey(),
        'include' => 'quotes',
    ]))->json('data');

    assertArrayNotHasKey('quotesCount', $responseData);
    assertArrayHasKey('quotes', $responseData);
    assertCount(3, $responseData['quotes']);
});

it('can include quotes count', function () {
    $responseData = getJson(route('users.show', [
        'user' => $this->user->getKey(),
        'include' => 'quotesCount',
    ]))->json('data');

    assertArrayNotHasKey('quotes', $responseData);
    assertArrayHasKey('quotesCount', $responseData);
    assertEquals(3, $responseData['quotesCount']);
});
