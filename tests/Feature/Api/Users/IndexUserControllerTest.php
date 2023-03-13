<?php

use Domain\Quotes\Factories\QuoteFactory;
use Domain\Users\Factories\UserFactory;
use Domain\Users\Models\User;
use Illuminate\Support\Facades\DB;
use function Pest\Laravel\getJson;
use function PHPUnit\Framework\assertArrayHasKey;
use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertLessThan;

beforeEach(function () {
    $this->user = User::factory()->create();

    (new UserFactory)->setAmount(4)->create();

    login($this->user);
});

it('can index', function () {
    getJson(route('users.index'))
        ->assertJsonStructure([
            'data' => ['*' => ['id', 'name', 'email', 'created_at']],
        ])->assertOk();
});

it('can filter by id', function () {
    /** @var User $newUser */
    $newUser = User::factory()->create();

    $responseData = getJson(route('users.index', ['filter[id]' => $newUser->id]))
        ->json('data');

    assertCount(1, $responseData);
    assertEquals($newUser->getKey(), $responseData[0]['id']);
});

it('can filter by name', function () {
    $newUser = User::factory()->create([
        'name' => 'Shakespeare',
    ]);

    $responseData = getJson(route('users.index', ['filter[name]' => 'shakespeare']))
        ->json('data');

    assertCount(1, $responseData);
    assertEquals($newUser->getKey(), $responseData[0]['id']);
});

it('can include quotes', function () {
    $responseData = getJson(route('users.index', ['include' => 'quotes']))
        ->json('data');

    assertCount(5, $responseData);
    assertArrayHasKey('quotes', $responseData[0]);

    $newUser = User::factory()->create([
        'name' => 'User with quote',
    ]);

    /** @var User $quote */
    $quote = (new QuoteFactory)->withUser($newUser)->create();

    $responseDataTwo = getJson(route('users.index', [
        'filter[name]' => 'User with quote',
        'include' => 'quotes',
    ]))
        ->json('data');

    assertCount(1, $responseDataTwo);
    assertCount(1, $responseDataTwo[0]['quotes']);
    assertEquals($quote->getKey(), $responseDataTwo[0]['quotes'][0]['id']);
});

it('can sort by id', function () {
    $responseData = getJson(route('users.index', ['sort' => 'id']))
        ->json('data');

    assertLessThan($responseData[4]['id'], $responseData[0]['id']);

    $responseDataTwo = getJson(route('users.index', ['sort' => '-id']))
        ->json('data');

    assertLessThan($responseDataTwo[0]['id'], $responseDataTwo[4]['id']);
});

it('can sort by name', function () {
    $this->user->name = 'AAA';
    $this->user->update();

    $responseData = getJson(route('users.index', ['sort' => 'name']))
        ->json('data');

    assertEquals('AAA', $responseData[0]['name']);

    $responseDataTwo = getJson(route('users.index', ['sort' => '-name']))
        ->json('data');

    assertEquals('AAA', $responseDataTwo[4]['name']);
});

test('sql queries optimization test', function () {
    DB::enableQueryLog();
    getJson(route('users.index'))->assertOk();

    expect(formatQueries(DB::getQueryLog()))
        ->toHaveCount(2)
        ->sequence(
            fn ($query) => $query->toBe('select count(*) as aggregate from `users`'),
            fn ($query) => $query->toBe('select `id`, `name`, `email`, `created_at` from `users` limit 15 offset 0'),
        );

    DB::disableQueryLog();
});
