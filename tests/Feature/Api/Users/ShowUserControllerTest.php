<?php

use Domain\Quotes\Factories\QuoteFactory;
use Domain\Users\Factories\UserFactory;
use Domain\Users\Models\User;
use Illuminate\Support\Facades\DB;
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

it('can show', function () {
    getJson(route('users.show', ['user' => $this->user->id]))
        ->assertSee([$this->user->id, $this->user->name])
        ->assertOk();
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

test('sql queries optimization test', function () {
    DB::enableQueryLog();

    getJson(route('users.show', ['user' => $this->user->id]))->assertOk();

    expect(formatQueries(DB::getQueryLog()))
        ->toHaveCount(1)
        ->sequence(
            fn ($query) => $query->toBe('select `id`, `name`, `email`, `created_at` from `users` where `id` = ? limit 1'),
        );

    DB::disableQueryLog();
});
